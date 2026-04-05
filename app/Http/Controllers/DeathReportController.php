<?php

namespace App\Http\Controllers;

use App\Models\DeathReport;
use App\Models\Dependent;
use Illuminate\Http\Request;

class DeathReportController extends Controller
{
    public function create()
    {
        return view('death-reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'deceased_type' => ['required', 'in:member,dependent'],
            'nama_si_mati' => ['required', 'string', 'max:255'],
            'no_kp_si_mati' => ['required', 'string', 'max:20'],
            'jantina' => ['nullable', 'string', 'max:20'],
            'alamat_terakhir' => ['nullable', 'string'],
            'tarikh_meninggal' => ['required', 'date'],
            'umur' => ['nullable', 'integer', 'min:0'],
            'no_permit_kebumi' => ['nullable', 'string', 'max:100'],

            'nama_pelapor' => ['required', 'string', 'max:255'],
            'no_kp_pelapor' => ['nullable', 'string', 'max:20'],
            'no_tel_pelapor' => ['required', 'string', 'max:20'],
            'pertalian_pelapor' => ['nullable', 'string', 'max:100'],

            'sijil_mati' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'permit_kebumi' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'dokumen_sokongan' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $data = [
            'deceased_type' => $validated['deceased_type'],
            'nama_si_mati' => $validated['nama_si_mati'],
            'no_kp_si_mati' => $validated['no_kp_si_mati'],
            'jantina' => $validated['jantina'] ?? null,
            'alamat_terakhir' => $validated['alamat_terakhir'] ?? null,
            'tarikh_meninggal' => $validated['tarikh_meninggal'],
            'umur' => $validated['umur'] ?? null,
            'no_permit_kebumi' => $validated['no_permit_kebumi'] ?? null,

            'nama_pelapor' => $validated['nama_pelapor'],
            'no_kp_pelapor' => $validated['no_kp_pelapor'] ?? null,
            'no_tel_pelapor' => $validated['no_tel_pelapor'],
            'pertalian_pelapor' => $validated['pertalian_pelapor'] ?? null,

            'status' => 'menunggu_semakan',
        ];

        if ($request->hasFile('sijil_mati')) {
            $data['sijil_mati_path'] = $request->file('sijil_mati')->store('death-reports', 'public');
        }

        if ($request->hasFile('permit_kebumi')) {
            $data['permit_kebumi_path'] = $request->file('permit_kebumi')->store('death-reports', 'public');
        }

        if ($request->hasFile('dokumen_sokongan')) {
            $data['dokumen_sokongan_path'] = $request->file('dokumen_sokongan')->store('death-reports', 'public');
        }

        DeathReport::create($data);

        return redirect()->route('death-report.create')
            ->with('success', 'Laporan kematian berjaya dihantar dan sedang menunggu semakan pentadbir.');
    }

    

}