<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeathReport;
use App\Models\UserProfile;
use App\Models\Dependent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $matchedUserProfile = UserProfile::where('no_kp', $deathReport->no_kp_si_mati)->first();
        $matchedDependent = Dependent::where('no_kp', $deathReport->no_kp_si_mati)->first();

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
            'admin_notes' => ['required', 'string', 'max:1000'],
        ], [
            'verification_category.required' => 'Sila pilih kategori si mati.',
            'status.required' => 'Sila pilih status semakan.',
            'burial_lot_no.required_if' => 'No lot kubur wajib diisi apabila status disahkan.',
            'burial_date.required_if' => 'Tarikh kebumi wajib diisi apabila status disahkan.',
            'admin_notes.required' => 'Sila isi catatan pentadbir.',
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
            'admin_notes' => $validated['admin_notes'],
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        if ($validated['status'] === 'disahkan') {
            if ($validated['verification_category'] === 'ahli_khairat') {
                $profile = UserProfile::where('no_kp', $deathReport->no_kp_si_mati)->first();

                if ($profile) {
                    $profile->update([
                        'status_kehidupan' => 'meninggal',
                        'tarikh_kematian' => $deathReport->tarikh_meninggal,
                    ]);
                }
            }

            if ($validated['verification_category'] === 'tanggungan') {
                $dependent = Dependent::where('no_kp', $deathReport->no_kp_si_mati)->first();

                if ($dependent) {
                    $dependent->update([
                        'status_kehidupan' => 'meninggal',
                        'tarikh_kematian' => $deathReport->tarikh_meninggal,
                    ]);
                }
            }
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
}