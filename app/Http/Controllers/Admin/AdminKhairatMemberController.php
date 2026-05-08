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
        /*
        |--------------------------------------------------------------------------
        | Query Asas - Ahli Utama Sahaja
        |--------------------------------------------------------------------------
        | Senarai Ahli hanya paparkan ahli utama yang sudah diluluskan / aktif.
        | Tanggungan tidak dipaparkan di sini.
        */
        $mainMemberQuery = UserProfile::query()
            ->whereIn('status_permohonan', ['approved', 'active'])
            ->whereNotExists(function ($query) {
                $query->selectRaw(1)
                    ->from('dependents')
                    ->whereColumn('dependents.no_kp', 'user_profiles.no_kp');
            });

        /*
        |--------------------------------------------------------------------------
        | Statistik Tetap - Ahli Utama Sahaja
        |--------------------------------------------------------------------------
        */
        $totalMembers = (clone $mainMemberQuery)->count();

        $aliveCount = (clone $mainMemberQuery)
            ->where(function ($query) {
                $query->whereNull('status_kehidupan')
                    ->orWhereIn('status_kehidupan', ['hidup', 'aktif']);
            })
            ->count();

        $deceasedCount = (clone $mainMemberQuery)
            ->whereIn('status_kehidupan', ['meninggal', 'meninggal dunia'])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Senarai Ahli Utama Sahaja + Carian
        |--------------------------------------------------------------------------
        */
        $query = clone $mainMemberQuery;

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('no_kp', 'like', "%{$search}%")
                ->orWhere('no_tel_bimbit', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_kehidupan')) {
            if ($request->status_kehidupan === 'hidup') {
                $query->where(function ($q) {
                    $q->whereNull('status_kehidupan')
                    ->orWhereIn('status_kehidupan', ['hidup', 'aktif']);
                });
            }

            if ($request->status_kehidupan === 'meninggal') {
                $query->whereIn('status_kehidupan', ['meninggal', 'meninggal dunia']);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */
        $sort = $request->get('sort', 'name_asc');

        match ($sort) {
            'name_desc' => $query->orderBy('nama', 'desc'),
            'plan' => $query->orderBy('payment_plan', 'asc')->orderBy('nama', 'asc'),
            default => $query->orderBy('nama', 'asc'),
        };

        $members = $query->paginate(10)->withQueryString();

        return view('admin.khairat.members.index', compact(
            'members',
            'totalMembers',
            'aliveCount',
            'deceasedCount',
            'sort'
        ));
    }

    public function show(UserProfile $member)
    {
        $payments = Payment::where('user_id', $member->user_id)
            ->latest()
            ->get();

        $dependents = Dependent::where('user_id', $member->user_id)
            ->latest()
            ->get();

        $statusKehidupan = strtolower($member->status_kehidupan ?? 'hidup');

        $isDeceased = in_array($statusKehidupan, ['meninggal', 'meninggal dunia']);

        $statusClass = $isDeceased ? 'danger' : 'success';
        $statusLabel = $isDeceased ? 'Meninggal Dunia' : 'Masih Hidup';

        return view('admin.khairat.members.show', compact(
            'member',
            'payments',
            'dependents',
            'statusClass',
            'statusLabel'
        ));
    }
}