<?php

namespace App\Http\Controllers;

use App\Models\BurialPlot;
use App\Models\DeathReport;
use App\Models\Dependent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserGraveLocationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Tentukan keluarga user login
        |--------------------------------------------------------------------------
        | Ahli utama:
        | - mainUserId = id user sendiri
        |
        | Tanggungan:
        | - cari rekod dependent berdasarkan linked_dependent_id
        | - mainUserId = user_id ahli utama kepada tanggungan tersebut
        */
        $mainUserId = null;
        $familyDependentIds = collect();

        if ($user->account_type === 'utama') {
            $mainUserId = $user->id;

            $familyDependentIds = Dependent::where('user_id', $mainUserId)
                ->pluck('id');
        }

        if ($user->account_type === 'tanggungan') {
            $linkedDependent = Dependent::find($user->linked_dependent_id);

            if ($linkedDependent) {
                $mainUserId = $linkedDependent->user_id;

                $familyDependentIds = Dependent::where('user_id', $mainUserId)
                    ->pluck('id');
            }
        }

        $query = DeathReport::with(['burialPlot', 'assignedBurialPlot'])
            ->where(function ($q) use ($mainUserId, $familyDependentIds) {
                if ($mainUserId) {
                    /*
                    |--------------------------------------------------------------------------
                    | Papar semua rekod kematian dalam keluarga yang sama
                    |--------------------------------------------------------------------------
                    | Termasuk:
                    | - rekod kematian ahli utama
                    | - rekod kematian semua tanggungan bawah ahli utama
                    */
                    $q->where('user_id', $mainUserId)
                      ->orWhereIn('dependent_id', $familyDependentIds);
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
            ->where(function ($q) {
                /*
                |--------------------------------------------------------------------------
                | Hanya papar rekod yang sudah ada lokasi kubur
                |--------------------------------------------------------------------------
                */
                $q->whereNotNull('burial_plot_id')
                  ->orWhereHas('assignedBurialPlot');
            });

        $deathReports = $query
            ->latest()
            ->paginate(10);

        return view('user.grave-locations.index', compact('deathReports'));
    }

    public function show(DeathReport $deathReport)
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Tentukan keluarga user login
        |--------------------------------------------------------------------------
        */
        $mainUserId = null;
        $familyDependentIds = collect();

        if ($user->account_type === 'utama') {
            $mainUserId = $user->id;

            $familyDependentIds = Dependent::where('user_id', $mainUserId)
                ->pluck('id');
        }

        if ($user->account_type === 'tanggungan') {
            $linkedDependent = Dependent::find($user->linked_dependent_id);

            if ($linkedDependent) {
                $mainUserId = $linkedDependent->user_id;

                $familyDependentIds = Dependent::where('user_id', $mainUserId)
                    ->pluck('id');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Kawalan akses
        |--------------------------------------------------------------------------
        | User hanya boleh lihat rekod kubur dalam keluarga yang sama.
        */
        $allowed = false;

        if ($mainUserId) {
            if ($deathReport->user_id == $mainUserId) {
                $allowed = true;
            }

            if ($deathReport->dependent_id && $familyDependentIds->contains($deathReport->dependent_id)) {
                $allowed = true;
            }
        }

        if (!$allowed) {
            abort(403, 'Anda tidak dibenarkan melihat lokasi kubur ini.');
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil lokasi kubur
        |--------------------------------------------------------------------------
        */
        $deathReport->load(['burialPlot', 'assignedBurialPlot']);

        $selectedPlot = $deathReport->final_burial_plot;

        if (!$selectedPlot) {
            return redirect()
                ->route('user.grave-locations.index')
                ->with('error', 'Lokasi kubur belum ditetapkan oleh pentadbir.');
        }

        /*
        |--------------------------------------------------------------------------
        | Papar peta ikut zon si mati sahaja
        |--------------------------------------------------------------------------
        */
        $zone = $selectedPlot->zone;

        $plots = BurialPlot::where('zone', $zone)
            ->orderBy('row_number')
            ->orderBy('lot_number')
            ->get()
            ->groupBy('row_number');

        return view('user.grave-locations.show', compact(
            'deathReport',
            'selectedPlot',
            'zone',
            'plots'
        ));
    }
}