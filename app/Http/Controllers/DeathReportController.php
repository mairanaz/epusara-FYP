<?php

namespace App\Http\Controllers;

use App\Models\DeathReport;
use App\Models\Dependent;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DeathReportController extends Controller
{
    public function create()
    {
        $authUser = auth()->user();

        $isMainMember = !empty($authUser->linked_profile_id);
        $familyUserId = null;
        $mainProfile = null;
        $loggedDependent = null;

        if ($isMainMember) {
            $mainProfile = UserProfile::find($authUser->linked_profile_id);

            if (!$mainProfile) {
                return redirect()->route('home')
                    ->with('error', 'Profil ahli utama tidak dijumpai.');
            }

            $familyUserId = $mainProfile->user_id;
        } else {
            $loggedDependent = Dependent::find($authUser->linked_dependent_id);

            if (!$loggedDependent) {
                return redirect()->route('home')
                    ->with('error', 'Rekod tanggungan tidak dijumpai.');
            }

            $familyUserId = $loggedDependent->user_id;
            $mainProfile = UserProfile::where('user_id', $familyUserId)->first();

            if (!$mainProfile) {
                return redirect()->route('home')
                    ->with('error', 'Profil ahli utama keluarga ini tidak dijumpai.');
            }
        }

        $dependents = Dependent::where('user_id', $familyUserId)->get();

        $memberOptions = collect();
        $dependentOptions = collect();

        // Tanggungan login boleh lapor ahli utama
        if (!$isMainMember) {
            $memberOptions->push([
                'id' => $mainProfile->id,
                'name' => $mainProfile->nama,
                'no_kp' => $mainProfile->no_kp,
                'label' => 'Ahli Utama',
            ]);
        }

        // Ahli utama login -> semua tanggungan keluar
        // Tanggungan login -> semua tanggungan lain keluar kecuali dirinya sendiri
        $dependentOptions = $dependents
            ->filter(function ($dependent) use ($isMainMember, $loggedDependent) {
                if ($isMainMember) {
                    return true;
                }

                return !$loggedDependent || $dependent->id !== $loggedDependent->id;
            })
            ->map(function ($dependent) {
                return [
                    'id' => $dependent->id,
                    'name' => $dependent->name,
                    'no_kp' => $dependent->no_kp,
                    'label' => ucfirst($dependent->pertalian),
                ];
            })
            ->values();

        if ($isMainMember) {
            $pelaporNama = $mainProfile->nama ?? $authUser->name ?? '';
            $pelaporNoKp = $mainProfile->no_kp ?? '';
            $pelaporNoTel = $mainProfile->no_tel_bimbit ?? '';
            $pelaporPertalian = 'Ahli Utama';
        } else {
            $pelaporNama = $loggedDependent->name ?? $authUser->name ?? '';
            $pelaporNoKp = $loggedDependent->no_kp ?? '';
            $pelaporNoTel = $loggedDependent->no_tel ?? '';
            $pelaporPertalian = ucfirst($loggedDependent->pertalian ?? 'Tanggungan');
        }

        return view('death-reports.create', [
            'isMainMember' => $isMainMember,
            'memberOptions' => $memberOptions,
            'dependentOptions' => $dependentOptions,
            'pelaporNama' => $pelaporNama,
            'pelaporNoKp' => $pelaporNoKp,
            'pelaporNoTel' => $pelaporNoTel,
            'pelaporPertalian' => $pelaporPertalian,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'deceased_type' => ['required', 'in:member,dependent'],
            'deceased_id' => ['required', 'integer'],

            'alamat_terakhir' => ['required', 'string'],
            'tarikh_meninggal' => ['required', 'date', 'before_or_equal:today'],
            'no_permit_kebumi' => ['nullable', 'string', 'max:255'],

            'nama_pelapor' => ['required', 'string', 'max:255'],
            'no_kp_pelapor' => ['required', 'string', 'max:255'],
            'no_tel_pelapor' => ['required', 'string', 'max:255'],
            'pertalian_pelapor' => ['required', 'string', 'max:255'],

            'sijil_mati' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'permit_kebumi' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'dokumen_sokongan' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $authUser = auth()->user();

        $isMainMember = !empty($authUser->linked_profile_id);
        $familyUserId = null;
        $mainProfile = null;
        $loggedDependent = null;

        if ($isMainMember) {
            $mainProfile = UserProfile::find($authUser->linked_profile_id);

            if (!$mainProfile) {
                return back()->withErrors([
                    'deceased_id' => 'Profil ahli utama tidak dijumpai.'
                ])->withInput();
            }

            $familyUserId = $mainProfile->user_id;
        } else {
            $loggedDependent = Dependent::find($authUser->linked_dependent_id);

            if (!$loggedDependent) {
                return back()->withErrors([
                    'deceased_id' => 'Rekod tanggungan pelapor tidak dijumpai.'
                ])->withInput();
            }

            $familyUserId = $loggedDependent->user_id;
            $mainProfile = UserProfile::where('user_id', $familyUserId)->first();

            if (!$mainProfile) {
                return back()->withErrors([
                    'deceased_id' => 'Profil ahli utama keluarga ini tidak dijumpai.'
                ])->withInput();
            }
        }

        $data = [
            'deceased_type' => $validated['deceased_type'],
            'user_id' => null,
            'dependent_id' => null,
            'nama_si_mati' => null,
            'no_kp_si_mati' => null,
            'jantina' => null,
            'alamat_terakhir' => $validated['alamat_terakhir'],
            'tarikh_meninggal' => $validated['tarikh_meninggal'],
            'umur' => null,
            'no_permit_kebumi' => $validated['no_permit_kebumi'] ?? null,

            'nama_pelapor' => $validated['nama_pelapor'],
            'no_kp_pelapor' => $validated['no_kp_pelapor'],
            'no_tel_pelapor' => $validated['no_tel_pelapor'],
            'pertalian_pelapor' => $validated['pertalian_pelapor'],

            'status' => 'menunggu_semakan',
        ];

        if ($validated['deceased_type'] === 'member') {
            // Ahli utama tak boleh lapor dirinya sendiri
            if ($isMainMember) {
                return back()->withErrors([
                    'deceased_type' => 'Ahli utama tidak boleh membuat laporan kematian untuk dirinya sendiri.'
                ])->withInput();
            }

            if ((int) $validated['deceased_id'] !== (int) $mainProfile->id) {
                return back()->withErrors([
                    'deceased_id' => 'Pilihan ahli utama tidak sah.'
                ])->withInput();
            }

            $data['user_id'] = $familyUserId;
            $data['dependent_id'] = null;
            $data['nama_si_mati'] = $mainProfile->nama;
            $data['no_kp_si_mati'] = $mainProfile->no_kp;
            $data['jantina'] = $this->getGenderFromIc($mainProfile->no_kp) ?? $mainProfile->jantina;
            $data['umur'] = $this->getAgeFromIc($mainProfile->no_kp);
        } else {
            $dependentQuery = Dependent::where('user_id', $familyUserId)
                ->where('id', $validated['deceased_id']);

            // Tanggungan login tak boleh lapor dirinya sendiri
            if (!$isMainMember && $loggedDependent) {
                $dependentQuery->where('id', '!=', $loggedDependent->id);
            }

            $dependent = $dependentQuery->first();

            if (!$dependent) {
                return back()->withErrors([
                    'deceased_id' => 'Pilihan tanggungan tidak sah.'
                ])->withInput();
            }

            $data['user_id'] = $familyUserId;
            $data['dependent_id'] = $dependent->id;
            $data['nama_si_mati'] = $dependent->name;
            $data['no_kp_si_mati'] = $dependent->no_kp;
            $data['jantina'] = $this->getGenderFromIc($dependent->no_kp);
            $data['umur'] = $this->getAgeFromIc($dependent->no_kp);
        }

        if (empty($data['nama_si_mati']) || empty($data['no_kp_si_mati'])) {
            return back()->withErrors([
                'deceased_id' => 'Maklumat si mati tidak lengkap dalam rekod.'
            ])->withInput();
        }

        $existingReport = DeathReport::where('no_kp_si_mati', $data['no_kp_si_mati'])
            ->whereIn('status', ['menunggu_semakan', 'disemak', 'approved'])
            ->first();

        if ($existingReport) {
            return back()->withErrors([
                'deceased_id' => 'Laporan kematian untuk individu ini telah wujud dan sedang diproses.'
            ])->withInput();
        }

        if ($request->hasFile('sijil_mati')) {
            $data['sijil_mati_path'] = $request->file('sijil_mati')->store('death-reports', 'public');
        }

        if ($request->hasFile('permit_kebumi')) {
            $data['permit_kebumi_path'] = $request->file('permit_kebumi')->store('death-reports', 'public');
        }

        if ($request->hasFile('dokumen_sokongan')) {
            $data['dokumen_sokongan_path'] = $request->file('dokumen_sokongan')->store('death-reports', 'public');
        }

        DeathReport::create($data);

        return redirect()->route('death-report.create')
            ->with('success', 'Laporan kematian berjaya dihantar dan sedang menunggu semakan pentadbir.');
    }

    private function getGenderFromIc(?string $ic): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $ic);

        if (strlen($digits) < 1) {
            return null;
        }

        $lastDigit = (int) substr($digits, -1);

        return $lastDigit % 2 === 0 ? 'Perempuan' : 'Lelaki';
    }

    private function getAgeFromIc(?string $ic): ?int
    {
        $digits = preg_replace('/\D/', '', (string) $ic);

        if (strlen($digits) < 6) {
            return null;
        }

        $yy = (int) substr($digits, 0, 2);
        $mm = (int) substr($digits, 2, 2);
        $dd = (int) substr($digits, 4, 2);

        if ($mm < 1 || $mm > 12 || $dd < 1 || $dd > 31) {
            return null;
        }

        $currentYearTwoDigits = (int) now()->format('y');
        $century = $yy <= $currentYearTwoDigits ? 2000 : 1900;
        $year = $century + $yy;

        try {
            $birthDate = Carbon::createFromDate($year, $mm, $dd);
            return $birthDate->age;
        } catch (\Exception $e) {
            return null;
        }
    }
}