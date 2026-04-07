<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dependent;
use Illuminate\Http\Request;

class AdminKhairatDependentController extends Controller
{
    public function index(Request $request)
    {
        $query = Dependent::with('user');

        if ($request->filled('search')) {
            $search = $request->search;

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
            $query->where('pertalian', $request->pertalian);
        }

        $dependents = $query->latest()->paginate(10);

        return view('admin.khairat.dependents.index', compact('dependents'));
    }
}