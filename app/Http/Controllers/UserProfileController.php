<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show()
    {
        $profile = auth()->user()->profile;

        if (!$profile) {
            return redirect()->route('user.profile.create');
        }

        return view('user.profile.show', compact('profile'));
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
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'no_kp' => 'required|string|max:20|unique:user_profiles,no_kp',
            'tarikh_lahir' => 'required|date',
            'agama' => 'required|string|max:50',
            'warganegara' => 'required|string|max:50',
            'alamat_rumah' => 'required|string|max:1000',
            'no_tel_rumah' => 'nullable|string|max:30',
            'no_tel_bimbit' => 'required|string|max:30',
            'tinggal_dalam_kariah' => 'required|boolean',
            'tempoh_menetap' => 'nullable|string|max:100',
            'pekerjaan' => 'nullable|string|max:255',
            'nama_majikan' => 'nullable|string|max:255',
            'alamat_kerja' => 'nullable|string|max:1000',
            'nama_waris' => 'required|string|max:255',
            'hubungan_waris' => 'required|string|max:100',
            'no_tel_waris' => 'required|string|max:30',
            'alamat_waris' => 'nullable|string|max:1000',
            'payment_plan' => 'required|in:bulanan,tahunan',
        ]);

        $data['tarikh_permohonan'] = now()->toDateString();
        $data['status_permohonan'] = 'draft';

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

        $rules = [
            'nama' => 'required|string|max:255',
            'no_kp' => 'required|string|max:20|unique:user_profiles,no_kp,' . $profile->id,
            'tarikh_lahir' => 'required|date',
            'agama' => 'required|string|max:50',
            'warganegara' => 'required|string|max:50',
            'alamat_rumah' => 'required|string|max:1000',
            'no_tel_rumah' => 'nullable|string|max:30',
            'no_tel_bimbit' => 'required|string|max:30',
            'tinggal_dalam_kariah' => 'required|boolean',
            'tempoh_menetap' => 'nullable|string|max:100',
            'pekerjaan' => 'nullable|string|max:255',
            'nama_majikan' => 'nullable|string|max:255',
            'alamat_kerja' => 'nullable|string|max:1000',
            'nama_waris' => 'required|string|max:255',
            'hubungan_waris' => 'required|string|max:100',
            'no_tel_waris' => 'required|string|max:30',
            'alamat_waris' => 'nullable|string|max:1000',
            'payment_plan' => $hasPaidPayments ? 'nullable|in:bulanan,tahunan' : 'required|in:bulanan,tahunan',
        ];

        $data = $request->validate($rules);

        if ($hasPaidPayments) {
            $data['payment_plan'] = $profile->payment_plan;
        }

        $profile->update($data);

        return redirect()->route('user.profile.show')
            ->with('success', 'Maklumat berjaya dikemaskini.');
    }
}