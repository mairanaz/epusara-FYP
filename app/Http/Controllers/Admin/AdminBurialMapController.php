<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BurialPlot;
use App\Models\DeathReport;
use Illuminate\Http\Request;

class AdminBurialMapController extends Controller
{
    public function index(Request $request)
    {
        $selectedZone = $request->get('zone', 'all');
        $search = $request->get('search');

        $plotsQuery = BurialPlot::with('deathReport')
            ->orderBy('zone')
            ->orderBy('row_number')
            ->orderBy('lot_number');

        if ($selectedZone !== 'all') {
            $plotsQuery->where('zone', $selectedZone);
        }

        $plots = $plotsQuery->get();

        $matchedPlotId = null;
        $matchedReport = null;

        if ($search) {
            $matchedReport = DeathReport::where(function ($query) use ($search) {
                    $query->where('nama_si_mati', 'like', '%' . $search . '%')
                        ->orWhere('no_kp_si_mati', 'like', '%' . $search . '%')
                        ->orWhere('burial_lot_no', 'like', '%' . $search . '%')
                        ->orWhere('burial_plot_code', 'like', '%' . $search . '%');
                })
                ->whereNotNull('burial_plot_id')
                ->latest()
                ->first();

            if ($matchedReport) {
                $matchedPlotId = $matchedReport->burial_plot_id;
            }
        }

        $summary = [
            'total' => BurialPlot::count(),
            'available' => BurialPlot::where('status', 'available')->count(),
            'occupied' => BurialPlot::where('status', 'occupied')->count(),
            'zone_l' => BurialPlot::where('zone', 'L')->count(),
            'zone_p' => BurialPlot::where('zone', 'P')->count(),
            'zone_k' => BurialPlot::where('zone', 'K')->count(),
        ];

        return view('admin.burial-map.index', compact(
            'plots',
            'summary',
            'selectedZone',
            'search',
            'matchedPlotId',
            'matchedReport'
        ));
    }
}