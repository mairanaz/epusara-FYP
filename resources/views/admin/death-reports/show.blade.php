@extends('layouts.app')

@section('content')
<div class="container-fluid">

    @php
        $statusMap = [
            'menunggu_semakan' => ['label' => 'Menunggu Semakan', 'class' => 'warning'],
            'disahkan' => ['label' => 'Disahkan', 'class' => 'success'],
            'perlukan_dokumen_tambahan' => ['label' => 'Perlukan Dokumen Tambahan', 'class' => 'info'],
            'ditolak' => ['label' => 'Ditolak', 'class' => 'danger'],
        ];

        $recordType = [
            'label' => 'Belum Ditentukan',
            'class' => 'warning',
            'summary' => 'Pentadbir perlu menentukan kategori rekod berdasarkan semakan maklumat dan dokumen.',
        ];

        if ($deathReport->verification_category === 'ahli_khairat') {
            $recordType = [
                'label' => 'Ahli Khairat',
                'class' => 'success',
                'summary' => 'Rekod telah disahkan sebagai ahli khairat.',
            ];
        } elseif ($deathReport->verification_category === 'tanggungan') {
            $recordType = [
                'label' => 'Tanggungan Berdaftar',
                'class' => 'primary',
                'summary' => 'Rekod telah disahkan sebagai tanggungan berdaftar.',
            ];
        } elseif ($deathReport->verification_category === 'bukan_ahli') {
            $recordType = [
                'label' => 'Bukan Ahli',
                'class' => 'secondary',
                'summary' => 'Rekod telah disahkan sebagai bukan ahli.',
            ];
        } elseif ($deathReport->verification_category === 'warga_asing') {
            $recordType = [
                'label' => 'Warga Asing',
                'class' => 'dark',
                'summary' => 'Rekod telah disahkan sebagai warga asing.',
            ];
        } elseif ($matchedUserProfile) {
            $recordType = [
                'label' => 'Ahli Khairat Berdaftar',
                'class' => 'success',
                'summary' => 'Padanan rekod ahli dijumpai dalam sistem.',
            ];
        } elseif ($matchedDependent) {
            $recordType = [
                'label' => 'Tanggungan Berdaftar',
                'class' => 'primary',
                'summary' => 'Padanan rekod tanggungan dijumpai dalam sistem.',
            ];
        }

        $currentStatus = $statusMap[$deathReport->status] ?? [
            'label' => 'Belum Disemak',
            'class' => 'warning'
        ];
    @endphp

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Semakan Laporan Kematian</h1>
            <div class="text-muted">
                Rekod laporan untuk semakan pentadbir
            </div>
        </div>

        <div class="mt-3 mt-md-0 d-flex flex-wrap gap-2">
            <span class="badge bg-{{ $recordType['class'] }} fs-12 px-3 py-2">
                Jenis Rekod: {{ $recordType['label'] }}
            </span>
            <span class="badge bg-{{ $currentStatus['class'] }} fs-12 px-3 py-2">
                Status: {{ $currentStatus['label'] }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berjaya',
                    text: @json(session('success')),
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif

    @if($errors->any())
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Semakan tidak berjaya',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonText: 'Tutup'
                });
            });
        </script>
    @endif

    {{-- Ringkasan atas --}}
    <div class="card custom-card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-lg-8">
                    <h3 class="fw-semibold mb-1">{{ $deathReport->nama_si_mati }}</h3>
                    <div class="text-muted mb-3">No. KP: {{ $deathReport->no_kp_si_mati }}</div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted fs-12 mb-1">Tarikh Meninggal</div>
                                <div class="fw-semibold">
                                    {{ optional($deathReport->tarikh_meninggal)->format('d/m/Y') ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted fs-12 mb-1">Tarikh Laporan</div>
                                <div class="fw-semibold">
                                    {{ optional($deathReport->created_at)->format('d/m/Y h:i A') ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted fs-12 mb-1">No Permit Kebumi</div>
                                <div class="fw-semibold">
                                    {{ $deathReport->no_permit_kebumi ?: '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="border rounded p-4 bg-light">
                        <div class="text-muted fs-12 mb-1">Jenis Rekod</div>
                        <div class="mb-3">
                            <span class="badge bg-{{ $recordType['class'] }} fs-13 px-3 py-2">
                                {{ $recordType['label'] }}
                            </span>
                        </div>

                        <div class="text-muted fs-12 mb-1">Rumusan</div>
                        <div class="fw-semibold mb-3">
                            {{ $recordType['summary'] }}
                        </div>

                        <div class="text-muted fs-12 mb-1">Status Laporan</div>
                        <div>
                            <span class="badge bg-{{ $currentStatus['class'] }} fs-13 px-3 py-2">
                                {{ $currentStatus['label'] }}
                            </span>
                        </div>

                        @if($deathReport->verified_at)
                            <hr>
                            <div class="text-muted fs-12 mb-1">Disemak pada</div>
                            <div class="fw-semibold">{{ optional($deathReport->verified_at)->format('d/m/Y h:i A') }}</div>
                        @endif

                        @if($deathReport->verifier)
                            <div class="text-muted fs-12 mt-3 mb-1">Disemak oleh</div>
                            <div class="fw-semibold">{{ $deathReport->verifier->name }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- LEFT --}}
        <div class="col-xl-7">

            {{-- Maklumat Si Mati --}}
            <div class="card custom-card mb-4 border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Maklumat Si Mati</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted fs-12 d-block mb-1">Nama Penuh</label>
                            <div class="fw-semibold">{{ $deathReport->nama_si_mati ?: '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted fs-12 d-block mb-1">No KP</label>
                            <div class="fw-semibold">{{ $deathReport->no_kp_si_mati ?: '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted fs-12 d-block mb-1">Jantina</label>
                            <div class="fw-semibold">{{ $deathReport->jantina ?: '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted fs-12 d-block mb-1">Umur</label>
                            <div class="fw-semibold">{{ $deathReport->umur ?: '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted fs-12 d-block mb-1">Jenis Si Mati</label>
                            <div class="fw-semibold">
                                {{ $deathReport->deceased_type === 'member' ? 'Ahli Utama' : ($deathReport->deceased_type === 'dependent' ? 'Tanggungan' : '-') }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted fs-12 d-block mb-1">Tarikh Meninggal</label>
                            <div class="fw-semibold">{{ optional($deathReport->tarikh_meninggal)->format('d/m/Y') ?: '-' }}</div>
                        </div>

                        <div class="col-12">
                            <label class="text-muted fs-12 d-block mb-1">Alamat Terakhir</label>
                            <div class="fw-semibold">{{ $deathReport->alamat_terakhir ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dokumen --}}
            <div class="card custom-card mb-4 border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Dokumen Sokongan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="fw-semibold mb-2">Sijil Mati</div>
                                @if($deathReport->sijil_mati_path)
                                    <a href="{{ route('admin.death-reports.preview', [$deathReport, 'sijil_mati']) }}"
                                    target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                        Preview Dokumen
                                    </a>
                                    <div class="text-muted fs-12 mt-2">Klik untuk buka dokumen</div>
                                @else
                                    <div class="text-muted">Tiada dokumen dimuat naik</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="fw-semibold mb-2">Permit Kebumi</div>
                                @if($deathReport->permit_kebumi_path)
                                    <a href="{{ route('admin.death-reports.preview', [$deathReport, 'permit_kebumi']) }}"
                                    target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                        Preview Dokumen
                                    </a>
                                    <div class="text-muted fs-12 mt-2">Klik untuk buka dokumen</div>
                                @else
                                    <div class="text-muted">Tiada dokumen dimuat naik</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="fw-semibold mb-2">Dokumen Sokongan</div>
                                @if($deathReport->dokumen_sokongan_path)
                                    <a href="{{ route('admin.death-reports.preview', [$deathReport, 'dokumen_sokongan']) }}"
                                    target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                        Preview Dokumen
                                    </a>
                                    <div class="text-muted fs-12 mt-2">Klik untuk buka dokumen</div>
                                @else
                                    <div class="text-muted">Tiada dokumen dimuat naik</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Maklumat Pelapor --}}
            <div class="card custom-card mb-4 border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Maklumat Pelapor / Waris</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted fs-12 d-block mb-1">Nama Pelapor</label>
                            <div class="fw-semibold">{{ $deathReport->nama_pelapor ?: '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted fs-12 d-block mb-1">No KP Pelapor</label>
                            <div class="fw-semibold">{{ $deathReport->no_kp_pelapor ?: '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted fs-12 d-block mb-1">No Telefon</label>
                            <div class="fw-semibold">{{ $deathReport->no_tel_pelapor ?: '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted fs-12 d-block mb-1">Pertalian dengan Si Mati</label>
                            <div class="fw-semibold">{{ $deathReport->pertalian_pelapor ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT --}}
        <div class="col-xl-5">

            {{-- Rumusan Padanan Sistem --}}
            <div class="card custom-card mb-4 border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Rumusan Padanan Sistem</h5>
                </div>
                <div class="card-body">

                    @if($matchedUserProfile)
                        <div class="alert alert-success mb-3">
                            <strong>Status Padanan:</strong> Rekod ahli khairat dijumpai dalam sistem.
                        </div>

                        <div class="border rounded p-3 bg-light">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="text-muted fs-12 mb-1">Jenis Rekod</div>
                                    <div class="fw-semibold">Ahli Khairat</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted fs-12 mb-1">Status Kehidupan</div>
                                    <div class="fw-semibold">{{ $matchedUserProfile->status_kehidupan ?? 'aktif' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted fs-12 mb-1">Nama Ahli</div>
                                    <div class="fw-semibold">{{ $matchedUserProfile->nama }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted fs-12 mb-1">No KP</div>
                                    <div class="fw-semibold">{{ $matchedUserProfile->no_kp }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted fs-12 mb-1">No Telefon Waris</div>
                                    <div class="fw-semibold">{{ $matchedUserProfile->no_tel_waris ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted fs-12 mb-1">Status Permohonan</div>
                                    <div class="fw-semibold">{{ $matchedUserProfile->status_permohonan ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    @elseif($matchedDependent)
                        <div class="alert alert-primary mb-3">
                            <strong>Status Padanan:</strong> Rekod tanggungan dijumpai dalam sistem.
                        </div>

                        <div class="border rounded p-3 bg-light mb-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="text-muted fs-12 mb-1">Jenis Rekod</div>
                                    <div class="fw-semibold">Tanggungan Berdaftar</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted fs-12 mb-1">Pertalian</div>
                                    <div class="fw-semibold">{{ $matchedDependent->pertalian ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted fs-12 mb-1">Nama Tanggungan</div>
                                    <div class="fw-semibold">{{ $matchedDependent->name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted fs-12 mb-1">No KP Tanggungan</div>
                                    <div class="fw-semibold">{{ $matchedDependent->no_kp }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted fs-12 mb-1">No Telefon</div>
                                    <div class="fw-semibold">{{ $matchedDependent->no_tel ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted fs-12 mb-1">Status Kehidupan</div>
                                    <div class="fw-semibold">{{ $matchedDependent->status_kehidupan ?? 'aktif' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded p-3">
                            <div class="fw-semibold mb-3">Maklumat Ahli Utama Berkaitan</div>

                            @if(isset($principalMember) && $principalMember)
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="text-muted fs-12 mb-1">Nama Ahli Utama</div>
                                        <div class="fw-semibold">{{ $principalMember->nama ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted fs-12 mb-1">No KP Ahli Utama</div>
                                        <div class="fw-semibold">{{ $principalMember->no_kp ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted fs-12 mb-1">Status Kehidupan</div>
                                        <div class="fw-semibold">{{ $principalMember->status_kehidupan ?? 'aktif' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted fs-12 mb-1">Status Permohonan</div>
                                        <div class="fw-semibold">{{ $principalMember->status_permohonan ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted fs-12 mb-1">No Telefon Waris</div>
                                        <div class="fw-semibold">{{ $principalMember->no_tel_waris ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted fs-12 mb-1">Pelan Bayaran</div>
                                        <div class="fw-semibold">{{ $principalMember->payment_plan ?? '-' }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="text-muted">
                                    Maklumat ahli utama tidak dijumpai.
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            Tiada padanan rekod dijumpai dalam sistem. Pentadbir perlu menentukan sama ada si mati ialah bukan ahli atau warga asing.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tindakan Pentadbir --}}
            <div class="card custom-card border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Tindakan Pentadbir</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.death-reports.verify', $deathReport) }}" method="POST" id="adminReviewForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Kategori Si Mati <span class="text-danger">*</span></label>
                            <select name="verification_category" id="verification_category" class="form-control" required>
                                <option value="">-- Pilih --</option>
                                <option value="ahli_khairat"
                                    {{ old('verification_category', $deathReport->verification_category ?? ($matchedUserProfile ? 'ahli_khairat' : '')) == 'ahli_khairat' ? 'selected' : '' }}>
                                    Ahli Khairat
                                </option>
                                <option value="tanggungan"
                                    {{ old('verification_category', $deathReport->verification_category ?? ($matchedDependent ? 'tanggungan' : '')) == 'tanggungan' ? 'selected' : '' }}>
                                    Tanggungan
                                </option>
                                <option value="bukan_ahli"
                                    {{ old('verification_category', $deathReport->verification_category) == 'bukan_ahli' ? 'selected' : '' }}>
                                    Bukan Ahli
                                </option>
                                <option value="warga_asing"
                                    {{ old('verification_category', $deathReport->verification_category) == 'warga_asing' ? 'selected' : '' }}>
                                    Warga Asing
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status Semakan <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="">-- Pilih --</option>
                                <option value="disahkan" {{ old('status', $deathReport->status) == 'disahkan' ? 'selected' : '' }}>
                                    Disahkan
                                </option>
                                <option value="perlukan_dokumen_tambahan" {{ old('status', $deathReport->status) == 'perlukan_dokumen_tambahan' ? 'selected' : '' }}>
                                    Perlukan Dokumen Tambahan
                                </option>
                                <option value="ditolak" {{ old('status', $deathReport->status) == 'ditolak' ? 'selected' : '' }}>
                                    Ditolak
                                </option>
                            </select>
                            <small class="text-muted">
                                Pilih status semakan berdasarkan dokumen dan padanan rekod.
                            </small>
                        </div>

                        <div id="burialFields">
                            <div class="mb-3">
                                <label class="form-label">No Lot Kubur</label>
                                <input type="text"
                                       name="burial_lot_no"
                                       id="burial_lot_no"
                                       class="form-control"
                                       value="{{ old('burial_lot_no', $deathReport->burial_lot_no) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tarikh Kebumi</label>
                                <input type="date"
                                       name="burial_date"
                                       id="burial_date"
                                       class="form-control"
                                       value="{{ old('burial_date', optional($deathReport->burial_date)->format('Y-m-d')) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan Pentadbir <span class="text-danger">*</span></label>
                            <textarea name="admin_notes"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Masukkan catatan semakan pentadbir...">{{ old('admin_notes', $deathReport->admin_notes) }}</textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                Simpan Semakan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusField = document.getElementById('status');
        const burialFields = document.getElementById('burialFields');
        const burialLot = document.getElementById('burial_lot_no');
        const burialDate = document.getElementById('burial_date');

        function toggleBurialFields() {
            if (statusField.value === 'disahkan') {
                burialFields.style.display = 'block';
                burialLot.disabled = false;
                burialDate.disabled = false;
            } else {
                burialFields.style.display = 'none';
                burialLot.disabled = true;
                burialDate.disabled = true;
                burialLot.value = '';
                burialDate.value = '';
            }
        }

        toggleBurialFields();
        statusField.addEventListener('change', toggleBurialFields);
    });
</script>
@endsection