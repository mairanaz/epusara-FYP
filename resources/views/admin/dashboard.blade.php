@extends('layouts.app')

@section('content')
<style>
    .dashboard-page {
        padding-bottom: 30px;
    }

    .dashboard-page .welcome-card {
        border: 0;
        border-radius: 20px;
        background: linear-gradient(135deg, #064e46, #0f766e);
        color: #ffffff;
        overflow: hidden;
        position: relative;
        box-shadow: 0 12px 30px rgba(15, 118, 110, 0.18);
    }

    .dashboard-page .welcome-card::after {
        content: "";
        position: absolute;
        width: 180px;
        height: 180px;
        right: -45px;
        top: -45px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
    }

    .dashboard-page .welcome-card h4 {
        font-weight: 700;
        margin-bottom: 8px;
    }

    .dashboard-page .welcome-card p {
        color: rgba(255, 255, 255, 0.85);
        margin-bottom: 0;
        max-width: 850px;
    }

    .dashboard-page .stat-card {
        border: 0;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        transition: 0.2s ease;
        height: 100%;
    }

    .dashboard-page .stat-card:hover {
        transform: translateY(-3px);
    }

    .dashboard-page .stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e7f7f4;
        color: #0f766e;
        font-size: 22px;
    }

    .dashboard-page .stat-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .dashboard-page .stat-value {
        color: #111827;
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 0;
    }

    .dashboard-page .content-card {
        border: 0;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
    }

    .dashboard-page .card-title {
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
    }

    .dashboard-page .card-subtitle {
        color: #64748b;
        font-size: 13px;
    }

    .dashboard-page .quick-action {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        background: #ffffff;
    }

    .dashboard-page .quick-action:last-child {
        margin-bottom: 0;
    }

    .dashboard-page .quick-action strong {
        color: #111827;
        font-size: 14px;
    }

    .dashboard-page .quick-action span {
        color: #64748b;
        font-size: 13px;
    }

    .dashboard-page .badge-soft-warning {
        background: #fff7ed;
        color: #c2410c;
    }

    .dashboard-page .badge-soft-success {
        background: #ecfdf5;
        color: #047857;
    }

    .dashboard-page .badge-soft-danger {
        background: #fef2f2;
        color: #b91c1c;
    }

    .dashboard-page .badge-soft-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    .dashboard-page table {
        margin-bottom: 0;
    }

    .dashboard-page table thead th {
        color: #475569;
        font-size: 13px;
        font-weight: 700;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
    }

    .dashboard-page table tbody td {
        color: #334155;
        font-size: 13px;
        vertical-align: middle;
    }

    .dashboard-page .btn-dashboard {
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        padding: 7px 12px;
    }
</style>

