<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\UserProfile;
use App\Models\Dependent;
use App\Models\DeathReport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    public function show()
    {
    $profile = auth()->user()->profile;

    if (!$profile) {
        return redirect()->route('user.profile.create.step1');
    }

    $statusClass = $this->getStatusClass($profile->status_permohonan);
    $paymentPlanLabel = $profile->payment_plan
        ? $this->getPaymentPlanLabel($profile->payment_plan)
        : 'Tidak berkenaan';

    return view('user.profile.show', compact(
        'profile',
        'statusClass',
        'paymentPlanLabel'
    ));
}

    // =========================
    // MULTI STEP CREATE
    // =========================

    public function createStep1()
    {
        if (auth()->user()->profile) {
            return redirect()->route('user.profile.show');
        }

        session()->forget('user_profile');

        return view('user.profile.step1');
    }

    public function postStep1(Request $request)
    {
        if (auth()->user()->profile) {
            return redirect()->route('user.profile.show');
        }

        $validated = $request->validate(
            $this->step1Rules(),
            $this->messages()
        );

        // Paksa nama ikut nama akaun yang didaftarkan
        $validated['nama'] = auth()->user()->name;

        session(['user_profile.step1' => $validated]);

        $matchedDependent = Dependent::where('no_kp', $validated['no_kp'])->first();

        if ($matchedDependent) {
            session([
                'user_profile.is_dependent' => true,
                'user_profile.linked_dependent_id' => $matchedDependent->id,
            ]);
        } else {
            session()->forget([
                'user_profile.is_dependent',
                'user_profile.linked_dependent_id',
            ]);
        }

        return redirect()->route('user.profile.create.step2');
    }

    public function createStep2()
    {
        if (auth()->user()->profile) {
            return redirect()->route('user.profile.show');
        }

        if (!session()->has('user_profile.step1')) {
            return redirect()->route('user.profile.create.step1')
                ->with('error', 'Sila lengkapkan Maklumat Asas terlebih dahulu.');
        }

        return view('user.profile.step2');
    }

    public function postStep2(Request $request)
    {
        if (auth()->user()->profile) {
            return redirect()->route('user.profile.show');
        }

        if (!session()->has('user_profile.step1')) {
            return redirect()->route('user.profile.create.step1')
                ->with('error', 'Sila lengkapkan Maklumat Asas terlebih dahulu.');
        }

        $validated = $request->validate(
            $this->step2Rules(),
            $this->messages()
        );

        session(['user_profile.step2' => $validated]);

        if ($this->isDependentFlow()) {
            $step1 = session('user_profile.step1', []);
            $step2 = session('user_profile.step2', []);

            $data = array_merge($step1, $step2);

            $data['user_id'] = auth()->id();
            $data['tarikh_permohonan'] = now()->toDateString();
            $data['status_permohonan'] = 'pending';
            $data['catatan_permohonan'] = 'Akaun didaftarkan sebagai tanggungan.';
            $data['nama_waris'] = null;
            $data['hubungan_waris'] = null;
            $data['no_tel_waris'] = null;
            $data['alamat_waris'] = null;
            $data['payment_plan'] = null;

            $profile = UserProfile::create($data);

            $this->syncAccountTypeByNoKp($profile->no_kp);

            session()->forget('user_profile');

            return redirect()->route('user.profile.show')
                ->with('success', 'Maklumat profil tanggungan berjaya disimpan.');
        }

        return redirect()->route('user.profile.create.step3');
    }

   public function createStep3()
    {
        if (auth()->user()->profile) {
            return redirect()->route('user.profile.show');
        }

        if (!session()->has('user_profile.step1')) {
            return redirect()->route('user.profile.create.step1')
                ->with('error', 'Sila lengkapkan Maklumat Asas terlebih dahulu.');
        }

        if (!session()->has('user_profile.step2')) {
            return redirect()->route('user.profile.create.step2')
                ->with('error', 'Sila lengkapkan Maklumat Perhubungan terlebih dahulu.');
        }

        if ($this->isDependentFlow()) {
            return redirect()->route('user.profile.show')
                ->with('info', 'Akaun anda dikenal pasti sebagai tanggungan. Langkah seterusnya tidak diperlukan.');
        }

        return view('user.profile.step3');
    }

    public function postStep3(Request $request)
    {
        if (auth()->user()->profile) {
            return redirect()->route('user.profile.show');
        }

        if ($this->isDependentFlow()) {
            return redirect()->route('user.profile.show')
                ->with('error', 'Akaun tanggungan tidak perlu melengkapkan langkah ini.');
        }

        if (!session()->has('user_profile.step1')) {
            return redirect()->route('user.profile.create.step1')
                ->with('error', 'Sila lengkapkan Maklumat Asas terlebih dahulu.');
        }

        if (!session()->has('user_profile.step2')) {
            return redirect()->route('user.profile.create.step2')
                ->with('error', 'Sila lengkapkan Maklumat Perhubungan terlebih dahulu.');
        }

        $validated = $request->validate(
            $this->step3Rules(),
            $this->messages()
        );

        session(['user_profile.step3' => $validated]);

        return redirect()->route('user.profile.create.step4');
    }

    public function createStep4()
    {
        if (auth()->user()->profile) {
            return redirect()->route('user.profile.show');
        }

        if ($this->isDependentFlow()) {
            return redirect()->route('user.profile.show')
                ->with('error', 'Akaun tanggungan tidak perlu melengkapkan langkah ini.');
        }

        if (!session()->has('user_profile.step1')) {
            return redirect()->route('user.profile.create.step1')
                ->with('error', 'Sila lengkapkan Maklumat Asas terlebih dahulu.');
        }

        if (!session()->has('user_profile.step2')) {
            return redirect()->route('user.profile.create.step2')
                ->with('error', 'Sila lengkapkan Maklumat Perhubungan terlebih dahulu.');
        }

        if (!session()->has('user_profile.step3')) {
            return redirect()->route('user.profile.create.step3')
                ->with('error', 'Sila lengkapkan Maklumat Waris terlebih dahulu.');
        }

        return view('user.profile.step4');
    }

    public function storeFinal(Request $request)
    {
        if (auth()->user()->profile) {
            return redirect()->route('user.profile.show');
        }

        if (!session()->has('user_profile.step1')) {
            return redirect()->route('user.profile.create.step1')
                ->with('error', 'Sila lengkapkan Maklumat Asas terlebih dahulu.');
        }

        if (!session()->has('user_profile.step2')) {
            return redirect()->route('user.profile.create.step2')
                ->with('error', 'Sila lengkapkan Maklumat Perhubungan terlebih dahulu.');
        }

        if (!session()->has('user_profile.step3')) {
            return redirect()->route('user.profile.create.step3')
               ->with('error', 'Sila lengkapkan Maklumat Waris terlebih dahulu.');
        }

        $validated = $request->validate(
            $this->step4Rules(),
            $this->messages()
        );

        session(['user_profile.step4' => $validated]);

        $step1 = session('user_profile.step1', []);
        $step2 = session('user_profile.step2', []);
        $step3 = session('user_profile.step3', []);
        $step4 = session('user_profile.step4', []);

        $data = array_merge($step1, $step2, $step3, $step4);

        $data['user_id'] = auth()->id();
        $data['tarikh_permohonan'] = now()->toDateString();
        $data['status_permohonan'] = 'pending';
        $data['catatan_permohonan'] = null;

        $profile = UserProfile::create($data);

        session()->forget('user_profile');

        $this->syncAccountTypeByNoKp($profile->no_kp);

        return redirect()->route('user.profile.show')
            ->with('success', 'Maklumat profil berjaya disimpan.');
    }

    // =========================
    // EDIT / UPDATE
    // =========================

    public function edit()
    {
        $profile = auth()->user()->profile;

        if (!$profile) {
            return redirect()->route('user.profile.create.step1');
        }

        $hasPaidPayments = Payment::where('user_id', auth()->id())
            ->where('status', 'paid')
            ->exists();

        return view('user.profile.edit', compact('profile', 'hasPaidPayments'));
    }

   public function update(Request $request)
    {
        $profile = auth()->user()->profile;

        if (!$profile) {
            return redirect()->route('user.profile.create.step1');
        }

        $hasPaidPayments = Payment::where('user_id', auth()->id())
            ->where('status', 'paid')
            ->exists();

        $isDependent = auth()->user()->account_type === 'tanggungan' || is_null($profile->payment_plan);
        

        $rules = $isDependent
            ? $this->dependentProfileRules($profile->id)
            : $this->fullProfileRules($profile->id, $hasPaidPayments);

        $data = $request->validate($rules, $this->messages());

        $data['nama'] = auth()->user()->name;

        if (!$isDependent && $hasPaidPayments) {
            $data['payment_plan'] = $profile->payment_plan;
        }

        if (in_array($profile->status_permohonan, ['approved', 'rejected', 'active'])) {
            unset($data['status_permohonan']);
        }

        $profile->update($data);

        $this->syncAccountTypeByNoKp($profile->fresh()->no_kp);

        return redirect()->route('user.profile.show')
            ->with('success', 'Maklumat profil berjaya dikemaskini.');
    }


    // =========================
    // VALIDATION RULES
    // =========================

    protected function step1Rules(?int $profileId = null): array
    {
        return [
            'nama' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z@\'\.\-\/\s]+$/u'],
            'no_kp' => [
                'required',
                'digits:12',
                Rule::unique('user_profiles', 'no_kp')->ignore($profileId),
            ],
            'tarikh_lahir' => ['required', 'date', 'before:today'],
            'jantina' => ['required', Rule::in(['lelaki', 'perempuan'])],
            'agama' => ['required', Rule::in(['Islam'])],
            'warganegara' => ['required', Rule::in(['Malaysia', 'Penduduk Tetap'])],
        ];
    }

    protected function step2Rules(): array
    {
        return [
            'alamat_rumah' => ['required', 'string', 'max:1000'],
            'no_tel_rumah' => ['nullable', 'regex:/^(0\d{1,2}-?\d{6,8})$/'],
            'no_tel_bimbit' => ['required', 'regex:/^(01[0-9]-?\d{7,8})$/'],
            'tinggal_dalam_kariah' => ['required', 'in:1'],
            'tempoh_menetap' => ['required', 'string', 'max:100'],
            'pekerjaan' => 'nullable|string|max:255',
            'nama_majikan' => 'nullable|string|max:255',
            'alamat_kerja' => 'nullable|string|max:255',
        ];
    }

    protected function step3Rules(): array
    {
        return [
            'nama_waris' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z@\'\.\-\/\s]+$/u'],
            'hubungan_waris' => ['required', Rule::in([
                'Suami',
                'Isteri',
                'Anak',
                'Ibu',
                'Bapa',
                'Ibu Mertua',
                'Bapa Mertua'
            ])],
            'no_tel_waris' => ['required', 'regex:/^(01[0-9]-?\d{7,8})$/'],
            'alamat_waris' => ['required', 'string', 'max:1000'],
        ];
    }

    protected function step4Rules(): array
    {
        return [
            'payment_plan' => ['required', Rule::in(['bulanan', 'tahunan'])],
            'akuan' => ['required', 'accepted'],
        ];
    }

    protected function dependentProfileRules(?int $profileId = null): array
    {
        return [
            'nama' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z@\'\.\-\/\s]+$/u'],
            'no_kp' => [
                'required',
                'digits:12',
                Rule::unique('user_profiles', 'no_kp')->ignore($profileId),
            ],
            'tarikh_lahir' => ['required', 'date', 'before:today'],
            'jantina' => ['required', Rule::in(['lelaki', 'perempuan'])],
            'agama' => ['required', Rule::in(['Islam'])],
            'warganegara' => ['required', Rule::in(['Malaysia', 'Penduduk Tetap'])],

            'alamat_rumah' => ['required', 'string', 'max:1000'],
            'no_tel_rumah' => ['nullable', 'regex:/^(0\d{1,2}-?\d{6,8})$/'],
            'no_tel_bimbit' => ['required', 'regex:/^(01[0-9]-?\d{7,8})$/'],
            'tinggal_dalam_kariah' => ['required', 'in:1'],
            'tempoh_menetap' => ['required', 'string', 'max:100'],

            'pekerjaan' => ['nullable', 'string', 'max:255'],
            'nama_majikan' => ['nullable', 'string', 'max:255'],
            'alamat_kerja' => ['nullable', 'string', 'max:1000'],

            'akuan' => ['required', 'accepted'],
        ];
    }

    protected function fullProfileRules(?int $profileId = null, bool $hasPaidPayments = false): array
    {
        $paymentPlanRule = $hasPaidPayments
            ? ['nullable', Rule::in(['bulanan', 'tahunan'])]
            : ['required', Rule::in(['bulanan', 'tahunan'])];

        return [
            'nama' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z@\'\.\-\/\s]+$/u'],
            'no_kp' => [
                'required',
                'digits:12',
                Rule::unique('user_profiles', 'no_kp')->ignore($profileId),
            ],
            'tarikh_lahir' => ['required', 'date', 'before:today'],
            'jantina' => ['required', Rule::in(['lelaki', 'perempuan'])],
            'agama' => ['required', Rule::in(['Islam'])],
            'warganegara' => ['required', Rule::in(['Malaysia', 'Penduduk Tetap'])],

            'alamat_rumah' => ['required', 'string', 'max:1000'],
            'no_tel_rumah' => ['nullable', 'regex:/^(0\d{1,2}-?\d{6,8})$/'],
            'no_tel_bimbit' => ['required', 'regex:/^(01[0-9]-?\d{7,8})$/'],
            'tinggal_dalam_kariah' => ['required', 'in:1'],
            'tempoh_menetap' => ['required', 'string', 'max:100'],

            'pekerjaan' => ['nullable', 'string', 'max:255'],
            'nama_majikan' => ['nullable', 'string', 'max:255'],
            'alamat_kerja' => ['nullable', 'string', 'max:1000'],

            'nama_waris' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z@\'\.\-\/\s]+$/u'],
            'hubungan_waris' => ['required', Rule::in([
                'Suami',
                'Isteri',
                'Anak',
                'Ibu',
                'Bapa',
                'Ibu Mertua',
                'Bapa Mertua'
            ])],
            'no_tel_waris' => ['required', 'regex:/^(01[0-9]-?\d{7,8})$/'],
            'alamat_waris' => ['required', 'string', 'max:1000'],

            'payment_plan' => $paymentPlanRule,
            'akuan' => ['required', 'accepted'],
        ];
    }

    protected function messages(): array
    {
        return [
            'nama.required' => 'Nama penuh wajib diisi.',
            'nama.regex' => 'Nama penuh hanya boleh mengandungi huruf, ruang, apostrophe, titik, sempang atau slash.',

            'no_kp.required' => 'No. MyKad wajib diisi.',
            'no_kp.digits' => 'No. MyKad mesti 12 digit tanpa sengkang.',
            'no_kp.unique' => 'No. MyKad ini telah didaftarkan.',

            'tarikh_lahir.required' => 'Tarikh lahir wajib diisi.',
            'tarikh_lahir.before' => 'Tarikh lahir mesti sebelum hari ini.',

            'jantina.required' => 'Jantina wajib dipilih.',
            'jantina.in' => 'Jantina yang dipilih tidak sah.',

            'agama.required' => 'Agama wajib dipilih.',
            'agama.in' => 'Keahlian hanya terbuka kepada pemohon beragama Islam.',

            'warganegara.required' => 'Status warganegara wajib dipilih.',
            'warganegara.in' => 'Hanya warganegara Malaysia atau Penduduk Tetap yang sah dibenarkan.',

            'alamat_rumah.required' => 'Alamat rumah wajib diisi.',

            'no_tel_rumah.regex' => 'Format no. telefon rumah tidak sah. Contoh: 03-12345678',
            'no_tel_bimbit.required' => 'No. telefon bimbit wajib diisi.',
            'no_tel_bimbit.regex' => 'Format no. telefon bimbit tidak sah. Contoh: 0123456789',

            'tinggal_dalam_kariah.required' => 'Sila nyatakan sama ada anda tinggal dalam kariah.',
            'tinggal_dalam_kariah.in' => 'Permohonan hanya terbuka kepada pemastautin dalam kariah Masjid RTB Bukit Changgang.',

            'tempoh_menetap.required' => 'Tempoh menetap wajib diisi.',

            'nama_waris.required' => 'Nama waris wajib diisi.',
            'nama_waris.regex' => 'Nama waris hanya boleh mengandungi huruf, ruang, apostrophe, titik, sempang atau slash.',

            'hubungan_waris.required' => 'Hubungan waris wajib dipilih.',
            'hubungan_waris.in' => 'Hubungan waris yang dipilih tidak sah.',

            'no_tel_waris.required' => 'No. telefon waris wajib diisi.',
            'no_tel_waris.regex' => 'Format no. telefon waris tidak sah. Contoh: 0123456789',

            'alamat_waris.required' => 'Alamat waris wajib diisi.',

            'payment_plan.required' => 'Pelan pembayaran wajib dipilih.',
            'payment_plan.in' => 'Pelan pembayaran yang dipilih tidak sah.',

            'akuan.required' => 'Sila tandakan pengakuan sebelum simpan.',
            'akuan.accepted' => 'Anda perlu mengesahkan bahawa maklumat yang diberikan adalah benar.',
        ];
    }

    protected function getStatusClass(?string $status): string
    {
        return match ($status) {
            'pending' => 'warning',
            'approved' => 'primary',
            'active' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    protected function getPaymentPlanLabel(?string $paymentPlan): string
    {
        return match ($paymentPlan) {
            'bulanan' => 'Bulanan',
            'tahunan' => 'Tahunan',
            default => '-',
        };
    }

    protected function syncAccountTypeByNoKp(string $noKp): void
    {
        $user = auth()->user();

        $matchedDependent = Dependent::where('no_kp', $noKp)->first();
        $matchedProfile = UserProfile::where('no_kp', $noKp)->first();

        if ($matchedDependent) {
            $user->update([
                'account_type' => 'tanggungan',
                'linked_dependent_id' => $matchedDependent->id,
                'linked_profile_id' => null,
            ]);
        } elseif ($matchedProfile && $matchedProfile->user_id === $user->id) {
            $user->update([
                'account_type' => 'utama',
                'linked_profile_id' => $matchedProfile->id,
                'linked_dependent_id' => null,
            ]);
        }
    }

    public function dependentMainMember()
    {
        $user = auth()->user();

        if ($user->account_type !== 'tanggungan' || !$user->linked_dependent_id) {
            abort(403, 'Akses hanya untuk akaun tanggungan.');
        }

        $dependent = Dependent::findOrFail($user->linked_dependent_id);

        $principalProfile = UserProfile::where('user_id', $dependent->user_id)->first();

        $otherDependents = Dependent::where('user_id', $dependent->user_id)
            ->orderBy('name')
            ->get();

        $familyMembers = collect();

        if ($principalProfile) {
            $familyMembers->push([
                'nama' => $principalProfile->nama,
                'no_kp' => $principalProfile->no_kp,
                'peranan' => 'ahli_utama',
                'pertalian' => 'Ahli Utama',
                'status_kehidupan' => $principalProfile->status_kehidupan ?? 'aktif',
                'tarikh_kematian' => $principalProfile->tarikh_kematian ?? null,
            ]);
        }

        foreach ($otherDependents as $item) {
            $familyMembers->push([
                'nama' => $item->name,
                'no_kp' => $item->no_kp,
                'peranan' => 'tanggungan',
                'pertalian' => ucwords($item->pertalian ?? '-'),
                'status_kehidupan' => $item->status_kehidupan ?? 'aktif',
                'tarikh_kematian' => $item->tarikh_kematian ?? null,
            ]);
        }

        $dependentIds = $otherDependents->pluck('id')->toArray();

        $familyDeathReports = DeathReport::query()
            ->with('burialPlot')
            ->where(function ($query) use ($dependent, $dependentIds) {
                $query->where('user_id', $dependent->user_id);

                if (!empty($dependentIds)) {
                    $query->orWhereIn('dependent_id', $dependentIds);
                }
            })
            ->latest()
            ->get()
            ->map(function ($report) use ($principalProfile, $otherDependents) {
                $hubunganKeluarga = '-';

                if ($report->dependent_id) {
                    $matchedDependent = $otherDependents->firstWhere('id', $report->dependent_id);
                    if ($matchedDependent) {
                        $hubunganKeluarga = ucwords($matchedDependent->pertalian ?? '-');
                    }
                } elseif ($principalProfile && $report->user_id == $principalProfile->user_id) {
                    $hubunganKeluarga = 'Ahli Utama';
                }

                $report->hubungan_keluarga = $hubunganKeluarga;

                if (empty($report->burial_lot_no) && !empty($report->burialPlot)) {
                    $lot = [];

                    if (!empty($report->burialPlot->zone)) {
                        $lot[] = 'Zon ' . $report->burialPlot->zone;
                    }

                    if (!empty($report->burialPlot->row_number)) {
                        $lot[] = 'Baris ' . $report->burialPlot->row_number;
                    }

                    if (!empty($report->burialPlot->lot_number)) {
                        $lot[] = 'Lot ' . $report->burialPlot->lot_number;
                    }

                    $report->burial_lot_no = !empty($lot) ? implode(', ', $lot) : '-';
                }

                return $report;
            });

        return view('dependent.main-member', compact(
            'dependent',
            'principalProfile',
            'familyMembers',
            'familyDeathReports'
        ));
    }

    protected function isDependentFlow(): bool
    {
        return session('user_profile.is_dependent', false) === true;
    }
}