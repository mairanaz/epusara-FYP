<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Models\Payment;
use App\Models\Dependent;
use Illuminate\Http\Request;

class AdminKhairatMemberController extends Controller
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
        }

        $members = $query->latest()->paginate(10);

        return view('admin.khairat.members.index', compact('members'));
    }

    public function show(UserProfile $member)
    {
        $payments = Payment::where('user_id', $member->user_id)->latest()->get();
        $dependents = Dependent::where('user_id', $member->user_id)->latest()->get();

        $statusClass = match ($member->status_permohonan) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'active' => 'primary',
            default => 'secondary',
        };

        return view('admin.khairat.members.show', compact(
            'member',
            'payments',
            'dependents',
            'statusClass'
        ));
    }
}