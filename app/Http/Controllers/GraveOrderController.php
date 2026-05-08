<?php

namespace App\Http\Controllers;

use App\Models\DeathReport;
use App\Models\GraveOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GraveOrderController extends Controller
{
    public function index()
    {
       $orders = GraveOrder::with(['deathReport', 'burialPlot'])
        ->where('user_id', Auth::id())
        ->orderByRaw("CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END")
        ->latest()
        ->paginate(10);

        return view('user.grave-orders.index', compact('orders'));
    }

    public function create()
    {
        $deathReports = DeathReport::with([
                'graveOrder',
                'burialPlot',
                'assignedBurialPlot',
                'dependent',
            ])
            ->where('user_id', Auth::id())
            ->where('status', 'disahkan')
            ->whereDoesntHave('graveOrder', function ($query) {
                $query->whereIn('status', ['pending', 'approved']);
            })
            ->latest()
            ->get();

        $orderOptions = GraveOrder::orderOptions();

        return view('user.grave-orders.create', compact('deathReports', 'orderOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'death_report_id' => ['required', 'exists:death_reports,id'],
            'category' => ['required', 'in:dewasa,kanak-kanak'],
            'order_type' => ['required', 'string'],
            'declaration' => ['accepted'],
        ], [
            'death_report_id.required' => 'Sila pilih nama si mati.',
            'death_report_id.exists' => 'Rekod si mati tidak sah.',
            'category.required' => 'Kategori tempahan diperlukan.',
            'category.in' => 'Kategori tempahan tidak sah.',
            'order_type.required' => 'Sila pilih jenis tempahan.',
            'declaration.accepted' => 'Sila sahkan perakuan sebelum menghantar permohonan.',
        ]);

        $deathReport = DeathReport::with(['burialPlot', 'assignedBurialPlot'])
            ->where('id', $request->death_report_id)
            ->where('user_id', Auth::id())
            ->where('status', 'disahkan')
            ->firstOrFail();

        $umur = is_numeric($deathReport->umur) ? (int) $deathReport->umur : null;
        $burialZone = strtoupper(trim($deathReport->burial_zone ?? ''));

        if ($burialZone === 'K') {
            $detectedCategory = 'kanak-kanak';
        } elseif (!is_null($umur) && $umur < 12) {
            $detectedCategory = 'kanak-kanak';
        } else {
            $detectedCategory = 'dewasa';
        }

        if ($request->category !== $detectedCategory) {
            return back()
                ->withInput()
                ->with('error', $detectedCategory === 'kanak-kanak'
                    ? 'Si mati ini dikategorikan sebagai kanak-kanak. Sila pilih tempahan kategori kanak-kanak sahaja.'
                    : 'Si mati ini dikategorikan sebagai dewasa. Sila pilih tempahan kategori dewasa sahaja.');
        }

        $alreadyExists = GraveOrder::where('death_report_id', $deathReport->id)
        ->whereIn('status', ['pending', 'approved'])
        ->exists();

        if ($alreadyExists) {
            return redirect()
                ->route('grave-orders.index')
                ->with('error', 'Permohonan tempahan untuk si mati ini telah wujud.');
        }

        $options = GraveOrder::orderOptions();

        if (!isset($options[$request->category][$request->order_type])) {
            return back()
                ->withInput()
                ->with('error', 'Jenis tempahan tidak sah.');
        }

        $selectedOption = $options[$request->category][$request->order_type];

        $finalBurialPlot = $deathReport->final_burial_plot;

        GraveOrder::create([
            'user_id' => Auth::id(),
            'death_report_id' => $deathReport->id,
            'burial_plot_id' => $finalBurialPlot?->id,
            'category' => $request->category,
            'order_type' => $request->order_type,
            'order_label' => $selectedOption['label'],
            'amount' => $selectedOption['amount'],
            'declaration' => true,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('grave-orders.index')
            ->with('success', 'Permohonan tempahan kepuk / nisan berjaya dihantar.');
    }

    public function show(GraveOrder $graveOrder)
    {
        abort_if($graveOrder->user_id !== Auth::id(), 403);

        $graveOrder->load(['deathReport', 'burialPlot']);

        return view('user.grave-orders.show', compact('graveOrder'));
    }
}