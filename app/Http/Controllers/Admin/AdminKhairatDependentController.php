<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dependent;
use Illuminate\Http\Request;

class AdminKhairatDependentController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = Dependent::with('user');

        if ($request->filled('search')) {
            $search = $request->search;

            $baseQuery->where(function ($q) use ($search) {
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
                $baseQuery->whereIn('pertalian', ['isteri', 'suami']);
            } elseif ($request->pertalian === 'lain-lain') {
                $baseQuery->whereNotIn('pertalian', ['anak', 'isteri', 'suami']);
            } else {
                $baseQuery->where('pertalian', $request->pertalian);
            }
        }

        $totalCount = (clone $baseQuery)->count();
        $anakCount = (clone $baseQuery)->where('pertalian', 'anak')->count();
        $pasanganCount = (clone $baseQuery)->whereIn('pertalian', ['isteri', 'suami'])->count();
        $lainCount = (clone $baseQuery)->whereNotIn('pertalian', ['anak', 'isteri', 'suami'])->count();

        $dependents = (clone $baseQuery)->latest()->paginate(10);

        return view('admin.khairat.dependents.index', compact(
            'dependents',
            'totalCount',
            'anakCount',
            'pasanganCount',
            'lainCount'
        ));
    }
}