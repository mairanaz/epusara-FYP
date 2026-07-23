<?php

namespace App\Http\Controllers;

use App\Models\DeathReport;
use App\Models\GraveOrder;
use App\Models\Dependent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GraveOrderController extends Controller
{
    public function index()
    {
        $mainUserId = $this->getMainUserId();

        $orders = GraveOrder::with([
                'user.profile',
                'deathReport.dependent',
                'deathReport.burialPlot',
                'deathReport.assignedBurialPlot',
                'burialPlot',
            ])
            ->whereHas('deathReport', function ($query) use ($mainUserId) {
                $query->where('user_id', $mainUserId)
                    ->orWhereHas('dependent', function ($q) use ($mainUserId) {
                        $q->where('user_id', $mainUserId);
                    });
            })
            ->orderByRaw("CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END")
            ->latest()
            ->paginate(10);

        return view('user.grave-orders.index', compact('orders'));
    }

    public function create()
    {
        $mainUserId = $this->getMainUserId();

        $deathReports = DeathReport::with([
                'graveOrder',
                'burialPlot',
                'assignedBurialPlot',
                'dependent',
            ])
            ->where('status', 'disahkan')
            ->whereDoesntHave('graveOrder', function ($query) {
                $query->whereIn('status', ['pending', 'approved']);
            })
            ->where(function ($query) use ($mainUserId) {
                $query->where('user_id', $mainUserId)
                    ->orWhereHas('dependent', function ($q) use ($mainUserId) {
                        $q->where('user_id', $mainUserId);
                    });
            })
            ->latest()
            ->get()
            ->filter(function ($report) {
                /*
                |--------------------------------------------------------------------------
                | Tapis si mati yang tiada lot kubur RTB
                |--------------------------------------------------------------------------
                | Jika si mati kebumi di luar RTB, final_burial_plot akan null.
                | Jadi nama tersebut tidak akan dipaparkan dalam dropdown tempahan
                | kepuk / batu nisan.
                */
                return !is_null($report->final_burial_plot);
            })
            ->values();

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

        $mainUserId = $this->getMainUserId();

        $deathReport = DeathReport::with([
                'burialPlot',
                'assignedBurialPlot',
                'dependent',
                'graveOrder',
            ])
            ->where('id', $request->death_report_id)
            ->where('status', 'disahkan')
            ->where(function ($query) use ($mainUserId) {
                $query->where('user_id', $mainUserId)
                    ->orWhereHas('dependent', function ($q) use ($mainUserId) {
                        $q->where('user_id', $mainUserId);
                    });
            })
            ->firstOrFail();

        $finalBurialPlot = $deathReport->final_burial_plot;

        /*
        |--------------------------------------------------------------------------
        | Validation penting: mesti ada lot kubur RTB
        |--------------------------------------------------------------------------
        | Ini elak user pilih / paksa submit death_report_id si mati yang
        | dikebumikan di luar RTB.
        */
        if (!$finalBurialPlot) {
            return back()
                ->withInput()
                ->with('error', 'Tempahan kepuk / nisan hanya boleh dibuat untuk si mati yang mempunyai lot kubur di RTB Bukit Changgang.');
        }

        $alreadyExists = GraveOrder::where('death_report_id', $deathReport->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($alreadyExists) {
            return redirect()
                ->route('grave-orders.index')
                ->with('error', 'Permohonan tempahan untuk si mati ini telah wujud.');
        }

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

        $options = GraveOrder::orderOptions();

        if (!isset($options[$request->category][$request->order_type])) {
            return back()
                ->withInput()
                ->with('error', 'Jenis tempahan tidak sah.');
        }

        $selectedOption = $options[$request->category][$request->order_type];

        GraveOrder::create([
            'user_id' => Auth::id(),
            'death_report_id' => $deathReport->id,
            'burial_plot_id' => $finalBurialPlot->id,
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
        $mainUserId = $this->getMainUserId();

        $graveOrder->load([
            'user.profile',
            'deathReport.dependent',
            'deathReport.burialPlot',
            'deathReport.assignedBurialPlot',
            'burialPlot',
        ]);

        $isFamilyOrder = false;

        if ($graveOrder->deathReport) {
            if ($graveOrder->deathReport->user_id == $mainUserId) {
                $isFamilyOrder = true;
            }

            if (
                $graveOrder->deathReport->dependent &&
                $graveOrder->deathReport->dependent->user_id == $mainUserId
            ) {
                $isFamilyOrder = true;
            }
        }

        abort_if(!$isFamilyOrder, 403);

        return view('user.grave-orders.show', compact('graveOrder'));
    }

    private function getMainUserId()
    {
        $user = Auth::user();

        if ($user->linked_dependent_id) {
            $dependent = Dependent::find($user->linked_dependent_id);

            if ($dependent) {
                return $dependent->user_id;
            }
        }

        return $user->id;
    }
}