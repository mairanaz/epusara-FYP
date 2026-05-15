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
        $year = $request->get('year', now()->year);
        $month = $request->get('month');
        $status = $request->get('status');

        /*
        |--------------------------------------------------------------------------
        | Query utama untuk senarai detail tempahan
        |--------------------------------------------------------------------------
        */
        $query = GraveOrder::with([
                'user.profile',
                'deathReport',
                'burialPlot',
            ])
            ->whereYear('created_at', $year);

        if (!empty($month)) {
            $query->whereMonth('created_at', $month);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->get();

        /*
        |--------------------------------------------------------------------------
        | Summary berdasarkan filter semasa
        |--------------------------------------------------------------------------
        */
        $summary = [
            'total' => $orders->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'approved' => $orders->where('status', 'approved')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
            'amount' => $orders->sum('amount'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Ringkasan bulanan
        | Jika status dipilih, table bulanan ikut status tersebut.
        | Jika bulan dipilih, detail table ikut bulan, tapi table bulanan masih papar Jan-Dis.
        |--------------------------------------------------------------------------
        */
        $monthlyQuery = GraveOrder::whereYear('created_at', $year);

        if (!empty($status)) {
            $monthlyQuery->where('status', $status);
        }

        $monthlyOrders = $monthlyQuery->get();

        $monthlySummary = collect(range(1, 12))->map(function ($monthNo) use ($monthlyOrders) {
            $ordersInMonth = $monthlyOrders->filter(function ($order) use ($monthNo) {
                return $order->created_at && (int) $order->created_at->month === (int) $monthNo;
            });

            return [
                'month_no' => $monthNo,
                'month_name' => Carbon::create()->month($monthNo)->locale('ms')->translatedFormat('F'),
                'total' => $ordersInMonth->count(),
                'pending' => $ordersInMonth->where('status', 'pending')->count(),
                'approved' => $ordersInMonth->where('status', 'approved')->count(),
                'cancelled' => $ordersInMonth->where('status', 'cancelled')->count(),
                'amount' => $ordersInMonth->sum('amount'),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Data untuk chart
        |--------------------------------------------------------------------------
        */
        $chartLabels = $monthlySummary->pluck('month_name')->values();
        $chartTotals = $monthlySummary->pluck('total')->values();

        $statusChartLabels = collect(['Menunggu', 'Diluluskan', 'Dibatalkan']);
        $statusChartTotals = collect([
            $summary['pending'],
            $summary['approved'],
            $summary['cancelled'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tahun yang ada dalam database
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

        return view('admin.reports.grave-orders.index', compact(
            'year',
            'month',
            'status',
            'years',
            'orders',
            'summary',
            'monthlySummary',
            'chartLabels',
            'chartTotals',
            'statusChartLabels',
            'statusChartTotals'
        ));
    }
}