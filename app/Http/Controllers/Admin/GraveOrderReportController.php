<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GraveOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GraveOrderReportController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->get('year', now()->year);
        $month = $request->get('month');

        /*
        |--------------------------------------------------------------------------
        | Query asas laporan
        |--------------------------------------------------------------------------
        */
        $baseQuery = GraveOrder::query()
            ->whereYear('grave_orders.created_at', $year);

        if (!empty($month)) {
            $baseQuery->whereMonth('grave_orders.created_at', $month);
        }

        /*
        |--------------------------------------------------------------------------
        | Senarai detail tempahan
        |--------------------------------------------------------------------------
        */
        $orders = (clone $baseQuery)
            ->with([
                'user.profile',
                'deathReport',
                'burialPlot',
            ])
            ->latest('grave_orders.created_at')
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Summary Cards
        |--------------------------------------------------------------------------
        */
        $summary = [
            'total' => (clone $baseQuery)->count(),

            'pending' => (clone $baseQuery)
                ->where('grave_orders.status', 'pending')
                ->count(),

            'approved' => (clone $baseQuery)
                ->where('grave_orders.status', 'approved')
                ->count(),

            'cancelled' => (clone $baseQuery)
                ->whereIn('grave_orders.status', ['cancelled', 'rejected'])
                ->count(),

            'kepuk' => (clone $baseQuery)
                ->where(function ($q) {
                    $q->where('grave_orders.order_type', 'like', '%kepuk%')
                        ->orWhere('grave_orders.order_label', 'like', '%kepuk%')
                        ->orWhere('grave_orders.category', 'like', '%kepuk%');
                })
                ->count(),

            'nisan' => (clone $baseQuery)
                ->where(function ($q) {
                    $q->where('grave_orders.order_type', 'like', '%nisan%')
                        ->orWhere('grave_orders.order_label', 'like', '%nisan%')
                        ->orWhere('grave_orders.category', 'like', '%nisan%');
                })
                ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Peratus Status + Bukti Pengiraan
        |--------------------------------------------------------------------------
        */
        $totalForCalculation = $summary['total'];

        $statusPercentages = [
            'pending' => $totalForCalculation > 0
                ? round(($summary['pending'] / $totalForCalculation) * 100, 2)
                : 0,

            'approved' => $totalForCalculation > 0
                ? round(($summary['approved'] / $totalForCalculation) * 100, 2)
                : 0,

            'cancelled' => $totalForCalculation > 0
                ? round(($summary['cancelled'] / $totalForCalculation) * 100, 2)
                : 0,
        ];

        $calculationProof = collect([
            [
                'status' => 'Menunggu Kelulusan',
                'count' => $summary['pending'],
                'formula' => $summary['pending'] . ' / ' . $summary['total'] . ' × 100',
                'percentage' => $statusPercentages['pending'],
            ],
            [
                'status' => 'Diluluskan',
                'count' => $summary['approved'],
                'formula' => $summary['approved'] . ' / ' . $summary['total'] . ' × 100',
                'percentage' => $statusPercentages['approved'],
            ],
            [
                'status' => 'Dibatalkan / Ditolak',
                'count' => $summary['cancelled'],
                'formula' => $summary['cancelled'] . ' / ' . $summary['total'] . ' × 100',
                'percentage' => $statusPercentages['cancelled'],
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Ringkasan Tempahan Mengikut Jenis Dan Status
        |--------------------------------------------------------------------------
        */
        $typeExpression = "
            COALESCE(
                NULLIF(grave_orders.order_label, ''),
                NULLIF(grave_orders.order_type, ''),
                NULLIF(grave_orders.category, ''),
                'Tidak Dinyatakan'
            )
        ";

        $orderTypeSummary = (clone $baseQuery)
            ->selectRaw("$typeExpression as type_label")
            ->selectRaw("SUM(CASE WHEN grave_orders.status = 'pending' THEN 1 ELSE 0 END) as pending_total")
            ->selectRaw("SUM(CASE WHEN grave_orders.status = 'approved' THEN 1 ELSE 0 END) as approved_total")
            ->selectRaw("SUM(CASE WHEN grave_orders.status IN ('cancelled', 'rejected') THEN 1 ELSE 0 END) as cancelled_total")
            ->selectRaw("COUNT(*) as grand_total")
            ->groupByRaw($typeExpression)
            ->orderByDesc('grand_total')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Ringkasan Tempahan Mengikut Zon / Lokasi
        |--------------------------------------------------------------------------
        */
        $zoneOrders = (clone $baseQuery)
            ->with('burialPlot')
            ->get();

        $zoneSummary = $zoneOrders
            ->groupBy(function ($order) {
                if ($order->burialPlot) {
                    return $order->burialPlot->zone_label
                        ?? $order->burialPlot->zone
                        ?? $order->burialPlot->section
                        ?? $order->burialPlot->blok
                        ?? $order->burialPlot->plot_code
                        ?? 'Belum Ditentukan';
                }

                return 'Belum Ditentukan';
            })
            ->map(function ($items, $zoneLabel) {
                return (object) [
                    'zone_label' => $zoneLabel,

                    'pending' => $items
                        ->where('status', 'pending')
                        ->count(),

                    'approved' => $items
                        ->where('status', 'approved')
                        ->count(),

                    'cancelled' => $items
                        ->filter(function ($item) {
                            return in_array($item->status, ['cancelled', 'rejected']);
                        })
                        ->count(),

                    'total' => $items->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Ringkasan Bulanan
        |--------------------------------------------------------------------------
        */
        $monthlySummary = collect(range(1, 12))->map(function ($monthNo) use ($year) {
            $monthQuery = GraveOrder::query()
                ->whereYear('grave_orders.created_at', $year)
                ->whereMonth('grave_orders.created_at', $monthNo);

            return [
                'month_no' => $monthNo,
                'month_name' => Carbon::create(null, $monthNo, 1)
                    ->locale('ms')
                    ->translatedFormat('F'),

                'total' => (clone $monthQuery)->count(),

                'pending' => (clone $monthQuery)
                    ->where('grave_orders.status', 'pending')
                    ->count(),

                'approved' => (clone $monthQuery)
                    ->where('grave_orders.status', 'approved')
                    ->count(),

                'cancelled' => (clone $monthQuery)
                    ->whereIn('grave_orders.status', ['cancelled', 'rejected'])
                    ->count(),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Data Chart
        |--------------------------------------------------------------------------
        */
        $chartLabels = $monthlySummary->pluck('month_name')->values();
        $chartTotals = $monthlySummary->pluck('total')->values();

        $statusChartLabels = collect([
            'Menunggu Kelulusan',
            'Diluluskan',
            'Dibatalkan / Ditolak',
        ]);

        $statusChartTotals = collect([
            $summary['pending'],
            $summary['approved'],
            $summary['cancelled'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tahun Dalam Database
        |--------------------------------------------------------------------------
        */
        $years = GraveOrder::selectRaw('YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        if ($years->isEmpty()) {
            $years = collect([now()->year]);
        }

        if (!$years->contains($year)) {
            $years->prepend($year);
        }

        return view('admin.reports.grave-orders.index', compact(
            'year',
            'month',
            'years',
            'orders',
            'summary',
            'statusPercentages',
            'calculationProof',
            'orderTypeSummary',
            'zoneSummary',
            'monthlySummary',
            'chartLabels',
            'chartTotals',
            'statusChartLabels',
            'statusChartTotals'
        ));
    }
}