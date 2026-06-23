<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dependent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\SuccessorFinalizationService;

class MainMemberSuccessorController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Papar senarai calon yang layak jadi Ahli Utama Baharu
    |--------------------------------------------------------------------------
    */
    public function show(User $user)
    {
        $oldMainUser = $user->load('profile', 'dependents');

        $statusKehidupan = strtolower($oldMainUser->profile->status_kehidupan ?? 'aktif');

        $isDeceased = in_array($statusKehidupan, [
            'meninggal',
            'meninggal dunia',
            'meninggal_dunia',
        ]);

        if (!$isDeceased) {
            return redirect()
                ->route('admin.khairat.members.index')
                ->with('error', 'Pengganti hanya boleh dipilih untuk Ahli Utama yang telah meninggal dunia.');
        }

        /*
        |--------------------------------------------------------------------------
        | Senarai semua tanggungan keluarga untuk paparan validation
        |--------------------------------------------------------------------------
        */
        $allDependents = Dependent::where('user_id', $oldMainUser->id)
            ->orderByRaw("
                CASE
                    WHEN pertalian = 'isteri' THEN 1
                    WHEN pertalian = 'suami' THEN 1
                    WHEN pertalian = 'anak' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('name')
            ->get()
            ->map(function ($dependent) {
                $dependent->umur_dari_ic = $this->getAgeFromIc($dependent->no_kp);

                $dependent->is_child_successor_eligible = $this->isEligibleChildSuccessor($dependent);
                $dependent->is_spouse_successor_eligible = $this->isEligibleSpouseSuccessor($dependent);

                return $dependent;
            });

        /*
        |--------------------------------------------------------------------------
        | 1. Cari anak yang layak dahulu
        |--------------------------------------------------------------------------
        */
        $children = Dependent::with('linkedUser')
            ->where('user_id', $oldMainUser->id)
            ->where('pertalian', 'anak')
            ->where('status_tanggungan', 'aktif')
            ->where('status_kehidupan', 'aktif')
            ->where('status_perkahwinan', 'bujang')
            ->whereNull('promoted_user_id')
            ->get();

        $eligibleChildren = $children->filter(function ($child) {
            return $this->isEligibleChildSuccessor($child);
        })->map(function ($child) {
            $child->umur_dari_ic = $this->getAgeFromIc($child->no_kp);
            $child->successor_type = 'anak';
            return $child;
        });

        /*
        |--------------------------------------------------------------------------
        | 2. Jika tiada anak layak, cari pasangan sebagai fallback
        |--------------------------------------------------------------------------
        */
        $spouses = collect();

        if ($eligibleChildren->count() === 0) {
            $spouses = Dependent::with('linkedUser')
                ->where('user_id', $oldMainUser->id)
                ->whereIn('pertalian', ['isteri', 'suami'])
                ->where('status_tanggungan', 'aktif')
                ->where('status_kehidupan', 'aktif')
                ->whereNull('promoted_user_id')
                ->get();

            $spouses = $spouses->filter(function ($spouse) {
                return $this->isEligibleSpouseSuccessor($spouse);
            })->map(function ($spouse) {
                $spouse->umur_dari_ic = $this->getAgeFromIc($spouse->no_kp);
                $spouse->successor_type = 'pasangan';
                return $spouse;
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Senarai calon akhir
        |--------------------------------------------------------------------------
        */
        $eligibleSuccessors = $eligibleChildren->count() > 0
            ? $eligibleChildren
            : $spouses;

        return view('admin.members.successor', compact(
            'oldMainUser',
            'allDependents',
            'eligibleChildren',
            'spouses',
            'eligibleSuccessors'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Admin sahkan calon pengganti
    |--------------------------------------------------------------------------
    | Flow baru:
    | - Tidak create user
    | - Tidak create profile
    | - Tidak pindah tanggungan lagi
    | - Hanya simpan calon sebagai pending_registration
    |--------------------------------------------------------------------------
    */
    public function store(Request $request, User $user, SuccessorFinalizationService $successorService)
    {
        $request->validate([
            'dependent_id' => ['required', 'exists:dependents,id'],
        ]);

        $oldMainUser = $user->load('profile');

        if (!$oldMainUser->profile) {
            return back()->with('error', 'Profil Ahli Utama tidak dijumpai.');
        }

        $statusKehidupan = strtolower($oldMainUser->profile->status_kehidupan ?? 'aktif');

        $isDeceased = in_array($statusKehidupan, [
            'meninggal',
            'meninggal dunia',
            'meninggal_dunia',
        ]);

        if (!$isDeceased) {
            return back()->with('error', 'Ahli Utama ini masih aktif. Pengganti tidak boleh dipilih.');
        }

        if (!empty($oldMainUser->profile->replaced_by_user_id)) {
            return back()->with('error', 'Ahli Utama ini telah mempunyai pengganti.');
        }

        if (($oldMainUser->profile->replacement_status ?? null) === 'pending_registration') {
            return back()->with('error', 'Ahli Utama ini sudah mempunyai calon pengganti yang sedang menunggu pendaftaran akaun.');
        }

        $selectedCandidate = Dependent::where('id', $request->dependent_id)
            ->where('user_id', $oldMainUser->id)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Semak kelayakan calon
        |--------------------------------------------------------------------------
        */
        $isEligibleChild = $this->isEligibleChildSuccessor($selectedCandidate);
        $isEligibleSpouse = false;

        if (!$isEligibleChild) {
            $eligibleChildrenCount = Dependent::where('user_id', $oldMainUser->id)
                ->where('pertalian', 'anak')
                ->where('status_tanggungan', 'aktif')
                ->where('status_kehidupan', 'aktif')
                ->where('status_perkahwinan', 'bujang')
                ->whereNull('promoted_user_id')
                ->get()
                ->filter(function ($child) {
                    return $this->isEligibleChildSuccessor($child);
                })
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Pasangan hanya boleh dipilih jika tiada anak layak
            |--------------------------------------------------------------------------
            */
            if ($eligibleChildrenCount === 0) {
                $isEligibleSpouse = $this->isEligibleSpouseSuccessor($selectedCandidate);
            }
        }

        if (!$isEligibleChild && !$isEligibleSpouse) {
            return back()->with('error', 'Calon yang dipilih tidak memenuhi syarat sebagai Ahli Utama Baharu.');
        }

        /*
        |--------------------------------------------------------------------------
        | Semak sama ada calon sudah ada akaun
        |--------------------------------------------------------------------------
        */
        $linkedUser = User::where('linked_dependent_id', $selectedCandidate->id)->first();

        if ($linkedUser) {
            $accountType = strtolower((string) $linkedUser->account_type);

            /*
            |--------------------------------------------------------------------------
            | Kalau calon sudah ahli utama, tidak boleh pilih
            |--------------------------------------------------------------------------
            */
            if (in_array($accountType, ['utama', 'main_member'])) {
                return back()->with('error', 'Calon ini sudah mempunyai akaun Ahli Utama.');
            }

            /*
            |--------------------------------------------------------------------------
            | Kalau calon ada akaun tanggungan, terus upgrade jadi Ahli Utama
            |--------------------------------------------------------------------------
            */
            $successorService->finalizeExistingDependentAccount(
                $linkedUser,
                $selectedCandidate,
                $oldMainUser
            );

            return redirect()
                ->route('admin.khairat.members.index')
                ->with('success', 'Akaun tanggungan calon telah dinaik taraf sebagai Ahli Utama Baharu. Rekod keluarga dan yuran telah dipindahkan.');
        }

        /*
        |--------------------------------------------------------------------------
        | Jika calon belum ada akaun, simpan sebagai pending registration
        |--------------------------------------------------------------------------
        */
        DB::transaction(function () use ($oldMainUser, $selectedCandidate, $isEligibleChild) {
            $oldProfile = $oldMainUser->profile;

            $reason = $isEligibleChild
                ? 'Calon pengganti anak telah disahkan oleh admin dan menunggu pendaftaran akaun.'
                : 'Calon pengganti pasangan telah disahkan oleh admin kerana tiada anak yang layak dan menunggu pendaftaran akaun.';

            $oldProfile->update([
                'status_kehidupan' => 'meninggal_dunia',
                'replacement_dependent_id' => $selectedCandidate->id,
                'replacement_status' => 'pending_registration',
                'replacement_reason' => $reason,
            ]);

            $selectedCandidate->update([
                'promoted_at' => now(),
                'sebab_tidak_layak' => 'Telah disahkan sebagai calon Ahli Utama Baharu dan menunggu pendaftaran akaun.',
            ]);
        });

        return redirect()
            ->route('admin.khairat.members.index')
            ->with('success', 'Calon pengganti telah disahkan. Calon perlu mendaftar akaun untuk melengkapkan proses penggantian.');
    }

    /*
    |--------------------------------------------------------------------------
    | Semak anak layak jadi pengganti
    |--------------------------------------------------------------------------
    */
    private function isEligibleChildSuccessor(Dependent $child): bool
    {
        $linkedUser = User::where('linked_dependent_id', $child->id)->first();

        $alreadyMainMember = $linkedUser
            && in_array(strtolower((string) $linkedUser->account_type), ['utama', 'main_member']);

        return strtolower(trim($child->pertalian ?? '')) === 'anak'
            && strtolower(trim($child->status_tanggungan ?? '')) === 'aktif'
            && strtolower(trim($child->status_kehidupan ?? '')) === 'aktif'
            && strtolower(trim($child->status_perkahwinan ?? '')) === 'bujang'
            && is_null($child->promoted_user_id)
            && !$alreadyMainMember
            && $this->isAtLeast18FromIc($child->no_kp);
    }

    /*
    |--------------------------------------------------------------------------
    | Semak pasangan layak jadi pengganti
    |--------------------------------------------------------------------------
    */
    private function isEligibleSpouseSuccessor(Dependent $spouse): bool
    {
        $linkedUser = User::where('linked_dependent_id', $spouse->id)->first();

        $alreadyMainMember = $linkedUser
            && in_array(strtolower((string) $linkedUser->account_type), ['utama', 'main_member']);

        return in_array(strtolower(trim($spouse->pertalian ?? '')), ['isteri', 'suami'])
            && strtolower(trim($spouse->status_tanggungan ?? '')) === 'aktif'
            && strtolower(trim($spouse->status_kehidupan ?? '')) === 'aktif'
            && is_null($spouse->promoted_user_id)
            && !$alreadyMainMember;
    }

    /*
    |--------------------------------------------------------------------------
    | Semak umur 18 tahun ke atas daripada IC
    |--------------------------------------------------------------------------
    */
    private function isAtLeast18FromIc(?string $ic): bool
    {
        $age = $this->getAgeFromIc($ic);

        return !is_null($age) && $age >= 18;
    }

    /*
    |--------------------------------------------------------------------------
    | Kira umur daripada IC
    |--------------------------------------------------------------------------
    */
    private function getAgeFromIc(?string $ic): ?int
    {
        $birthDate = $this->getBirthDateFromIc($ic);

        if (!$birthDate) {
            return null;
        }

        return Carbon::parse($birthDate)->age;
    }

    /*
    |--------------------------------------------------------------------------
    | Dapatkan tarikh lahir daripada IC
    |--------------------------------------------------------------------------
    */
    private function getBirthDateFromIc(?string $ic): ?string
    {
        if (!$ic) {
            return null;
        }

        $cleanIc = preg_replace('/[^0-9]/', '', $ic);

        if (strlen($cleanIc) < 6) {
            return null;
        }

        $yy = (int) substr($cleanIc, 0, 2);
        $mm = (int) substr($cleanIc, 2, 2);
        $dd = (int) substr($cleanIc, 4, 2);

        $currentYearTwoDigit = (int) now()->format('y');

        $fullYear = $yy <= $currentYearTwoDigit
            ? 2000 + $yy
            : 1900 + $yy;

        if (!checkdate($mm, $dd, $fullYear)) {
            return null;
        }

        return Carbon::createFromDate($fullYear, $mm, $dd)->toDateString();
    }
}