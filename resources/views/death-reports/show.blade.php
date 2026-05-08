@extends('layouts.app')

@section('content')
<div class="container-fluid">

    @php
        function statusBadgeClass($status) {
            return match($status) {
                'menunggu_semakan' => 'warning',
                'disahkan' => 'success',
                'ditolak' => 'danger',
                'perlukan_dokumen_tambahan' => 'info',
                default => 'secondary',
            };
        }

        function statusLabel($status) {
            return match($status) {
                'menunggu_semakan' => 'Menunggu Semakan',
                'disahkan' => 'Disahkan',
                'ditolak' => 'Ditolak',
                'perlukan_dokumen_tambahan' => 'Perlukan Dokumen Tambahan',
                default => ucfirst(str_replace('_', ' ', $status)),
            };
        }

        function categoryLabel($category) {
            return match($category) {
                'member' => 'Ahli Utama',
                'dependent' => 'Tanggungan',
                default => '-',
            };
        }

        function referenceNo($report) {
            $year = optional($report->created_at)->format('Y') ?? now()->format('Y');
            return 'LRK-' . $year . '-' . str_pad($report->id, 4, '0', STR_PAD_LEFT);
        }

        $timeline = [
            [
                'title' => 'Laporan Dihantar',
                'date' => optional($deathReport->created_at)->format('d/m/Y h:i A'),
                'done' => !empty($deathReport->created_at),
                'description' => 'Laporan kematian telah berjaya dihantar oleh pelapor.'
            ],
            [
                'title' => 'Menunggu Semakan Pentadbir',
                'date' => $deathReport->status === 'menunggu_semakan' ? optional($deathReport->updated_at)->format('d/m/Y h:i A') : null,
                'done' => in_array($deathReport->status, ['menunggu_semakan', 'disahkan', 'ditolak', 'perlukan_dokumen_tambahan']),
                'description' => 'Laporan sedang menunggu semakan dan pengesahan pihak pentadbir.'
            ],
            [
                'title' => $deathReport->status === 'ditolak'
                    ? 'Laporan Ditolak'
                    : ($deathReport->status === 'perlukan_dokumen_tambahan'
                        ? 'Dokumen Tambahan Diperlukan'
                        : 'Laporan Disahkan'),
                'date' => $deathReport->verified_at ? \Carbon\Carbon::parse($deathReport->verified_at)->format('d/m/Y h:i A') : null,
                'done' => in_array($deathReport->status, ['disahkan', 'ditolak', 'perlukan_dokumen_tambahan']),
                'description' => $deathReport->status === 'ditolak'
                    ? 'Laporan ini telah ditolak oleh pentadbir.'
                    : ($deathReport->status === 'perlukan_dokumen_tambahan'
                        ? 'Pentadbir memerlukan dokumen atau maklumat tambahan.'
                        : 'Laporan ini telah disahkan oleh pentadbir.')
            ],
            [
                'title' => 'Lot Kubur Ditetapkan',
                'date' => $deathReport->burial_plot_code || $deathReport->burial_lot_no
                    ? optional($deathReport->updated_at)->format('d/m/Y h:i A')
                    : null,
                'done' => !empty($deathReport->burial_plot_code) || !empty($deathReport->burial_lot_no),
                'description' => 'Maklumat lot kubur telah direkodkan ke dalam sistem.'
            ],
            [
                'title' => 'Tarikh Kebumi Direkodkan',
                'date' => $deathReport->tarikh_kebumi
                    ? \Carbon\Carbon::parse($deathReport->tarikh_kebumi)->format('d/m/Y')
                    : ($deathReport->burial_date
                        ? \Carbon\Carbon::parse($deathReport->burial_date)->format('d/m/Y')
                        : null),
                'done' => !empty($deathReport->tarikh_kebumi) || !empty($deathReport->burial_date),
                'description' => 'Tarikh kebumi telah direkodkan untuk rujukan.'
            ],
        ];
    @endphp

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-20 mb-1">Butiran Laporan Kematian</h1>
            <p class="text-muted mb-0">
                Paparan terperinci bagi laporan kematian yang telah dihantar.
            </p>
        </div>

        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('death-reports.index') }}" class="btn btn-light border">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
            <a href="{{ route('death-reports.create') }}" class="btn btn-info">
                <i class="bx bx-plus me-1"></i> Lapor Baharu
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm border-0">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger shadow-sm border-0">
            {{ session('error') }}
        </div>
    @endif

    <div class="card custom-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center">
                <div class="col-xl-8">
                    <div class="mb-2 text-muted small">No. Rujukan Laporan</div>
                    <h3 class="fw-bold mb-2">{{ referenceNo($deathReport) }}</h3>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-{{ statusBadgeClass($deathReport->status) }} fs-12">
                            {{ statusLabel($deathReport->status) }}
                        </span>

                        <span class="badge bg-light text-dark border fs-12">
                            {{ categoryLabel($deathReport->deceased_type) }}
                        </span>

                        @if($deathReport->burial_plot_code || $deathReport->burial_lot_no)
                            <span class="badge bg-light text-dark border fs-12">
                                Lot: {{ $deathReport->burial_plot_code ?? $deathReport->burial_lot_no }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="border rounded-3 p-3 bg-light-subtle">
                        <div class="mb-2">
                            <span class="text-muted small d-block">Tarikh & Masa Hantar</span>
                            <span class="fw-semibold">{{ optional($deathReport->created_at)->format('d/m/Y h:i A') ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Tarikh Semakan Pentadbir</span>
                            <span class="fw-semibold">
                                {{ $deathReport->verified_at ? \Carbon\Carbon::parse($deathReport->verified_at)->format('d/m/Y h:i A') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">

            <div class="card custom-card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">Maklumat Si Mati</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Nama Penuh</label>
                            <div class="fw-semibold">{{ $deathReport->nama_si_mati ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">No. Kad Pengenalan</label>
                            <div class="fw-semibold">{{ $deathReport->no_kp_si_mati ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Jantina</label>
                            <div class="fw-semibold">{{ $deathReport->jantina ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Umur</label>
                            <div class="fw-semibold">{{ $deathReport->umur ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Kategori Laporan</label>
                            <div class="fw-semibold">{{ categoryLabel($deathReport->deceased_type) }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Tarikh Meninggal</label>
                            <div class="fw-semibold">
                                {{ $deathReport->tarikh_meninggal ? \Carbon\Carbon::parse($deathReport->tarikh_meninggal)->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-muted mb-1">Tempat Kematian</label>
                            <div class="fw-semibold">{{ $deathReport->alamat_terakhir ?? '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Sebab Kematian</label>
                            <div class="fw-semibold">{{ $deathReport->sebab_kematian ?? '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">No. Permit Kebumi</label>
                            <div class="fw-semibold">{{ $deathReport->no_permit_kebumi ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card custom-card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">Pengurusan Jenazah</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Lokasi Mandikan Jenazah</label>
                            <div class="fw-semibold">{{ $deathReport->lokasi_mandi_jenazah ?? '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Siapa Uruskan Jenazah</label>
                            <div class="fw-semibold">{{ $deathReport->pengurusan_jenazah_oleh ?? '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Lokasi Pengkebumian</label>
                            <div class="fw-semibold">
                                @if($deathReport->lokasi_pengkebumian === 'rtb')
                                    Tanah Perkuburan RTB
                                @elseif($deathReport->lokasi_pengkebumian === 'luar_rtb')
                                    Luar Kawasan / Bukan RTB
                                @else
                                    -
                                @endif
                            </div>
                        </div>

                        @if($deathReport->lokasi_pengkebumian === 'luar_rtb')
                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">Nama Tanah Perkuburan</label>
                                <div class="fw-semibold">{{ $deathReport->nama_tanah_perkuburan ?? '-' }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">Negeri Pengkebumian</label>
                                <div class="fw-semibold">{{ $deathReport->negeri_tanah_perkuburan ?? '-' }}</div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label text-muted mb-1">Alamat Penuh Tempat Pengkebumian</label>
                                <div class="fw-semibold">{{ $deathReport->alamat_tanah_perkuburan ?? '-' }}</div>
                            </div>
                        @endif

                        <div class="col-md-12">
                            <label class="form-label text-muted mb-1">Catatan Tambahan</label>
                            <div class="fw-semibold">{{ $deathReport->catatan_pengurusan ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card custom-card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">Maklumat Pelapor</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Nama Pelapor</label>
                            <div class="fw-semibold">{{ $deathReport->nama_pelapor ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">No. Kad Pengenalan</label>
                            <div class="fw-semibold">{{ $deathReport->no_kp_pelapor ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">No. Telefon</label>
                            <div class="fw-semibold">{{ $deathReport->no_tel_pelapor ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Pertalian dengan Si Mati</label>
                            <div class="fw-semibold">{{ $deathReport->pertalian_pelapor ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card custom-card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">Maklumat Pengurusan Kebumi</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Zon Kubur</label>
                            <div class="fw-semibold">
                                 @php
                                    $zone = optional($deathReport->burialPlot)->zone;
                                @endphp

                                {{ match($zone) {
                                    'L' => 'Lelaki',
                                    'P' => 'Perempuan',
                                    'K' => 'Kanak-kanak',
                                    default => '-',
                                } }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">No. Lot Kebumi</label>
                            <div class="fw-semibold">{{ $deathReport->burial_lot_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Tarikh Kebumi</label>
                            <div class="fw-semibold">
                                @if($deathReport->tarikh_kebumi)
                                    {{ \Carbon\Carbon::parse($deathReport->tarikh_kebumi)->format('d/m/Y') }}
                                @elseif($deathReport->burial_date)
                                    {{ \Carbon\Carbon::parse($deathReport->burial_date)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card custom-card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">Dokumen Sokongan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="fw-semibold mb-2">Sijil Mati</div>
                                @if($deathReport->sijil_mati_path)
                                    <a href="{{ asset('storage/' . $deathReport->sijil_mati_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bx bx-show me-1"></i> Lihat Dokumen
                                    </a>
                                @else
                                    <span class="text-muted">Tiada dokumen</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="fw-semibold mb-2">Permit Kebumi</div>
                                @if($deathReport->permit_kebumi_path)
                                    <a href="{{ asset('storage/' . $deathReport->permit_kebumi_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bx bx-show me-1"></i> Lihat Dokumen
                                    </a>
                                @else
                                    <span class="text-muted">Tiada dokumen</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="fw-semibold mb-2">Dokumen Tambahan</div>
                                @if($deathReport->dokumen_sokongan_path)
                                    <a href="{{ asset('storage/' . $deathReport->dokumen_sokongan_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bx bx-show me-1"></i> Lihat Dokumen
                                    </a>
                                @else
                                    <span class="text-muted">Tiada dokumen</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card custom-card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">Catatan Pentadbir</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label text-muted mb-1">Nota Semakan Pentadbir</label>
                        <div class="border rounded-3 p-3 bg-light-subtle">
                            {{ $deathReport->admin_notes ?: '-' }}
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label text-muted mb-1">Catatan Tambahan</label>
                        <div class="border rounded-3 p-3 bg-light-subtle">
                            {{ $deathReport->catatan_admin ?: '-' }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-xl-4">

            <div class="card custom-card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">Ringkasan Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small mb-1">Status Semasa</div>
                        <span class="badge bg-{{ statusBadgeClass($deathReport->status) }} fs-12">
                            {{ statusLabel($deathReport->status) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small mb-1">No. Rujukan</div>
                        <div class="fw-semibold">{{ referenceNo($deathReport) }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small mb-1">Tarikh Hantar</div>
                        <div class="fw-semibold">{{ optional($deathReport->created_at)->format('d/m/Y h:i A') ?? '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small mb-1">Tarikh Semakan</div>
                        <div class="fw-semibold">
                            {{ $deathReport->verified_at ? \Carbon\Carbon::parse($deathReport->verified_at)->format('d/m/Y h:i A') : '-' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small mb-1">Lokasi Pengkebumian</div>
                        <div class="fw-semibold">
                            @if($deathReport->lokasi_pengkebumian === 'rtb')
                                Tanah Perkuburan RTB
                            @elseif($deathReport->lokasi_pengkebumian === 'luar_rtb')
                                Luar Kawasan / Bukan RTB
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div class="mb-0">
                        <div class="text-muted small mb-1">Maklumat Lot Kubur</div>
                        <div class="fw-semibold">
                            {{ $deathReport->burial_lot_no ?? 'Belum ditetapkan' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card custom-card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">Timeline Status</h5>
                </div>
                <div class="card-body">
                    <div class="timeline-custom">
                        @foreach($timeline as $item)
                            <div class="timeline-item d-flex mb-4">
                                <div class="me-3">
                                    <div class="timeline-icon rounded-circle d-flex align-items-center justify-content-center {{ $item['done'] ? 'bg-success text-white' : 'bg-light text-muted border' }}"
                                         style="width: 34px; height: 34px;">
                                        <i class="bx {{ $item['done'] ? 'bx-check' : 'bx-minus' }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold mb-1">{{ $item['title'] }}</div>
                                    <div class="text-muted small mb-1">{{ $item['description'] }}</div>
                                    <div class="small {{ $item['done'] ? 'text-success' : 'text-muted' }}">
                                        {{ $item['date'] ?: 'Belum tersedia' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($deathReport->status === 'perlukan_dokumen_tambahan')
                        <div class="alert alert-info mt-3 mb-0">
                            Sila semak catatan pentadbir dan sediakan dokumen tambahan yang diperlukan.
                        </div>
                    @endif

                    @if($deathReport->status === 'ditolak')
                        <div class="alert alert-danger mt-3 mb-0">
                            Laporan ini telah ditolak. Sila rujuk catatan pentadbir untuk maklumat lanjut.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection