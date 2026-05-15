<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserUpgradeMembershipController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        if ($user->account_type !== 'tanggungan') {
            return redirect()->route('dashboard')
                ->with('info', 'Akaun anda sudah berstatus ahli utama.');
        }

        $dependent = $user->linked_dependent_id
            ? \App\Models\Dependent::find($user->linked_dependent_id)
            : null;

        if (!$dependent) {
            return redirect()->route('dashboard')
                ->with('error', 'Rekod tanggungan tidak dijumpai.');
        }

        if ($dependent->status_tanggungan !== 'tidak_layak') {
            return redirect()->route('dashboard')
                ->with('info', 'Akaun tanggungan anda masih aktif.');
        }

        return view('user.upgrade-membership.create', compact('user', 'dependent'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->account_type !== 'tanggungan') {
            return redirect()->route('dashboard');
        }

        $dependent = \App\Models\Dependent::findOrFail($user->linked_dependent_id);

        if ($dependent->status_tanggungan !== 'tidak_layak') {
            return back()->with('error', 'Akaun tanggungan ini masih layak dan tidak perlu dinaik taraf.');
        }

        /*
        |--------------------------------------------------------------------------
        | Elak duplicate ahli utama aktif
        |--------------------------------------------------------------------------
        */
        $existingProfile = UserProfile::where('no_kp', $dependent->no_kp)
            ->whereIn('status_permohonan', ['pending', 'approved', 'active'])
            ->first();

        if ($existingProfile) {
            return back()->with('error', 'Permohonan ahli utama untuk No. KP ini telah wujud.');
        }

        $validated = $request->validate([
            'alamat' => ['required', 'string', 'max:500'],
            'no_tel' => ['required', 'regex:/^[0-9]{9,12}$/'],
            'jantina' => ['required', 'in:lelaki,perempuan'],
            'tarikh_lahir' => ['nullable', 'date'],
        ], [
            'alamat.required' => 'Alamat wajib diisi.',
            'no_tel.required' => 'No. telefon wajib diisi.',
            'no_tel.regex' => 'No. telefon mesti nombor sahaja antara 9 hingga 12 digit.',
            'jantina.required' => 'Jantina wajib dipilih.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Cipta profile ahli utama baharu, tapi status masih pending
        |--------------------------------------------------------------------------
        */
        $profile = UserProfile::create([
            'user_id' => $user->id,
            'nama' => $dependent->name,
            'no_kp' => $dependent->no_kp,
            'no_tel' => $validated['no_tel'],
            'jantina' => $validated['jantina'],
            'tarikh_lahir' => $validated['tarikh_lahir'] ?? null,
            'alamat' => $validated['alamat'],
            'status_permohonan' => 'pending',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Link profile kepada user, tapi jangan tukar account_type dulu
        | Tukar kepada utama hanya selepas admin approve.
        |--------------------------------------------------------------------------
        */
        $user->update([
            'linked_profile_id' => $profile->id,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Permohonan naik taraf ahli utama berjaya dihantar. Sila tunggu kelulusan admin.');
    }
}