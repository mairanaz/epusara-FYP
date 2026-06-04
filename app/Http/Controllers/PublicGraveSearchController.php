<?php

namespace App\Http\Controllers;

use App\Models\DeathReport;
use App\Models\BurialPlot;
use Illuminate\Http\Request;

class PublicGraveSearchController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search'));

        $deathReports = DeathReport::query()
            ->with('burialPlot')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_si_mati', 'like', '%' . $search . '%')
                        ->orWhereHas('burialPlot', function ($plotQuery) use ($search) {
                            $plotQuery->where('plot_code', 'like', '%' . $search . '%');
                        });
                });
            })
            ->whereNotNull('burial_plot_id')
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('public.grave-search.index', compact(
            'deathReports',
            'search'
        ));
    }

    public function show(DeathReport $deathReport)
    {
        $deathReport->load('burialPlot');

        /*
        |--------------------------------------------------------------------------
        | Lot kubur yang telah ditetapkan kepada si mati
        |--------------------------------------------------------------------------
        */
        $selectedPlot = $deathReport->burialPlot;

        if (!$selectedPlot) {
            return redirect()
                ->route('public.grave-search.index')
                ->with('error', 'Lokasi kubur belum ditetapkan oleh pentadbir.');
        }

        /*
        |--------------------------------------------------------------------------
        | Tentukan zon kubur
        |--------------------------------------------------------------------------
        | Gunakan column zone dahulu kerana field ini memang wujud dalam
        | table burial_plots. Jika kosong, sistem akan membaca plot_code.
        */
        $zone = strtoupper(
            $selectedPlot->zone ?: substr($selectedPlot->plot_code, 0, 1)
        );

        if (!in_array($zone, ['L', 'P', 'W', 'K'])) {
            $zone = 'L';
        }

        /*
        |--------------------------------------------------------------------------
        | Senarai plot bagi zon yang sama untuk paparan pelan dalaman SVG
        |--------------------------------------------------------------------------
        */
        $plots = BurialPlot::query()
            ->where('zone', $zone)
            ->orderBy('row_number')
            ->orderBy('lot_number')
            ->get()
            ->groupBy('row_number');

        /*
        |--------------------------------------------------------------------------
        | Data GIS lokasi sebenar tanah perkuburan
        |--------------------------------------------------------------------------
        | Data ini diambil daripada config/cemetery.php dan .env.
        */
        $cemetery = [
            'name' => config('cemetery.name'),
            'address' => config('cemetery.address'),
            'latitude' => config('cemetery.latitude'),
            'longitude' => config('cemetery.longitude'),
            'entrance_note' => config('cemetery.entrance_note'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Validasi koordinat GIS
        |--------------------------------------------------------------------------
        */
        $hasCoordinates =
            is_numeric($cemetery['latitude']) &&
            is_numeric($cemetery['longitude']);

        if ($hasCoordinates) {
            $cemetery['latitude'] = (float) $cemetery['latitude'];
            $cemetery['longitude'] = (float) $cemetery['longitude'];
        }

        return view('public.grave-search.show', compact(
            'deathReport',
            'selectedPlot',
            'plots',
            'zone',
            'cemetery',
            'hasCoordinates'
        ));
    }
}