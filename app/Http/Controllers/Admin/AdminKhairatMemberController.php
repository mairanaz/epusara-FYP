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
        | Status Kehidupan
        |--------------------------------------------------------------------------
        */
        $aliveStatuses = ['hidup', 'aktif'];
        $deceasedStatuses = ['meninggal', 'meninggal dunia', 'meninggal_dunia'];

        /*
        |--------------------------------------------------------------------------
        | Query Asas - Ahli Utama Sahaja
        |--------------------------------------------------------------------------
        | Papar profile yang:
        | - status permohonan approved / active
        | - user masih wujud
        | - user account_type = utama / main_member / null lama
        |
        | Nota:
        | main_member dimasukkan untuk keselamatan jika ada data lama semasa test.
        |--------------------------------------------------------------------------
        */
        $mainMemberQuery = UserProfile::with('user')
            ->whereIn('status_permohonan', ['approved', 'active'])
            ->whereHas('user', function ($query) {
                $query->where(function ($q) {
                    $q->whereIn('account_type', ['utama', 'main_member'])
                        ->orWhereNull('account_type');
                });
            });

        /*
        |--------------------------------------------------------------------------
        | Statistik Tetap - Ahli Utama Sahaja
        |--------------------------------------------------------------------------
        */
        $totalMembers = (clone $mainMemberQuery)->count();

        $aliveCount = (clone $mainMemberQuery)
            ->where(function ($query) use ($aliveStatuses) {
                $query->whereNull('status_kehidupan')
                    ->orWhereIn('status_kehidupan', $aliveStatuses);
            })
            ->count();

        $deceasedCount = (clone $mainMemberQuery)
            ->whereIn('status_kehidupan', $deceasedStatuses)
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
            if (in_array($request->status_kehidupan, ['hidup', 'aktif'])) {
                $query->where(function ($q) use ($aliveStatuses) {
                    $q->whereNull('status_kehidupan')
                        ->orWhereIn('status_kehidupan', $aliveStatuses);
                });
            }

            if ($request->status_kehidupan === 'meninggal') {
                $query->whereIn('status_kehidupan', $deceasedStatuses);
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
        $payments = Payment::with('transferredFromUser.profile')
            ->where('user_id', $member->user_id)
            ->latest()
            ->get();

        $dependents = Dependent::where('user_id', $member->user_id)
            ->latest()
            ->get();

        $statusKehidupan = strtolower($member->status_kehidupan ?? 'hidup');

        $isDeceased = in_array($statusKehidupan, [
            'meninggal',
            'meninggal dunia',
            'meninggal_dunia',
        ]);

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