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
        $query = UserProfile::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_kp', 'like', "%{$search}%")
                  ->orWhere('no_tel_bimbit', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status_permohonan', $request->status);
        } else {
            $query->where('status_permohonan', 'pending');
        }

        $profiles = $query->latest()->paginate(10);

        return view('admin.profile.index', compact('profiles'));
    }

    public function show(UserProfile $profile)
    {
        $statusClass = $this->getStatusClass($profile->status_permohonan);

        $hasPaidPayment = Payment::where('user_id', $profile->user_id)
            ->where('status', 'paid')
            ->exists();

        return view('admin.profile.show', compact('profile', 'statusClass', 'hasPaidPayment'));
    }

    public function approve(UserProfile $profile)
    {
        if ($profile->status_permohonan === 'approved') {
            return redirect()->route('admin.profile.show', $profile)
                ->with('error', 'Permohonan ini sudah diluluskan.');
        }

        if ($profile->status_permohonan === 'rejected') {
            return redirect()->route('admin.profile.show', $profile)
                ->with('error', 'Permohonan yang telah ditolak tidak boleh terus diluluskan.');
        }

        if ($profile->status_permohonan !== 'pending') {
            return redirect()->route('admin.profile.show', $profile)
                ->with('error', 'Hanya permohonan berstatus pending boleh diluluskan.');
        }

        $profile->update([
            'status_permohonan' => 'approved',
            'catatan_permohonan' => 'Permohonan telah diluluskan oleh pentadbir.',
        ]);

        return redirect()->route('admin.profile.show', $profile)
            ->with('success', 'Permohonan berjaya diluluskan.');
    }

    public function reject(Request $request, UserProfile $profile)
    {
        $request->validate([
            'catatan_permohonan' => ['required', 'string', 'max:1000'],
        ], [
            'catatan_permohonan.required' => 'Sila isi sebab penolakan.',
            'catatan_permohonan.max' => 'Catatan penolakan tidak boleh melebihi 1000 aksara.',
        ]);

        if ($profile->status_permohonan === 'approved') {
            return redirect()->route('admin.profile.show', $profile)
                ->with('error', 'Permohonan yang sudah diluluskan tidak boleh terus ditolak.');
        }

        if ($profile->status_permohonan !== 'pending') {
            return redirect()->route('admin.profile.show', $profile)
                ->with('error', 'Hanya permohonan berstatus pending boleh ditolak.');
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
            'approved' => 'success',
            'rejected' => 'danger',
            'active' => 'primary',
            default => 'secondary',
        };
    }
}