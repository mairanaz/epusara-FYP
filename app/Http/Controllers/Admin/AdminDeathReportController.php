<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeathReport;
use App\Models\UserProfile;
use App\Models\Dependent;
use App\Models\BurialPlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminDeathReportController extends Controller
{
    public function index(Request $request)
    {
        $query = DeathReport::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('nama_si_mati', 'like', '%' . $search . '%')
                    ->orWhere('no_kp_si_mati', 'like', '%' . $search . '%')
                    ->orWhere('nama_pelapor', 'like', '%' . $search . '%')
                    ->orWhere('no_tel_pelapor', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('verification_category')) {
            $query->where('verification_category', $request->verification_category);
        }

        $deathReports = $query->latest()->paginate(10)->withQueryString();

        $summary = [
            'total' => DeathReport::count(),
            'pending' => DeathReport::where('status', 'menunggu_semakan')->count(),
            'approved' => DeathReport::where('status', 'disahkan')->count(),
            'need_docs' => DeathReport::where('status', 'perlukan_dokumen_tambahan')->count(),
        ];

        return view('admin.death-reports.index', compact('deathReports', 'summary'));
    }

   public function show(DeathReport $deathReport)
    {
        $deathReport->load('verifier');

        $noKp = preg_replace('/\D/', '', $deathReport->no_kp_si_mati);

        $matchedUserProfile = UserProfile::get()->first(function ($item) use ($noKp) {
            return preg_replace('/\D/', '', $item->no_kp) === $noKp;
        });

        $matchedDependent = Dependent::get()->first(function ($item) use ($noKp) {
            return preg_replace('/\D/', '', $item->no_kp) === $noKp;
        });

        $principalMember = null;

        if ($matchedDependent) {
            $principalMember = UserProfile::where('user_id', $matchedDependent->user_id)->first();
        }

        return view('admin.death-reports.show', compact(
            'deathReport',
            'matchedUserProfile',
            'matchedDependent',
            'principalMember'
        ));
    }

    public function verify(Request $request, DeathReport $deathReport)
    {
        $validated = $request->validate([
            'verification_category' => ['required', 'in:ahli_khairat,tanggungan,bukan_ahli,warga_asing'],
            'status' => ['required', 'in:disahkan,perlukan_dokumen_tambahan,ditolak'],
            'burial_lot_no' => ['nullable', 'string', 'max:100', 'required_if:status,disahkan'],
            'burial_date' => ['nullable', 'date', 'required_if:status,disahkan'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'verification_category.required' => 'Sila pilih kategori si mati.',
            'status.required' => 'Sila pilih status semakan.',
            'burial_lot_no.required_if' => 'No lot kubur wajib diisi apabila status disahkan.',
            'burial_date.required_if' => 'Tarikh kebumi wajib diisi apabila status disahkan.',
        ]);

        $deathReport->update([
            'verification_category' => $validated['verification_category'],
            'status' => $validated['status'],
            'burial_lot_no' => $validated['status'] === 'disahkan'
                ? ($validated['burial_lot_no'] ?? null)
                : null,
            'burial_date' => $validated['status'] === 'disahkan'
                ? ($validated['burial_date'] ?? null)
                : null,
            'admin_notes' => $validated['admin_notes'] ?? null,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        if ($validated['status'] === 'disahkan') {
            $this->syncDeathStatus($deathReport->fresh());
        }

        return redirect()
            ->route('admin.death-reports.show', $deathReport)
            ->with('success', 'Semakan laporan kematian berjaya dikemaskini.');
    }

    public function preview(DeathReport $deathReport, $type)
    {
        $path = match ($type) {
            'sijil_mati' => $deathReport->sijil_mati_path,
            'permit_kebumi' => $deathReport->permit_kebumi_path,
            'dokumen_sokongan' => $deathReport->dokumen_sokongan_path,
            default => null,
        };

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'Fail tidak dijumpai.');
        }

        $fullPath = Storage::disk('public')->path($path);
        $mimeType = Storage::disk('public')->mimeType($path) ?? 'application/octet-stream';

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
        ]);
    }

    private function determineZone($deathReport)
    {
        $jantina = strtolower(trim($deathReport->jantina ?? ''));
        $umur = is_numeric($deathReport->umur) ? (int) $deathReport->umur : null;

        if (!is_null($umur) && $umur <= 12) {
            return 'K';
        }

        if (in_array($jantina, ['perempuan', 'female'])) {
            return 'P';
        }

        if (in_array($jantina, ['lelaki', 'male'])) {
            return 'L';
        }

        return null;
    }

    public function selectPlot(DeathReport $deathReport)
    {
        if ($deathReport->burial_plot_id) {
            return redirect()
                ->route('admin.death-reports.show', $deathReport)
                ->with('success', 'Lot kubur telah dipilih untuk laporan ini.');
        }

        $zone = $this->determineZone($deathReport);

        if (!$zone) {
            return redirect()
                ->route('admin.death-reports.show', $deathReport)
                ->with('error', 'Zon kubur tidak dapat ditentukan. Sila semak jantina atau umur si mati.');
        }

        $plots = BurialPlot::where('zone', $zone)
            ->orderBy('row_number')
            ->orderBy('lot_number')
            ->get()
            ->groupBy('row_number');

        return view('admin.death-reports.select-plot', compact('deathReport', 'zone', 'plots'));
    }

    public function storePlot(Request $request, DeathReport $deathReport)
    {
        $validated = $request->validate([
            'burial_plot_id' => ['required', 'exists:burial_plots,id'],
            'burial_date' => ['required', 'date'],
        ], [
            'burial_plot_id.required' => 'Sila pilih lot kubur.',
            'burial_plot_id.exists' => 'Lot kubur yang dipilih tidak sah.',
            'burial_date.required' => 'Sila pilih tarikh kebumi.',
            'burial_date.date' => 'Tarikh kebumi tidak sah.',
        ]);

        if ($deathReport->burial_plot_id) {
            return redirect()
                ->route('admin.death-reports.show', $deathReport)
                ->with('error', 'Laporan ini sudah mempunyai lot kubur. Setiap laporan hanya dibenarkan satu lot kubur sahaja.');
        }

        $zone = $this->determineZone($deathReport);

        if (!$zone) {
            return back()
                ->withInput()
                ->with('error', 'Zon kubur tidak dapat ditentukan. Sila semak jantina atau umur si mati.');
        }

        try {
            DB::transaction(function () use ($validated, $deathReport, $zone) {

                $plot = BurialPlot::where('id', $validated['burial_plot_id'])
                    ->where('zone', $zone)
                    ->lockForUpdate()
                    ->first();

                if (!$plot) {
                    throw new \Exception('Lot kubur tidak sepadan dengan zon si mati.');
                }

                if ($plot->status !== 'available') {
                    throw new \Exception('Lot kubur ini telah digunakan. Sila pilih lot lain.');
                }

                if (!is_null($plot->death_report_id)) {
                    throw new \Exception('Lot kubur ini telah mempunyai rekod laporan kematian.');
                }

                $plot->update([
                    'status' => 'occupied',
                    'death_report_id' => $deathReport->id,
                    'buried_at' => $validated['burial_date'],
                ]);

                $deathReport->update([
                    'burial_plot_id' => $plot->id,
                    'burial_zone' => $plot->zone,
                    'burial_plot_code' => $plot->plot_code,
                    'burial_lot_no' => $plot->plot_code,
                    'tarikh_kebumi' => $validated['burial_date'],
                    'burial_date' => $validated['burial_date'],
                    'status' => 'disahkan',
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);
            });

            $this->syncDeathStatus($deathReport->fresh());

            return redirect()
                ->route('admin.death-reports.show', $deathReport)
                ->with('success', 'Lot kubur berjaya dipilih dan disimpan.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    private function syncDeathStatus(DeathReport $deathReport): void
    {
        if ($deathReport->status !== 'disahkan') {
            return;
        }

        if ($deathReport->dependent_id) {
            $dependent = Dependent::find($deathReport->dependent_id);

            if ($dependent) {
                $dependent->update([
                    'status_kehidupan' => 'meninggal_dunia',
                    'tarikh_kematian' => $deathReport->tarikh_meninggal,
                ]);
            }
        }

        if ($deathReport->user_id && $deathReport->deceased_type === 'member') {
            $profile = UserProfile::where('user_id', $deathReport->user_id)->first();

            if ($profile) {
                $profile->update([
                    'status_kehidupan' => 'meninggal_dunia',
                    'tarikh_kematian' => $deathReport->tarikh_meninggal,
                ]);
            }
        }
    }

}