<div class="dashboard-page">

    {{-- Welcome --}}
    <div class="card welcome-card mb-4">
        <div class="card-body p-4">
            <h4>Selamat Datang, {{ auth()->user()->name ?? 'Admin' }}</h4>
            <p>
                Dashboard ini memaparkan ringkasan pengurusan e-Pusara bagi kawasan RTB Bukit Changgang
                termasuk ahli, tanggungan, pengesahan kematian, tempahan kepukan, lot kubur dan rekod yuran.
            </p>
        </div>
    </div>

    {{-- Statistik Utama --}}
    <div class="row g-3 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Jumlah Ahli Utama</div>
                        <h3 class="stat-value">{{ number_format($totalAhli) }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bx bx-user"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Jumlah Tanggungan</div>
                        <h3 class="stat-value">{{ number_format($totalTanggungan) }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bx bx-group"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Permohonan Ahli Menunggu</div>
                        <h3 class="stat-value">{{ number_format($permohonanAhliMenunggu) }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bx bx-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Kematian Menunggu</div>
                        <h3 class="stat-value">{{ number_format($kematianMenunggu) }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bx bx-clipboard"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Tempahan Kepukan Menunggu</div>
                        <h3 class="stat-value">{{ number_format($tempahanMenunggu) }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bx bx-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Lot Kubur Tersedia</div>
                        <h3 class="stat-value">{{ number_format($lotTersedia) }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bx bx-map-pin"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Jumlah Rekod Kubur</div>
                        <h3 class="stat-value">{{ number_format($jumlahRekodKubur) }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bx bx-archive"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Yuran Bulan Ini</div>
                        <h3 class="stat-value">RM {{ number_format($jumlahYuranBulanIni, 2) }}</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bx bx-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Tindakan Segera + Chart --}}
    <div class="row g-3 mb-4">

        <div class="col-xl-4">
            <div class="card content-card h-100">
                <div class="card-body">
                    <h5 class="card-title">Tindakan Segera</h5>
                    <div class="card-subtitle mb-3">Senarai tugasan yang memerlukan semakan admin.</div>

                    <div class="quick-action">
                        <div>
                            <strong>{{ $permohonanAhliMenunggu }} permohonan ahli</strong><br>
                            <span>Menunggu pengesahan</span>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-primary btn-dashboard">
                            Semak
                        </a>
                    </div>

                    <div class="quick-action">
                        <div>
                            <strong>{{ $kematianMenunggu }} laporan kematian</strong><br>
                            <span>Belum disahkan</span>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-primary btn-dashboard">
                            Semak
                        </a>
                    </div>

                    <div class="quick-action">
                        <div>
                            <strong>{{ $tempahanMenunggu }} tempahan kepukan</strong><br>
                            <span>Menunggu tindakan</span>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-primary btn-dashboard">
                            Semak
                        </a>
                    </div>

                    <div class="quick-action">
                        <div>
                            <strong>{{ $lotTersedia }} lot tersedia</strong><br>
                            <span>Masih boleh digunakan</span>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-primary btn-dashboard">
                            Lihat
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card content-card h-100">
                <div class="card-body">
                    <h5 class="card-title">Status Tempahan Kepukan</h5>
                    <div class="card-subtitle mb-3">Pecahan status tempahan lot/kepukan.</div>
                    <canvas id="statusTempahanChart" height="260"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card content-card h-100">
                <div class="card-body">
                    <h5 class="card-title">Laporan Kematian Bulanan</h5>
                    <div class="card-subtitle mb-3">Jumlah laporan kematian mengikut bulan.</div>
                    <canvas id="kematianBulananChart" height="260"></canvas>
                </div>
            </div>
        </div>

    </div>

    {{-- Jadual Terkini --}}
    <div class="row g-3">

        <div class="col-xl-6">
            <div class="card content-card">
                <div class="card-body">
                    <h5 class="card-title">Permohonan Ahli Terkini</h5>
                    <div class="card-subtitle mb-3">Senarai ahli terbaru dalam sistem.</div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Status</th>
                                    <th>Tarikh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($permohonanTerkini as $item)
                                    <tr>
                                        <td>{{ $item->nama ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ strtolower($item->status ?? '') == 'aktif' ? 'badge-soft-success' : 'badge-soft-warning' }}">
                                                {{ ucfirst(str_replace('_', ' ', $item->status ?? '-')) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">
                                            Tiada data permohonan ahli.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card content-card">
                <div class="card-body">
                    <h5 class="card-title">Tempahan Kepukan Terkini</h5>
                    <div class="card-subtitle mb-3">Senarai tempahan lot/kepukan terbaru.</div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Pemohon</th>
                                    <th>Status</th>
                                    <th>Tarikh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tempahanTerkini as $item)
                                    <tr>
                                        <td>{{ $item->nama_pemohon ?? '-' }}</td>
                                        <td>
                                            @php
                                                $status = strtolower($item->status ?? '');
                                                $badgeClass = 'badge-soft-secondary';

                                                if (in_array($status, ['pending', 'menunggu', 'belum_disahkan'])) {
                                                    $badgeClass = 'badge-soft-warning';
                                                } elseif (in_array($status, ['approved', 'diluluskan', 'lulus', 'selesai'])) {
                                                    $badgeClass = 'badge-soft-success';
                                                } elseif (in_array($status, ['rejected', 'ditolak', 'tolak'])) {
                                                    $badgeClass = 'badge-soft-danger';
                                                }
                                            @endphp

                                            <span class="badge {{ $badgeClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $item->status ?? '-')) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">
                                            Tiada data tempahan kepukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const statusTempahanLabels = @json(array_keys($statusTempahan));
    const statusTempahanData = @json(array_values($statusTempahan));

    new Chart(document.getElementById('statusTempahanChart'), {
        type: 'doughnut',
        data: {
            labels: statusTempahanLabels,
            datasets: [{
                data: statusTempahanData,
                backgroundColor: [
                    '#f59e0b',
                    '#10b981',
                    '#ef4444',
                    '#3b82f6'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    const kematianLabels = @json($bulanLabels);
    const kematianData = @json($kematianBulanan);

    new Chart(document.getElementById('kematianBulananChart'), {
        type: 'bar',
        data: {
            labels: kematianLabels,
            datasets: [{
                label: 'Jumlah Laporan',
                data: kematianData,
                backgroundColor: '#0f766e',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endsection