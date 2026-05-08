<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GraveOrder;
use Illuminate\Http\Request;
use App\Exports\GraveOrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminGraveOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = GraveOrder::with(['user', 'deathReport', 'burialPlot'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('order_label', 'like', "%{$search}%")
                    ->orWhereHas('deathReport', function ($deathQuery) use ($search) {
                        $deathQuery->where('nama_si_mati', 'like', "%{$search}%")
                            ->orWhere('nama_pelapor', 'like', "%{$search}%")
                            ->orWhere('burial_plot_code', 'like', "%{$search}%")
                            ->orWhere('burial_lot_no', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->paginate(10)->withQueryString();

        $statusCounts = [
            'pending' => GraveOrder::where('status', 'pending')->count(),
            'approved' => GraveOrder::where('status', 'approved')->count(),
            'cancelled' => GraveOrder::where('status', 'cancelled')->count(),
        ];

        return view('admin.grave-orders.index', compact('orders', 'statusCounts'));
    }

    public function show(GraveOrder $graveOrder)
    {
        $graveOrder->load(['user', 'deathReport', 'burialPlot']);

        return view('admin.grave-orders.show', compact('graveOrder'));
    }

    public function update(Request $request, GraveOrder $graveOrder)
    {
        $request->validate([
            'status' => ['required', 'in:pending,approved,cancelled'],
            'admin_note' => ['nullable', 'string'],
        ], [
            'status.required' => 'Sila pilih status tempahan.',
            'status.in' => 'Status tempahan tidak sah.',
        ]);

        $data = [
            'status' => $request->status,
            'admin_note' => $request->admin_note,
        ];

        if ($request->status === 'approved' && !$graveOrder->approved_at) {
            $data['approved_at'] = now();
        }

        if ($request->status !== 'approved') {
            $data['approved_at'] = null;
        }

        $graveOrder->update($data);

        return redirect()
            ->route('admin.grave-orders.show', $graveOrder)
            ->with('success', 'Status tempahan berjaya dikemaskini.');
    }

    public function exportExcel(Request $request)
    {
        $status = $request->get('status', 'approved');
        $search = $request->get('search');

        if ($status === '') {
            $status = 'approved';
        }

        $fileName = 'senarai-tempahan-kepuk-diluluskan-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new GraveOrdersExport($status, $search), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $status = $request->get('status', 'approved');
        $search = $request->get('search');

        if ($status === '') {
            $status = 'approved';
        }

        $query = GraveOrder::with(['user', 'deathReport', 'burialPlot'])
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_label', 'like', "%{$search}%")
                    ->orWhere('order_type', 'like', "%{$search}%")
                    ->orWhereHas('deathReport', function ($deathQuery) use ($search) {
                        $deathQuery->where('nama_si_mati', 'like', "%{$search}%")
                            ->orWhere('no_kp_si_mati', 'like', "%{$search}%")
                            ->orWhere('nama_pelapor', 'like', "%{$search}%")
                            ->orWhere('no_tel_pelapor', 'like', "%{$search}%")
                            ->orWhere('burial_plot_code', 'like', "%{$search}%")
                            ->orWhere('burial_lot_no', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->get();

        $summary = [
            'total_orders' => $orders->count(),
            'total_amount' => $orders->sum('amount'),
            'adult_count' => $orders->where('category', 'dewasa')->count(),
            'child_count' => $orders->where('category', 'kanak-kanak')->count(),
            'generated_at' => now(),
            'reference_no' => 'KPN/' . now()->format('Ymd/His'),
            'status_label' => $status === 'approved' ? 'Diluluskan' : ucfirst($status),
        ];

        $pdf = Pdf::loadView('admin.grave-orders.pdf.work-order', [
            'orders' => $orders,
            'summary' => $summary,
        ])->setPaper('a4', 'portrait');

        $fileName = 'surat-arahan-kerja-tempahan-kepuk-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->stream($fileName);
    }

}