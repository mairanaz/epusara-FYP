<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    public function show()
    {
        $profile = auth()->user()->profile;

        if (!$profile) {
            return redirect()->route('user.profile.create');
        }

        $statusClass = $this->getStatusClass($profile->status_permohonan);
        $paymentPlanLabel = $this->getPaymentPlanLabel($profile->payment_plan);

        $canSubmit = is_null($profile->tarikh_permohonan) && !in_array($profile->status_permohonan, ['approved', 'rejected']);

        return view('user.profile.show', compact(
            'profile',
            'statusClass',
            'paymentPlanLabel',
            'canSubmit'
        ));
    }

    public function create()
    {
        if (auth()->user()->profile) {
            return redirect()->route('user.profile.show');
        }

        return view('user.profile.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateProfile($request);

        $data['user_id'] = auth()->id();
        $data['tarikh_permohonan'] = now()->toDateString();
        $data['status_permohonan'] = 'pending';
        $data['catatan_permohonan'] = null;

        UserProfile::create($data);

        return redirect()->route('user.profile.show')
            ->with('success', 'Maklumat profil berjaya disimpan sebagai draf.');
    }

    public function edit()
    {
        $profile = auth()->user()->profile;

        if (!$profile) {
            return redirect()->route('user.profile.create');
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
            return redirect()->route('user.profile.create');
        }

        $hasPaidPayments = Payment::where('user_id', auth()->id())
            ->where('status', 'paid')
            ->exists();

        $data = $this->validateProfile($request, $profile->id, $hasPaidPayments);

        if ($hasPaidPayments) {
            $data['payment_plan'] = $profile->payment_plan;
        }

        // Kalau admin dah semak, jangan tukar status sewenang-wenangnya
       if (in_array($profile->status_permohonan, ['approved', 'rejected', 'active'])) {
            unset($data['status_permohonan']);
        }

        $profile->update($data);

        return redirect()->route('user.profile.show')
            ->with('success', 'Maklumat profil berjaya dikemaskini.');
    }

    public function submit()
    {
        $profile = auth()->user()->profile;

        if (!$profile) {
            return redirect()->route('user.profile.create')
                ->with('error', 'Sila lengkapkan maklumat profil terlebih dahulu.');
        }

        // Semak semula profile sebelum submit
        $validator = validator($profile->toArray(), $this->profileRules($profile->id, false), $this->messages());

        if ($validator->fails()) {
            return redirect()->route('user.profile.edit')
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Sila lengkapkan semua maklumat sebelum menghantar permohonan.');
        }

        if (!is_null($profile->tarikh_permohonan) || in_array($profile->status_permohonan, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('user.profile.show')
                ->with('error', 'Permohonan ini telah dihantar atau sedang diproses.');
        }

        $profile->update([
            'status_permohonan' => 'pending',
            'tarikh_permohonan' => now()->toDateString(),
        ]);

        return redirect()->route('user.profile.show')
            ->with('success', 'Permohonan keahlian berjaya dihantar untuk semakan admin.');
    }

    protected function validateProfile(Request $request, ?int $profileId = null, bool $hasPaidPayments = false): array
    {
        $rules = $this->profileRules($profileId, $hasPaidPayments);

        return $request->validate($rules, $this->messages());
    }

    protected function profileRules(?int $profileId = null, bool $hasPaidPayments = false): array
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

            // ikut syarat keahlian: mesti tinggal dalam kariah
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
}