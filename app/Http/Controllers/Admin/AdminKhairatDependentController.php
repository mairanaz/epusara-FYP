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
        | Statistik Tetap - Semua Tanggungan
        |--------------------------------------------------------------------------
        | Statistik ini tidak berubah walaupun admin buat carian / filter.
        */
        $statQuery = Dependent::query();

        $totalCount = (clone $statQuery)->count();

        $anakCount = (clone $statQuery)
            ->where('pertalian', 'anak')
            ->count();

        $pasanganCount = (clone $statQuery)
            ->whereIn('pertalian', ['isteri', 'suami'])
            ->count();

        $lainCount = (clone $statQuery)
            ->whereNotIn('pertalian', ['anak', 'isteri', 'suami'])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Senarai Tanggungan + Carian / Filter
        |--------------------------------------------------------------------------
        | Bahagian ini sahaja yang berubah bila admin buat carian.
        */
        $query = Dependent::with(['user', 'user.profile']);

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

        if ($request->filled('pertalian')) {
            if ($request->pertalian === 'pasangan') {
                $query->whereIn('pertalian', ['isteri', 'suami']);
            } elseif ($request->pertalian === 'lain-lain') {
                $query->whereNotIn('pertalian', ['anak', 'isteri', 'suami']);
            } else {
                $query->where('pertalian', $request->pertalian);
            }
        }

        $dependents = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.khairat.dependents.index', compact(
            'dependents',
            'totalCount',
            'anakCount',
            'pasanganCount',
            'lainCount'
        ));
    }
}