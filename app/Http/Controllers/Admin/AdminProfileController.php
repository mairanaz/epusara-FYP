<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class AdminProfileController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Statistik Semua Permohonan
        |--------------------------------------------------------------------------
        | Statistik ini tidak berubah walaupun admin buat carian / filter.
        */
        $baseQuery = UserProfile::query();

        $totalApplications = (clone $baseQuery)->count();

        $pendingCount = (clone $baseQuery)
            ->where('status_permohonan', 'pending')
            ->count();

        $approvedCount = (clone $baseQuery)
            ->whereIn('status_permohonan', ['approved', 'active'])
            ->count();

        $rejectedCount = (clone $baseQuery)
            ->where('status_permohonan', 'rejected')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Senarai Permohonan + Carian / Filter
        |--------------------------------------------------------------------------
        | Tambahan:
        | - is_dependent_profile: semak sama ada rekod ini ialah tanggungan.
        | - dependent_relation: papar pertalian seperti anak, isteri, bapa.
        */
        $query = UserProfile::query()
            ->select('user_profiles.*')
            ->selectRaw("
                EXISTS (
                    SELECT 1
                    FROM dependents
                    WHERE dependents.no_kp = user_profiles.no_kp
                ) as is_dependent_profile
            ")
            ->selectRaw("
                (
                    SELECT dependents.pertalian
                    FROM dependents
                    WHERE dependents.no_kp = user_profiles.no_kp
                    LIMIT 1
                ) as dependent_relation
            ");

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('no_kp', 'like', "%{$search}%")
                    ->orWhere('no_tel_bimbit', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->whereIn('status_permohonan', ['approved', 'active']);
            } else {
                $query->where('status_permohonan', $request->status);
            }
        }

        $profiles = $query
            ->latest('user_profiles.created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.profile.index', compact(
            'profiles',
            'totalApplications',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    public function show(UserProfile $profile)
    {
        $statusClass = $this->getStatusClass($profile->status_permohonan);
        $statusLabel = $this->getStatusLabel($profile->status_permohonan);

        $hasPaidPayment = Payment::where('user_id', $profile->user_id)
            ->where('status', 'paid')
            ->exists();

        return view('admin.profile.show', compact(
            'profile',
            'statusClass',
            'statusLabel',
            'hasPaidPayment'
        ));
    }

  public function approve(UserProfile $profile)
    {
        if (in_array($profile->status_permohonan, ['approved', 'active'])) {
            return redirect()->route('admin.profile.show', $profile)
                ->with('error', 'Permohonan ini sudah diluluskan.');
        }

        if ($profile->status_permohonan === 'rejected') {
            return redirect()->route('admin.profile.show', $profile)
                ->with('error', 'Permohonan yang telah ditolak tidak boleh terus diluluskan.');
        }

        if ($profile->status_permohonan !== 'pending') {
            return redirect()->route('admin.profile.show', $profile)
                ->with('error', 'Hanya permohonan berstatus menunggu boleh diluluskan.');
        }

        /*
        |--------------------------------------------------------------------------
        | Update status permohonan ahli
        |--------------------------------------------------------------------------
        */
        $profile->update([
            'status_permohonan' => 'approved',
            'status_kehidupan' => 'hidup',
            'catatan_permohonan' => 'Permohonan telah diluluskan oleh pentadbir.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Jika profile ini ada user login, update akaun user
        |--------------------------------------------------------------------------
        | Ini penting untuk kes naik taraf:
        | tanggungan -> ahli utama
        |--------------------------------------------------------------------------
        */
        $user = $profile->user;

        if ($user) {
            $user->update([
                'account_type' => 'utama',
                'linked_profile_id' => $profile->id,
                'linked_dependent_id' => null,
            ]);
        }

        return redirect()->route('admin.profile.show', $profile)
            ->with('success', 'Permohonan berjaya diluluskan dan akaun pengguna telah dikemaskini sebagai ahli utama.');
    }

    public function reject(Request $request, UserProfile $profile)
    {
        $request->validate([
            'catatan_permohonan' => ['required', 'string', 'max:1000'],
        ], [
            'catatan_permohonan.required' => 'Sila isi sebab penolakan.',
            'catatan_permohonan.max' => 'Catatan penolakan tidak boleh melebihi 1000 aksara.',
        ]);

        if (in_array($profile->status_permohonan, ['approved', 'active'])) {
            return redirect()->route('admin.profile.show', $profile)
                ->with('error', 'Permohonan yang sudah diluluskan tidak boleh terus ditolak.');
        }

        if ($profile->status_permohonan !== 'pending') {
            return redirect()->route('admin.profile.show', $profile)
                ->with('error', 'Hanya permohonan berstatus menunggu boleh ditolak.');
        }

        $profile->update([
            'status_permohonan' => 'rejected',
            'catatan_permohonan' => $request->catatan_permohonan,
        ]);

        return redirect()->route('admin.profile.show', $profile)
            ->with('success', 'Permohonan telah ditolak.');
    }

    protected function getStatusClass(?string $status): string
    {
        return match ($status) {
            'pending' => 'warning',
            'approved', 'active' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    protected function getStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Menunggu',
            'approved', 'active' => 'Diluluskan',
            'rejected' => 'Ditolak',
            default => 'Belum Dihantar',
        };
    }
}