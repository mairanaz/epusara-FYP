<?php

namespace App\Http\Controllers;

use App\Models\Dependent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
            'no_tel' => ['nullable', 'regex:/^[0-9]{9,12}$/'],
        ], [
            'name.required' => 'Nama tanggungan wajib diisi.',
            'no_kp.required' => 'No. KP wajib diisi.',
            'no_kp.regex' => 'No. KP mesti 12 digit tanpa dash.',
            'pasangan.required' => 'Sila pilih pasangan.',
            'pertalian.required' => 'Sila pilih pertalian.',
            'no_tel.regex' => 'No. telefon mesti nombor sahaja antara 9 hingga 12 digit.',
        ]);

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

        $validated['user_id'] = Auth::id();

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
            'no_tel' => ['nullable', 'regex:/^[0-9]{9,12}$/'],
        ], [
            'name.required' => 'Nama tanggungan wajib diisi.',
            'no_kp.required' => 'No. KP wajib diisi.',
            'no_kp.regex' => 'No. KP mesti 12 digit tanpa dash.',
            'pasangan.required' => 'Sila pilih pasangan.',
            'pertalian.required' => 'Sila pilih pertalian.',
            'no_tel.regex' => 'No. telefon mesti nombor sahaja antara 9 hingga 12 digit.',
        ]);

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

        $dependent->update($validated);

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
}