<?php

namespace App\Http\Controllers;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show()
    {
        $profile = auth()->user()->profile;

        // kalau belum isi, pergi create
        if (!$profile) return redirect()->route('user.profile.create');

        return view('user.profile.show', compact('profile'));
    }

    public function create()
    {
        // kalau dah ada data, terus pergi show
        if (auth()->user()->profile) return redirect()->route('user.profile.show');

        return view('user.profile.create');
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'nama' => 'required|string|max:255',
        'no_kp' => 'required|string|max:20',
        'email' => 'required|email|max:255',
        'tarikh' => 'nullable|date',
        'alamat_rumah' => 'nullable|string|max:255',
        'no_tel_rumah' => 'nullable|string|max:30',
        'no_tel' => 'nullable|string|max:30',
        'pekerjaan' => 'nullable|string|max:255',
        'alamat_kerja' => 'nullable|string|max:255',
    ]);

    UserProfile::updateOrCreate(
        ['user_id' => auth()->id()],
        $data
    );

    return redirect()->route('user.profile.show')
        ->with('success', 'Maklumat berjaya disimpan.');
}
    public function edit()
    {
        $profile = auth()->user()->profile;

        if (!$profile) return redirect()->route('user.profile.create');

        return view('user.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $profile = auth()->user()->profile;

        if (!$profile) return redirect()->route('user.profile.create');

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'no_kp' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'tarikh' => 'nullable|date',
            'alamat_rumah' => 'nullable|string|max:255',
            'no_tel_rumah' => 'nullable|string|max:30',
            'no_tel' => 'nullable|string|max:30',
            'pekerjaan' => 'nullable|string|max:255',
            'alamat_kerja' => 'nullable|string|max:255',
        ]);

        $profile->update($data);

        return redirect()->route('user.profile.show')->with('success', 'Maklumat berjaya dikemaskini.');
    }
}