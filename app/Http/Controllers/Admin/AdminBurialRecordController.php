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
        $query = DeathReport::with([
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

        if ($request->filled('image_status')) {
            if ($request->image_status === 'ada') {
                $query->where(function ($q) {
                    $q->whereHas('burialPlot', function ($plotQuery) {
                        $plotQuery->whereNotNull('grave_image');
                    })
                    ->orWhereHas('assignedBurialPlot', function ($plotQuery) {
                        $plotQuery->whereNotNull('grave_image');
                    });
                });
            }

            if ($request->image_status === 'tiada') {
                $query->where(function ($q) {
                    $q->whereHas('burialPlot', function ($plotQuery) {
                        $plotQuery->whereNull('grave_image');
                    })
                    ->orWhereHas('assignedBurialPlot', function ($plotQuery) {
                        $plotQuery->whereNull('grave_image');
                    });
                });
            }
        }

        $burialRecords = $query
            ->latest()
            ->paginate(10);

        $burialRecords->appends($request->query());

        /*
        |--------------------------------------------------------------------------
        | Tetapkan status kepuk untuk paparan Rekod Kubur
        |--------------------------------------------------------------------------
        | Priority:
        | 1. approved
        | 2. pending
        | 3. cancelled
        | 4. belum_tempah
        */
        $burialRecords->getCollection()->transform(function ($record) {
            $selectedOrder = $this->getSelectedGraveOrder($record);

            $record->selected_grave_order = $selectedOrder;
            $record->kepuk_status = $selectedOrder?->status ?? 'belum_tempah';
            $record->kepuk_status_label = $this->getKepukStatusLabel($record->kepuk_status);
            $record->kepuk_status_badge = $this->getKepukStatusBadge($record->kepuk_status);

            return $record;
        });

        return view('admin.burial-records.index', compact('burialRecords'));
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