<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dependent;
use Illuminate\Http\Request;

class AdminKhairatDependentController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Statistik Semua Tanggungan
        |--------------------------------------------------------------------------
        | Admin boleh nampak jumlah tanggungan, masih hidup dan meninggal dunia.
        */
        $deathStatuses = [
            'meninggal',
            'meninggal_dunia',
            'meninggal dunia',
            'mati',
            'deceased',
            'dead',
        ];

        $approvedDeathReportStatuses = [
            'disahkan',
            'approved',
            'selesai',
        ];

        $statQuery = Dependent::query();

        $totalCount = (clone $statQuery)->count();

        $meninggalCount = (clone $statQuery)
            ->where(function ($query) use ($deathStatuses, $approvedDeathReportStatuses) {
                $query->whereIn('status_tanggungan', $deathStatuses)
                    ->orWhereIn('status_kehidupan', $deathStatuses)
                    ->orWhereHas('deathReports', function ($deathQuery) use ($approvedDeathReportStatuses) {
                        $deathQuery->whereIn('status', $approvedDeathReportStatuses);
                    });
            })
            ->count();

        $hidupCount = $totalCount - $meninggalCount;

        /*
        |--------------------------------------------------------------------------
        | Senarai Tanggungan + Status Hidup / Meninggal Dunia
        |--------------------------------------------------------------------------
        */
        $query = Dependent::with(['user', 'user.profile'])
            ->withExists([
                'deathReports as has_death_report' => function ($deathQuery) use ($approvedDeathReportStatuses) {
                    $deathQuery->whereIn('status', $approvedDeathReportStatuses);
                }
            ]);

        /*
        |--------------------------------------------------------------------------
        | Carian
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('no_kp', 'like', "%{$search}%")
                    ->orWhere('no_tel', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Status Tanggungan
        |--------------------------------------------------------------------------
        | hidup      = tanggungan masih hidup
        | meninggal  = tanggungan telah meninggal dunia
        */
        if ($request->filled('status')) {
            if ($request->status === 'meninggal') {
                $query->where(function ($q) use ($deathStatuses, $approvedDeathReportStatuses) {
                    $q->whereIn('status_tanggungan', $deathStatuses)
                        ->orWhereIn('status_kehidupan', $deathStatuses)
                        ->orWhereHas('deathReports', function ($deathQuery) use ($approvedDeathReportStatuses) {
                            $deathQuery->whereIn('status', $approvedDeathReportStatuses);
                        });
                });
            }

            if ($request->status === 'hidup') {
                $query->where(function ($q) use ($deathStatuses) {
                    $q->whereNull('status_tanggungan')
                        ->orWhereNotIn('status_tanggungan', $deathStatuses);
                })
                ->where(function ($q) use ($deathStatuses) {
                    $q->whereNull('status_kehidupan')
                        ->orWhereNotIn('status_kehidupan', $deathStatuses);
                })
                ->whereDoesntHave('deathReports', function ($deathQuery) use ($approvedDeathReportStatuses) {
                    $deathQuery->whereIn('status', $approvedDeathReportStatuses);
                });
            }
        }

        $dependents = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.khairat.dependents.index', compact(
            'dependents',
            'totalCount',
            'hidupCount',
            'meninggalCount'
        ));
    }
}