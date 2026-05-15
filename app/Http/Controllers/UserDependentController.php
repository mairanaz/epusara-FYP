<?php

namespace App\Http\Controllers;

use App\Models\Dependent;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserDependentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dependents = Dependent::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('user.dependents.index', compact('dependents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $profile = Auth::user()->profile;
        $gender = strtolower($profile->jantina ?? '');

        return view('user.dependents.create', compact('gender'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $profile = Auth::user()->profile;
        $gender = strtolower($profile->jantina ?? '');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'no_kp' => ['required', 'regex:/^[0-9]{12}$/'],
            'pasangan' => ['required', Rule::in(['ya', 'tidak'])],
            'pertalian' => [
                'required',
                Rule::in([
                    'suami',
                    'isteri',
                    'anak',
                    'bapa kandung',
                    'ibu kandung',
                    'bapa mertua',
                    'ibu mertua',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | Field baru untuk syarat kelayakan tanggungan
            |--------------------------------------------------------------------------
            */
            'status_perkahwinan' => [
                'nullable',
                Rule::in(['bujang', 'berkahwin', 'duda', 'janda']),
            ],
            'tinggal_bersama' => ['required', Rule::in(['1', '0'])],

            'no_tel' => ['nullable', 'regex:/^[0-9]{9,12}$/'],
        ], [
            'name.required' => 'Nama tanggungan wajib diisi.',
            'no_kp.required' => 'No. KP wajib diisi.',
            'no_kp.regex' => 'No. KP mesti 12 digit tanpa dash.',
            'pasangan.required' => 'Sila pilih pasangan.',
            'pertalian.required' => 'Sila pilih pertalian.',
            'status_perkahwinan.in' => 'Status perkahwinan tidak sah.',
            'tinggal_bersama.required' => 'Sila pilih sama ada tinggal bersama ahli utama atau tidak.',
            'tinggal_bersama.in' => 'Pilihan tinggal bersama tidak sah.',
            'no_tel.regex' => 'No. telefon mesti nombor sahaja antara 9 hingga 12 digit.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validation pasangan ikut jantina ahli utama
        |--------------------------------------------------------------------------
        */
        if ($validated['pasangan'] === 'ya') {
            if ($gender === 'lelaki' && $validated['pertalian'] !== 'isteri') {
                return back()
                    ->withErrors(['pertalian' => 'Bagi pengguna lelaki, jika pasangan = Ya maka pertalian mestilah isteri.'])
                    ->withInput();
            }

            if ($gender === 'perempuan' && $validated['pertalian'] !== 'suami') {
                return back()
                    ->withErrors(['pertalian' => 'Bagi pengguna perempuan, jika pasangan = Ya maka pertalian mestilah suami.'])
                    ->withInput();
            }
        }

        if ($validated['pasangan'] === 'tidak') {
            $allowedRelations = [
                'anak',
                'bapa kandung',
                'ibu kandung',
                'bapa mertua',
                'ibu mertua',
            ];

            if (!in_array($validated['pertalian'], $allowedRelations)) {
                return back()
                    ->withErrors(['pertalian' => 'Pertalian yang dipilih tidak sah apabila pasangan = Tidak.'])
                    ->withInput();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Set status perkahwinan untuk pasangan
        |--------------------------------------------------------------------------
        | Suami / isteri dianggap berkahwin.
        |--------------------------------------------------------------------------
        */
        if (in_array($validated['pertalian'], ['suami', 'isteri'])) {
            $validated['status_perkahwinan'] = 'berkahwin';
        }

        /*
        |--------------------------------------------------------------------------
        | Jalankan validation kelayakan tanggungan
        |--------------------------------------------------------------------------
        */
        $this->validateDependentEligibility($validated);

        $validated['user_id'] = Auth::id();
        $validated['status_tanggungan'] = 'aktif';
        $validated['sebab_tidak_layak'] = null;
        $validated['tarikh_keluar_tanggungan'] = null;

        Dependent::create($validated);

        return redirect()
            ->route('user.dependents.index')
            ->with('success', 'Tanggungan berjaya ditambah.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dependent = Dependent::findOrFail($id);

        if ($dependent->user_id != Auth::id()) {
            abort(403, 'Akses tidak dibenarkan.');
        }

        return view('user.dependents.show', compact('dependent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $dependent = Dependent::findOrFail($id);

        if ($dependent->user_id != Auth::id()) {
            abort(403, 'Akses tidak dibenarkan.');
        }

        $profile = Auth::user()->profile;
        $gender = strtolower($profile->jantina ?? '');

        return view('user.dependents.edit', compact('dependent', 'gender'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dependent = Dependent::findOrFail($id);

        if ($dependent->user_id != Auth::id()) {
            abort(403, 'Akses tidak dibenarkan.');
        }

        $profile = Auth::user()->profile;
        $gender = strtolower($profile->jantina ?? '');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'no_kp' => ['required', 'regex:/^[0-9]{12}$/'],
            'pasangan' => ['required', Rule::in(['ya', 'tidak'])],
            'pertalian' => [
                'required',
                Rule::in([
                    'suami',
                    'isteri',
                    'anak',
                    'bapa kandung',
                    'ibu kandung',
                    'bapa mertua',
                    'ibu mertua',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | Field baru untuk syarat kelayakan tanggungan
            |--------------------------------------------------------------------------
            */
            'status_perkahwinan' => [
                'nullable',
                Rule::in(['bujang', 'berkahwin', 'duda', 'janda']),
            ],
            'tinggal_bersama' => ['required', Rule::in(['1', '0'])],

            'no_tel' => ['nullable', 'regex:/^[0-9]{9,12}$/'],
        ], [
            'name.required' => 'Nama tanggungan wajib diisi.',
            'no_kp.required' => 'No. KP wajib diisi.',
            'no_kp.regex' => 'No. KP mesti 12 digit tanpa dash.',
            'pasangan.required' => 'Sila pilih pasangan.',
            'pertalian.required' => 'Sila pilih pertalian.',
            'status_perkahwinan.in' => 'Status perkahwinan tidak sah.',
            'tinggal_bersama.required' => 'Sila pilih sama ada tinggal bersama ahli utama atau tidak.',
            'tinggal_bersama.in' => 'Pilihan tinggal bersama tidak sah.',
            'no_tel.regex' => 'No. telefon mesti nombor sahaja antara 9 hingga 12 digit.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validation pasangan ikut jantina ahli utama
        |--------------------------------------------------------------------------
        */
        if ($validated['pasangan'] === 'ya') {
            if ($gender === 'lelaki' && $validated['pertalian'] !== 'isteri') {
                return back()
                    ->withErrors(['pertalian' => 'Bagi pengguna lelaki, jika pasangan = Ya maka pertalian mestilah isteri.'])
                    ->withInput();
            }

            if ($gender === 'perempuan' && $validated['pertalian'] !== 'suami') {
                return back()
                    ->withErrors(['pertalian' => 'Bagi pengguna perempuan, jika pasangan = Ya maka pertalian mestilah suami.'])
                    ->withInput();
            }
        }

        if ($validated['pasangan'] === 'tidak') {
            $allowedRelations = [
                'anak',
                'bapa kandung',
                'ibu kandung',
                'bapa mertua',
                'ibu mertua',
            ];

            if (!in_array($validated['pertalian'], $allowedRelations)) {
                return back()
                    ->withErrors(['pertalian' => 'Pertalian yang dipilih tidak sah apabila pasangan = Tidak.'])
                    ->withInput();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Set status perkahwinan untuk pasangan
        |--------------------------------------------------------------------------
        */
        if (in_array($validated['pertalian'], ['suami', 'isteri'])) {
            $validated['status_perkahwinan'] = 'berkahwin';
        }

        /*
        |--------------------------------------------------------------------------
        | Jika anak sudah berkahwin / duda / janda
        |--------------------------------------------------------------------------
        | Jangan delete rekod.
        | Tukar status kepada tidak layak supaya rekod masih ada untuk sejarah.
        |--------------------------------------------------------------------------
        */
        if (
            strtolower($validated['pertalian']) === 'anak' &&
            $validated['status_perkahwinan'] !== 'bujang'
        ) {
            $dependent->update([
                ...$validated,
                'status_tanggungan' => 'tidak_layak',
                'sebab_tidak_layak' => 'Anak telah berkahwin',
                'tarikh_keluar_tanggungan' => now()->toDateString(),
            ]);

            return redirect()
                ->route('user.dependents.index')
                ->with('warning', 'Maklumat dikemaskini. Tanggungan ini tidak lagi layak kerana anak telah berkahwin.');
        }

        /*
        |--------------------------------------------------------------------------
        | Jika masih layak, jalankan validation kelayakan tanggungan
        |--------------------------------------------------------------------------
        */
        $this->validateDependentEligibility($validated, $dependent->id);

        $dependent->update([
            ...$validated,
            'status_tanggungan' => 'aktif',
            'sebab_tidak_layak' => null,
            'tarikh_keluar_tanggungan' => null,
        ]);

        return redirect()
            ->route('user.dependents.index')
            ->with('success', 'Tanggungan berjaya dikemaskini.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dependent = Dependent::findOrFail($id);

        if ($dependent->user_id != Auth::id()) {
            abort(403, 'Akses tidak dibenarkan.');
        }

        $dependent->delete();

        return redirect()
            ->route('user.dependents.index')
            ->with('success', 'Tanggungan berjaya dipadam.');
    }

    private function validateDependentEligibility(array $data, ?int $ignoreDependentId = null): void
    {
        $pertalian = strtolower($data['pertalian']);
        $statusPerkahwinan = $data['status_perkahwinan'] ?? null;
        $tinggalBersama = filter_var($data['tinggal_bersama'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $noKp = $data['no_kp'];

        /*
        |--------------------------------------------------------------------------
        | 1. Anak hanya boleh jadi tanggungan jika bujang
        |--------------------------------------------------------------------------
        */
        if ($pertalian === 'anak' && $statusPerkahwinan !== 'bujang') {
            throw ValidationException::withMessages([
                'status_perkahwinan' => 'Anak yang telah berkahwin tidak layak menjadi tanggungan ahli utama. Sila daftar sebagai ahli utama baharu.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Ibu/bapa kandung atau mertua mesti tinggal bersama ahli utama
        |--------------------------------------------------------------------------
        */
        $kategoriIbuBapa = [
            'bapa kandung',
            'ibu kandung',
            'bapa mertua',
            'ibu mertua',
        ];

        if (in_array($pertalian, $kategoriIbuBapa) && !$tinggalBersama) {
            throw ValidationException::withMessages([
                'tinggal_bersama' => 'Ibu/bapa kandung atau mertua hanya layak menjadi tanggungan jika tinggal bersama ahli utama.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Satu No KP tidak boleh menjadi tanggungan aktif di bawah ahli lain
        |--------------------------------------------------------------------------
        */
        $existingDependent = Dependent::where('no_kp', $noKp)
            ->where('status_tanggungan', 'aktif')
            ->when($ignoreDependentId, function ($query) use ($ignoreDependentId) {
                $query->where('id', '!=', $ignoreDependentId);
            })
            ->first();

        if ($existingDependent) {
            throw ValidationException::withMessages([
                'no_kp' => 'No. KP ini telah didaftarkan sebagai tanggungan aktif di bawah ahli lain.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Seseorang yang sudah menjadi ahli utama aktif tidak boleh jadi tanggungan
        |--------------------------------------------------------------------------
        */
        $existingMainMember = UserProfile::where('no_kp', $noKp)
            ->whereIn('status_permohonan', ['approved', 'active'])
            ->first();

        if ($existingMainMember) {
            throw ValidationException::withMessages([
                'no_kp' => 'No. KP ini telah didaftarkan sebagai ahli utama aktif dan tidak boleh didaftarkan sebagai tanggungan.',
            ]);
        }
    }
}