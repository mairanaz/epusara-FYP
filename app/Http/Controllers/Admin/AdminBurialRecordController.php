<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeathReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBurialRecordController extends Controller
{
    public function index(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | Base Query Rekod Kubur
    |--------------------------------------------------------------------------
    | Ini query asas untuk semua rekod kubur yang sudah ada lot.
    */
    $baseQuery = DeathReport::with([
            'user',
            'dependent',
            'burialPlot',
            'assignedBurialPlot',
            'graveOrders',
        ])
        ->where(function ($q) {
            $q->whereNotNull('burial_plot_id')
              ->orWhereHas('assignedBurialPlot');
        });

    /*
    |--------------------------------------------------------------------------
    | Statistik Tetap - Tidak berubah bila search / filter
    |--------------------------------------------------------------------------
    */
    $totalBurialRecords = (clone $baseQuery)->count();

    $kepukStatusCounts = [
        'belum_tempah' => (clone $baseQuery)
            ->whereDoesntHave('graveOrders')
            ->count(),

        'pending' => (clone $baseQuery)
            ->whereDoesntHave('graveOrders', function ($q) {
                $q->where('status', 'approved');
            })
            ->whereHas('graveOrders', function ($q) {
                $q->where('status', 'pending');
            })
            ->count(),

        'approved' => (clone $baseQuery)
            ->whereHas('graveOrders', function ($q) {
                $q->where('status', 'approved');
            })
            ->count(),

        'cancelled' => (clone $baseQuery)
            ->whereDoesntHave('graveOrders', function ($q) {
                $q->whereIn('status', ['approved', 'pending']);
            })
            ->whereHas('graveOrders', function ($q) {
                $q->where('status', 'cancelled');
            })
            ->count(),
    ];

    /*
    |--------------------------------------------------------------------------
    | Query Jadual - Ini sahaja berubah ikut search / filter
    |--------------------------------------------------------------------------
    */
    $query = clone $baseQuery;

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('nama_si_mati', 'like', "%{$search}%")
                ->orWhere('no_kp_si_mati', 'like', "%{$search}%")
                ->orWhere('burial_plot_code', 'like', "%{$search}%")
                ->orWhere('burial_lot_no', 'like', "%{$search}%")
                ->orWhereHas('burialPlot', function ($plotQuery) use ($search) {
                    $plotQuery->where('plot_code', 'like', "%{$search}%");
                })
                ->orWhereHas('assignedBurialPlot', function ($plotQuery) use ($search) {
                    $plotQuery->where('plot_code', 'like', "%{$search}%");
                })
                ->orWhereHas('graveOrders', function ($orderQuery) use ($search) {
                    $orderQuery->where('order_label', 'like', "%{$search}%")
                        ->orWhere('order_type', 'like', "%{$search}%");
                });
        });
    }

    if ($request->filled('zone')) {
        $zone = $request->zone;

        $query->where(function ($q) use ($zone) {
            $q->whereHas('burialPlot', function ($plotQuery) use ($zone) {
                $plotQuery->where('zone', $zone);
            })
            ->orWhereHas('assignedBurialPlot', function ($plotQuery) use ($zone) {
                $plotQuery->where('zone', $zone);
            });
        });
    }

    if ($request->filled('kepuk_status')) {
        $kepukStatus = $request->kepuk_status;

        if ($kepukStatus === 'belum_tempah') {
            $query->whereDoesntHave('graveOrders');
        }

        if ($kepukStatus === 'approved') {
            $query->whereHas('graveOrders', function ($orderQuery) {
                $orderQuery->where('status', 'approved');
            });
        }

        if ($kepukStatus === 'pending') {
            $query->whereDoesntHave('graveOrders', function ($orderQuery) {
                $orderQuery->where('status', 'approved');
            })
            ->whereHas('graveOrders', function ($orderQuery) {
                $orderQuery->where('status', 'pending');
            });
        }

        if ($kepukStatus === 'cancelled') {
            $query->whereDoesntHave('graveOrders', function ($orderQuery) {
                $orderQuery->whereIn('status', ['approved', 'pending']);
            })
            ->whereHas('graveOrders', function ($orderQuery) {
                $orderQuery->where('status', 'cancelled');
            });
        }
    }

    if ($request->filled('image_status')) {
        if ($request->image_status === 'ada') {
            $query->where(function ($q) {
                $q->whereHas('burialPlot', function ($plotQuery) {
                    $plotQuery->whereNotNull('grave_image')
                        ->where('grave_image', '!=', '');
                })
                ->orWhereHas('assignedBurialPlot', function ($plotQuery) {
                    $plotQuery->whereNotNull('grave_image')
                        ->where('grave_image', '!=', '');
                });
            });
        }

        if ($request->image_status === 'tiada') {
            $query->where(function ($q) {
                $q->whereHas('burialPlot', function ($plotQuery) {
                    $plotQuery->where(function ($qq) {
                        $qq->whereNull('grave_image')
                           ->orWhere('grave_image', '');
                    });
                })
                ->orWhereHas('assignedBurialPlot', function ($plotQuery) {
                    $plotQuery->where(function ($qq) {
                        $qq->whereNull('grave_image')
                           ->orWhere('grave_image', '');
                    });
                });
            });
        }
    }

    $burialRecords = $query
        ->latest()
        ->paginate(10)
        ->withQueryString();

    /*
    |--------------------------------------------------------------------------
    | Tetapkan status kepuk untuk paparan jadual
    |--------------------------------------------------------------------------
    */
    $burialRecords->getCollection()->transform(function ($record) {
        $selectedOrder = $this->getSelectedGraveOrder($record);

        $record->selected_grave_order = $selectedOrder;
        $record->kepuk_status = $selectedOrder?->status ?? 'belum_tempah';
        $record->kepuk_status_label = $this->getKepukStatusLabel($record->kepuk_status);
        $record->kepuk_status_badge = $this->getKepukStatusBadge($record->kepuk_status);

        return $record;
    });

    return view('admin.burial-records.index', compact(
        'burialRecords',
        'totalBurialRecords',
        'kepukStatusCounts'
    ));
}

    public function show(DeathReport $deathReport)
    {
        $deathReport->load([
            'user',
            'dependent',
            'burialPlot',
            'assignedBurialPlot',
            'graveOrders',
        ]);

        $plot = $deathReport->final_burial_plot;

        if (!$plot) {
            return redirect()
                ->route('admin.burial-records.index')
                ->with('error', 'Rekod ini belum mempunyai lot kubur.');
        }

        $selectedOrder = $this->getSelectedGraveOrder($deathReport);
        $kepukStatus = $selectedOrder?->status ?? 'belum_tempah';
        $kepukStatusLabel = $this->getKepukStatusLabel($kepukStatus);
        $kepukStatusBadge = $this->getKepukStatusBadge($kepukStatus);

        return view('admin.burial-records.show', compact(
            'deathReport',
            'plot',
            'selectedOrder',
            'kepukStatus',
            'kepukStatusLabel',
            'kepukStatusBadge'
        ));
    }

    public function updateGraveImage(Request $request, DeathReport $deathReport)
    {
        $request->validate([
            'grave_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $deathReport->load(['burialPlot', 'assignedBurialPlot']);

        $plot = $deathReport->final_burial_plot;

        if (!$plot) {
            return back()->with('error', 'Lot kubur belum ditetapkan.');
        }

        if ($plot->grave_image && Storage::disk('public')->exists($plot->grave_image)) {
            Storage::disk('public')->delete($plot->grave_image);
        }

        $path = $request->file('grave_image')->store('grave-images', 'public');

        $plot->update([
            'grave_image' => $path,
            'grave_image_updated_at' => now(),
        ]);

        return back()->with('success', 'Gambar kubur berjaya dikemaskini.');
    }

    private function getSelectedGraveOrder(DeathReport $deathReport)
    {
        $orders = $deathReport->graveOrders ?? collect();

        if ($orders->isEmpty()) {
            return null;
        }

        return $orders->firstWhere('status', 'approved')
            ?? $orders->firstWhere('status', 'pending')
            ?? $orders->firstWhere('status', 'cancelled')
            ?? $orders->sortByDesc('created_at')->first();
    }

    private function getKepukStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Menunggu Kelulusan',
            'approved' => 'Diluluskan',
            'cancelled' => 'Dibatalkan',
            'belum_tempah' => 'Belum Tempah',
            default => 'Tidak Diketahui',
        };
    }

    private function getKepukStatusBadge(?string $status): string
    {
        return match ($status) {
            'pending' => 'bg-warning text-white',
            'approved' => 'bg-success text-white',
            'cancelled' => 'bg-danger text-white',
            'belum_tempah' => 'bg-light text-dark border',
            default => 'bg-secondary text-white',
        };
    }
}