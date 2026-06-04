@extends('layouts.app')

@section('content')
<style>
    .fee-report-page .hero-card {
        border: 0;
        border-radius: 22px;
        background: linear-gradient(135deg, #d9ddff, #ecebff);
        color: #1f2937;
        overflow: hidden;
        position: relative;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .fee-report-page .hero-card::after {
        content: "";
        position: absolute;
        top: -55px;
        right: -35px;
        width: 190px;
        height: 190px;
        background: rgba(255,255,255,0.22);
        border-radius: 50%;
    }

    .fee-report-page .hero-subtitle {
        color: #6b7280;
    }

    .fee-report-page .report-badge {
        padding: 10px 18px;
        border-radius: 999px;
        background: rgba(255,255,255,0.85);
        color: #334155;
        font-size: 14px;
        font-weight: 700;
        border: 1px solid rgba(255,255,255,0.9);
        box-shadow: 0 5px 18px rgba(0,0,0,0.04);
    }

    .fee-report-page .fee-workspace {
        display: grid;
        grid-template-columns: 280px minmax(360px, 1fr) 420px;
        gap: 14px;
        align-items: stretch;
    }

    .fee-report-page .fee-panel {
        background: #fff;
        border: 1px solid #eef0f5;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.045);
        overflow: hidden;
    }

    .fee-report-page .fee-left-panel {
        min-height: 720px;
    }

    .fee-report-page .fee-panel-header {
        padding: 18px 20px;
        border-bottom: 1px solid #eef0f5;
    }

    .fee-report-page .fee-nav {
        list-style: none;
        padding: 18px;
        margin: 0;
    }

    .fee-report-page .fee-nav-title {
        font-size: 11px;
        color: #98a2b3;
        font-weight: 800;
        letter-spacing: .05em;
        margin: 16px 0 8px;
        text-transform: uppercase;
    }

    .fee-report-page .fee-nav li a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 12px;
        color: #475467;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: .2s ease;
    }

    .fee-report-page .fee-nav li a:hover,
    .fee-report-page .fee-nav li.active a {
        background: #f1ecff;
        color: #7c3aed;
    }

    .fee-report-page .fee-nav li a i {
        font-size: 17px;
    }

    .fee-report-page .fee-nav .nav-badge {
        margin-left: auto;
        font-size: 11px;
        border-radius: 999px;
        padding: 4px 8px;
        background: #ecfdf3;
        color: #039855;
        font-weight: 800;
    }

    .fee-report-page .filter-box {
        padding: 18px 20px;
        border-bottom: 1px solid #eef0f5;
    }

    .fee-report-page .form-control,
    .fee-report-page .form-select {
        min-height: 44px;
        border-radius: 12px;
        border-color: #e5e7eb;
    }

    .fee-report-page .btn {
        border-radius: 12px;
    }

    .fee-report-page .summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        padding: 18px 20px;
    }

    .fee-report-page .summary-mini-card {
        border: 1px solid #eef0f5;
        border-radius: 16px;
        padding: 16px;
        background: #fff;
        transition: .2s ease;
    }

    .fee-report-page .summary-mini-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 22px rgba(0,0,0,0.05);
    }

    .fee-report-page .summary-mini-label {
        font-size: 12px;
        color: #667085;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .fee-report-page .summary-mini-value {
        font-size: 22px;
        font-weight: 800;
        color: #111827;
        line-height: 1.15;
    }

    .fee-report-page .summary-mini-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .fee-report-page .monthly-list,
    .fee-report-page .report-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .fee-report-page .monthly-list li,
    .fee-report-page .report-list li {
        padding: 15px 20px;
        border-top: 1px solid #f2f4f7;
        cursor: pointer;
        transition: .2s ease;
    }

    .fee-report-page .monthly-list li:hover,
    .fee-report-page .monthly-list li.active,
    .fee-report-page .report-list li:hover,
    .fee-report-page .report-list li.active {
        background: #f8f5ff;
    }

    .fee-report-page .month-dot {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: #eef2ff;
        color: #5b63a8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        flex-shrink: 0;
    }

    .fee-report-page .amount-text {
        font-weight: 800;
        color: #111827;
    }

    .fee-report-page .muted-small {
        font-size: 12px;
        color: #667085;
    }

    .fee-report-page .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .fee-report-page .status-paid {
        background: #dcfce7;
        color: #15803d;
    }

    .fee-report-page .status-pending {
        background: #fef3c7;
        color: #b45309;
    }

    .fee-report-page .status-failed {
        background: #fee2e2;
        color: #b91c1c;
    }

    .fee-report-page .status-info {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .fee-report-page .detail-body {
        padding: 24px;
    }

    .fee-report-page .detail-title {
        font-size: 22px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 6px;
    }

    .fee-report-page .detail-card {
        border: 1px solid #eef0f5;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 14px;
        background: #fff;
    }

    .fee-report-page .detail-row {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        padding: 10px 0;
        border-bottom: 1px dashed #eef0f5;
        font-size: 14px;
    }

    .fee-report-page .detail-row:last-child {
        border-bottom: 0;
    }

    .fee-report-page .detail-row span:first-child {
        color: #667085;
        font-weight: 600;
    }

    .fee-report-page .detail-row span:last-child {
        color: #111827;
        font-weight: 800;
        text-align: right;
    }

    .fee-report-page .audit-note {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        padding: 14px 16px;
        color: #475569;
        font-size: 13px;
    }

    .fee-report-page .chart-box {
        min-height: 285px;
        border: 1px solid #eef0f5;
        border-radius: 16px;
        padding: 10px 12px 0;
        background: #fff;
    }

    .fee-report-page .export-footer {
        padding: 18px 20px;
        border-top: 1px solid #eef0f5;
        background: #fff;
    }

    .fee-report-page .table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e9ecef;
        font-weight: 700;
        color: #344054;
        white-space: nowrap;
        vertical-align: middle;
    }

    .fee-report-page .table tbody tr:hover {
        background: #faf7ff;
    }

    .fee-report-page .empty-state {
        padding: 32px 20px;
        text-align: center;
        color: #667085;
    }

    @media (max-width: 1400px) {
        .fee-report-page .fee-workspace {
            grid-template-columns: 260px minmax(360px, 1fr);
        }

        .fee-report-page .fee-detail-panel {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 992px) {
        .fee-report-page .fee-workspace {
            grid-template-columns: 1fr;
        }

        .fee-report-page .fee-left-panel {
            min-height: unset;
        }

        .fee-report-page .summary-grid {
            grid-template-columns: 1fr;
        }
    }

    .fee-report-page .transaction-mini-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .fee-report-page .transaction-mini-item {
        border: 1px solid #eef0f5;
        border-radius: 14px;
        padding: 12px;
        background: #fff;
        transition: .2s ease;
    }

    .fee-report-page .transaction-mini-item:hover {
        background: #faf7ff;
        transform: translateY(-2px);
    }

    .fee-report-page .transaction-avatar {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: #eef2ff;
        color: #5b63a8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        flex-shrink: 0;
    }

    .fee-report-page .transaction-section-title {
        font-size: 14px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 10px;
    }
</style>

@php
    $activeTab = request('tab', 'summary');

    $allowedTabs = [
        'summary',
        'monthly',
        'type',
        'transactions',
        'unpaid',
        'arrears',
        'audit',
        'export',
    ];

    if (! in_array($activeTab, $allowedTabs)) {
        $activeTab = 'summary';
    }

    $currentYear = request('year', $currentYear ?? now()->year);
    $currentMonth = request('month', $currentMonth ?? null);
    $currentKeyword = request('search');
    $currentStatus = request('status');

    $totalAmount = $totalAmount ?? 0;
    $totalCount = $totalCount ?? 0;
    $paidCount = $paidCount ?? 0;
    $pendingCount = $pendingCount ?? 0;
    $failedCount = $failedCount ?? 0;
    $groupedFees = $groupedFees ?? collect();

    $months = $months ?? [
        1 => ['short' => 'JAN', 'name' => 'Januari', 'amount' => 0, 'registration' => 0, 'monthly' => 0, 'annual' => 0, 'paid' => 0, 'pending' => 0, 'failed' => 0],
        2 => ['short' => 'FEB', 'name' => 'Februari', 'amount' => 0, 'registration' => 0, 'monthly' => 0, 'annual' => 0, 'paid' => 0, 'pending' => 0, 'failed' => 0],
        3 => ['short' => 'MAC', 'name' => 'Mac', 'amount' => 0, 'registration' => 0, 'monthly' => 0, 'annual' => 0, 'paid' => 0, 'pending' => 0, 'failed' => 0],
        4 => ['short' => 'APR', 'name' => 'April', 'amount' => 0, 'registration' => 0, 'monthly' => 0, 'annual' => 0, 'paid' => 0, 'pending' => 0, 'failed' => 0],
        5 => ['short' => 'MEI', 'name' => 'Mei', 'amount' => 0, 'registration' => 0, 'monthly' => 0, 'annual' => 0, 'paid' => 0, 'pending' => 0, 'failed' => 0],
        6 => ['short' => 'JUN', 'name' => 'Jun', 'amount' => 0, 'registration' => 0, 'monthly' => 0, 'annual' => 0, 'paid' => 0, 'pending' => 0, 'failed' => 0],
        7 => ['short' => 'JUL', 'name' => 'Julai', 'amount' => 0, 'registration' => 0, 'monthly' => 0, 'annual' => 0, 'paid' => 0, 'pending' => 0, 'failed' => 0],
        8 => ['short' => 'OGO', 'name' => 'Ogos', 'amount' => 0, 'registration' => 0, 'monthly' => 0, 'annual' => 0, 'paid' => 0, 'pending' => 0, 'failed' => 0],
        9 => ['short' => 'SEP', 'name' => 'September', 'amount' => 0, 'registration' => 0, 'monthly' => 0, 'annual' => 0, 'paid' => 0, 'pending' => 0, 'failed' => 0],
        10 => ['short' => 'OKT', 'name' => 'Oktober', 'amount' => 0, 'registration' => 0, 'monthly' => 0, 'annual' => 0, 'paid' => 0, 'pending' => 0, 'failed' => 0],
        11 => ['short' => 'NOV', 'name' => 'November', 'amount' => 0, 'registration' => 0, 'monthly' => 0, 'annual' => 0, 'paid' => 0, 'pending' => 0, 'failed' => 0],
        12 => ['short' => 'DIS', 'name' => 'Disember', 'amount' => 0, 'registration' => 0, 'monthly' => 0, 'annual' => 0, 'paid' => 0, 'pending' => 0, 'failed' => 0],
    ];

    $feeTypes = $feeTypes ?? [
        ['name' => 'Yuran Pendaftaran', 'amount' => 0, 'count' => 0, 'icon' => 'ri-user-add-line', 'status' => 'Aktif'],
        ['name' => 'Yuran Bulanan', 'amount' => 0, 'count' => 0, 'icon' => 'ri-calendar-check-line', 'status' => 'Utama'],
        ['name' => 'Yuran Tahunan', 'amount' => 0, 'count' => 0, 'icon' => 'ri-calendar-todo-line', 'status' => 'Aktif'],
    ];

    $unpaidMembers = $unpaidMembers ?? [];

    $selectedMonth = $currentMonth ?: 1;
    $selectedMonthData = $months[(int) $selectedMonth] ?? $months[1];

    
        $selectedMonthTransactions = collect($groupedFees ?? [])
            ->filter(function ($fee) use ($selectedMonth, $currentYear) {
                $feeMonth = $fee->paid_month
                    ?? ($fee->paid_at ? \Carbon\Carbon::parse($fee->paid_at)->month : null)
                    ?? ($fee->created_at ? \Carbon\Carbon::parse($fee->created_at)->month : null);

                $feeYear = $fee->membership_year
                    ?? ($fee->paid_at ? \Carbon\Carbon::parse($fee->paid_at)->year : null)
                    ?? ($fee->created_at ? \Carbon\Carbon::parse($fee->created_at)->year : null);

                return (int) $feeMonth === (int) $selectedMonth
                    && (int) $feeYear === (int) $currentYear;
            });

        $selectedPaidTransactions = $selectedMonthTransactions
            ->filter(fn ($fee) => strtolower($fee->fee_status ?? $fee->status ?? '') === 'paid')
            ->values();

        $selectedPendingTransactions = $selectedMonthTransactions
            ->filter(fn ($fee) => strtolower($fee->fee_status ?? $fee->status ?? '') === 'pending')
            ->values();

        $selectedFailedTransactions = $selectedMonthTransactions
            ->filter(fn ($fee) => strtolower($fee->fee_status ?? $fee->status ?? '') === 'failed')
            ->values();
    

    $getTabTitle = function ($tab) {
        return match($tab) {
            'summary' => 'Ringkasan Kewangan',
            'monthly' => 'Kutipan Bulanan',
            'type' => 'Jenis Yuran',
            'transactions' => 'Rekod Transaksi',
            'unpaid' => 'Ahli Belum Bayar',
            'arrears' => 'Tunggakan',
            'audit' => 'Audit Bayaran',
            'export' => 'Export Laporan',
            default => 'Ringkasan Kewangan',
        };
    };

    $getStatusClass = function ($status) {
        return match(strtolower($status ?? 'pending')) {
            'paid', 'berjaya', 'aktif', 'utama' => 'status-paid',
            'pending', 'belum bayar', 'belum lengkap' => 'status-pending',
            'failed', 'gagal', 'tertunggak' => 'status-failed',
            default => 'status-info',
        };
    };

    $totalRegistration = collect($feeTypes)
    ->firstWhere('name', 'Yuran Pendaftaran')['amount'] ?? 0;

    $totalMonthly = collect($feeTypes)
        ->firstWhere('name', 'Yuran Bulanan')['amount'] ?? 0;

    $totalAnnual = collect($feeTypes)
        ->firstWhere('name', 'Yuran Tahunan')['amount'] ?? 0;

    $displayTotalAmount = $totalAmount;

    $chartMonthLabels = collect($months)
        ->map(fn ($month) => $month['short'])
        ->values()
        ->toArray();

    $chartMonthAmounts = collect($months)
        ->map(fn ($month) => (float) $month['amount'])
        ->values()
        ->toArray();

@endphp

<div class="container-fluid fee-report-page">

    <div class="card hero-card shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div>
                    <p class="mb-2 small hero-subtitle">Panel Pentadbir</p>
                    <h1 class="fw-bold mb-2 display-5">Laporan Yuran</h1>
                    <p class="mb-0 hero-subtitle">
                        Rekod keseluruhan kewangan yuran ahli untuk semakan pentadbir dan tujuan audit.
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2 align-items-start">
                    <span class="report-badge">Tahun: {{ $currentYear }}</span>
                    <span class="report-badge">Dijana: {{ now()->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="fee-workspace">

        <div class="fee-panel fee-left-panel">
            <div class="fee-panel-header">
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'export']) }}" class="btn btn-success w-100">
                    <i class="ri-file-chart-line me-1"></i>
                    Jana Laporan
                </a>
            </div>

            <ul class="fee-nav">
                <li class="fee-nav-title">Laporan</li>

                <li class="{{ $activeTab == 'summary' ? 'active' : '' }}">
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'summary']) }}">
                        <i class="ri-dashboard-line"></i>
                        <span>Ringkasan Kewangan</span>
                        <span class="nav-badge">Utama</span>
                    </a>
                </li>

                <li class="{{ $activeTab == 'monthly' ? 'active' : '' }}">
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'monthly']) }}">
                        <i class="ri-calendar-2-line"></i>
                        <span>Kutipan Bulanan</span>
                    </a>
                </li>

                <li class="{{ $activeTab == 'type' ? 'active' : '' }}">
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'type']) }}">
                        <i class="ri-pie-chart-2-line"></i>
                        <span>Jenis Yuran</span>
                    </a>
                </li>

                <li class="{{ $activeTab == 'transactions' ? 'active' : '' }}">
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'transactions']) }}">
                        <i class="ri-list-check-2"></i>
                        <span>Rekod Transaksi</span>
                    </a>
                </li>

                <li class="{{ $activeTab == 'unpaid' ? 'active' : '' }}">
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'unpaid']) }}">
                        <i class="ri-user-unfollow-line"></i>
                        <span>Ahli Belum Bayar</span>
                        <span class="nav-badge">{{ count($unpaidMembers) }}</span>
                    </a>
                </li>

                <li class="{{ $activeTab == 'arrears' ? 'active' : '' }}">
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'arrears']) }}">
                        <i class="ri-alarm-warning-line"></i>
                        <span>Tunggakan</span>
                    </a>
                </li>

                <li class="fee-nav-title">Audit & Export</li>

                <li class="{{ $activeTab == 'audit' ? 'active' : '' }}">
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'audit']) }}">
                        <i class="ri-shield-check-line"></i>
                        <span>Audit Bayaran</span>
                    </a>
                </li>

                <li class="{{ $activeTab == 'export' ? 'active' : '' }}">
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'export']) }}">
                        <i class="ri-download-cloud-line"></i>
                        <span>Export Laporan</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="fee-panel">
            <div class="fee-panel-header d-flex align-items-center justify-content-between gap-3">
                <div>
                    <h5 class="fw-bold mb-1">{{ $getTabTitle($activeTab) }}</h5>
                    <div class="muted-small">Paparan laporan yuran bagi tahun {{ $currentYear }}</div>
                </div>

                <div class="dropdown">
                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => null]) }}">Semua Rekod</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'paid']) }}">Berjaya Sahaja</a></li>
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">Pending Sahaja</a></li>
                    </ul>
                </div>
            </div>

            <div class="filter-box">
                <form method="GET" action="{{ route('admin.khairat.fees.index') }}">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">

                    <div class="row g-2">
                        <div class="col-md-4">
                            <select name="year" class="form-select">
                                @for($year = now()->year; $year >= now()->year - 5; $year--)
                                    <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-4">
                            <select name="month" class="form-select">
                                <option value="">Semua Bulan</option>
                                @foreach($months as $monthNumber => $month)
                                    <option value="{{ $monthNumber }}" {{ $currentMonth == $monthNumber ? 'selected' : '' }}>
                                        {{ $month['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <button class="btn btn-primary w-100">
                                <i class="ri-search-line me-1"></i>
                                Tapis
                            </button>
                        </div>

                        <div class="col-12 mt-2">
                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Cari nama ahli / No KP / No resit / reference payment"
                                value="{{ $currentKeyword }}"
                            >
                        </div>
                    </div>
                </form>
            </div>

            @if($activeTab == 'summary')
                <div class="summary-grid">
                    <div class="summary-mini-card">
                        <div class="d-flex justify-content-between gap-3">
                            <div>
                                <div class="summary-mini-label">Jumlah Kutipan</div>
                                <div class="summary-mini-value">RM {{ number_format($displayTotalAmount, 2) }}</div>
                            </div>
                            <div class="summary-mini-icon bg-info-subtle text-info">
                                <i class="ri-wallet-3-line"></i>
                            </div>
                        </div>
                    </div>

                    <div class="summary-mini-card">
                        <div class="d-flex justify-content-between gap-3">
                            <div>
                                <div class="summary-mini-label">Transaksi Berjaya</div>
                                <div class="summary-mini-value">{{ $paidCount }}</div>
                            </div>
                            <div class="summary-mini-icon bg-success-subtle text-success">
                                <i class="ri-checkbox-circle-line"></i>
                            </div>
                        </div>
                    </div>

                    <div class="summary-mini-card">
                        <div class="d-flex justify-content-between gap-3">
                            <div>
                                <div class="summary-mini-label">Belum Lengkap</div>
                                <div class="summary-mini-value">{{ $pendingCount }}</div>
                            </div>
                            <div class="summary-mini-icon bg-warning-subtle text-warning">
                                <i class="ri-time-line"></i>
                            </div>
                        </div>
                    </div>

                    <div class="summary-mini-card">
                        <div class="d-flex justify-content-between gap-3">
                            <div>
                                <div class="summary-mini-label">Jumlah Ahli</div>
                                <div class="summary-mini-value">{{ $totalCount }}</div>
                            </div>
                            <div class="summary-mini-icon bg-primary-subtle text-primary">
                                <i class="ri-group-line"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 pb-3">
                    <h6 class="fw-bold mb-1">Trend Kutipan Bulanan Tahun {{ $currentYear }}</h6>
                        <div class="muted-small mb-3">
                            Graf ini memaparkan jumlah kutipan setiap bulan bagi tahun dipilih.
                        </div>

                    <div class="chart-box">
                        <div id="fee-column-datalabels"></div>
                    </div>
                </div>

                <div class="fee-panel-header">
                    <h6 class="fw-bold mb-0">Kutipan Tertinggi Mengikut Bulan</h6>
                </div>

                <ul class="monthly-list">
                    @forelse(collect($months)->where('amount', '>', 0)->sortByDesc('amount')->take(5) as $monthNumber => $month)
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['tab' => 'monthly', 'month' => $monthNumber]) }}" class="text-decoration-none text-reset">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="month-dot">{{ $month['short'] }}</div>
                                    <div class="flex-fill">
                                        <div class="fw-bold">{{ $month['name'] }} {{ $currentYear }}</div>
                                        <div class="muted-small">{{ $month['paid'] }} berjaya · {{ $month['pending'] }} pending</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="amount-text">RM {{ number_format($month['amount'], 2) }}</div>
                                        <div class="muted-small">Jumlah kutipan</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li>
                            <div class="empty-state">Tiada kutipan direkodkan untuk tahun ini.</div>
                        </li>
                    @endforelse
                </ul>

            @elseif($activeTab == 'monthly')
                <div class="fee-panel-header">
                    <h6 class="fw-bold mb-0">Kutipan Mengikut Bulan</h6>
                </div>

                <ul class="monthly-list">
                    @foreach($months as $monthNumber => $month)
                        <li class="{{ (int) $selectedMonth == $monthNumber ? 'active' : '' }}">
                            <a href="{{ request()->fullUrlWithQuery(['tab' => 'monthly', 'month' => $monthNumber]) }}" class="text-decoration-none text-reset">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="month-dot">{{ $month['short'] }}</div>
                                    <div class="flex-fill">
                                        <div class="fw-bold">{{ $month['name'] }} {{ $currentYear }}</div>
                                        <div class="muted-small">
                                            {{ $month['paid'] }} berjaya · {{ $month['pending'] }} pending · {{ $month['failed'] }} gagal
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="amount-text">RM {{ number_format($month['amount'], 2) }}</div>
                                        <div class="muted-small">Jumlah kutipan</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>

            @elseif($activeTab == 'type')
                <div class="p-4">
                    <h5 class="fw-bold mb-3">Laporan Mengikut Jenis Yuran</h5>

                    <ul class="report-list">
                        @foreach($feeTypes as $feeType)
                             @continue(($feeType['amount'] ?? 0) <= 0 && ($feeType['count'] ?? 0) <= 0)
                            <li>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="month-dot">
                                        <i class="{{ $feeType['icon'] }}"></i>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="fw-bold">{{ $feeType['name'] }}</div>
                                        <div class="muted-small">{{ $feeType['count'] }} transaksi direkodkan</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="amount-text">RM {{ number_format($feeType['amount'], 2) }}</div>
                                        <span class="status-pill {{ $getStatusClass($feeType['status']) }}">
                                            {{ $feeType['status'] }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

            @elseif($activeTab == 'transactions')
                <div class="p-4">
                    <h5 class="fw-bold mb-3">Rekod Transaksi Bayaran</h5>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>No Resit</th>
                                    <th>Nama Ahli</th>
                                    <th>Jenis Yuran</th>
                                    <th>Status</th>
                                    <th class="text-end">Amaun</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupedFees as $fee)
                                    <tr>
                                        <td>{{ $fee->receipt_no ?? $fee->no_resit ?? '-' }}</td>
                                        <td>{{ $fee->user_name ?? '-' }}</td>
                                        <td>{{ $fee->fee_label ?? $fee->payment_type ?? '-' }}</td>
                                        <td>
                                            <span class="status-pill {{ $getStatusClass($fee->fee_status ?? $fee->status ?? 'pending') }}">
                                                {{ ucfirst($fee->fee_status ?? $fee->status ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td class="text-end amount-text">
                                            RM {{ number_format($fee->total_paid_amount ?? $fee->amount ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="empty-state">Tiada rekod transaksi dijumpai.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif($activeTab == 'unpaid')
                <div class="p-4">
                    <h5 class="fw-bold mb-3">Senarai Ahli Belum Bayar</h5>

                    <ul class="report-list">
                        @forelse($unpaidMembers as $member)
                            <li>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="month-dot">
                                        {{ strtoupper(substr($member['name'] ?? 'A', 0, 1)) }}
                                    </div>
                                    <div class="flex-fill">
                                        <div class="fw-bold">{{ $member['name'] ?? '-' }}</div>
                                        <div class="muted-small">
                                            {{ $member['no_kp'] ?? '-' }} · {{ $member['phone'] ?? '-' }}
                                        </div>
                                        <div class="muted-small">
                                            Bulan belum bayar: {{ $member['month'] ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="amount-text">RM {{ number_format($member['arrears'] ?? 0, 2) }}</div>
                                        <span class="status-pill {{ $getStatusClass($member['status'] ?? 'Belum Bayar') }}">
                                            {{ $member['status'] ?? 'Belum Bayar' }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li>
                                <div class="empty-state">Tiada ahli belum bayar dijumpai.</div>
                            </li>
                        @endforelse
                    </ul>
                </div>

            @elseif($activeTab == 'arrears')
                <div class="p-4">
                    <h5 class="fw-bold mb-3">Laporan Tunggakan</h5>

                    <div class="summary-grid p-0 mb-3">
                        <div class="summary-mini-card">
                            <div class="summary-mini-label">Tunggakan 1 Bulan</div>
                            <div class="summary-mini-value">{{ count($unpaidMembers) }} Ahli</div>
                        </div>

                        <div class="summary-mini-card">
                            <div class="summary-mini-label">Jumlah Tunggakan</div>
                            <div class="summary-mini-value">RM {{ number_format(collect($unpaidMembers)->sum('arrears'), 2) }}</div>
                        </div>

                        <div class="summary-mini-card">
                            <div class="summary-mini-label">Pending</div>
                            <div class="summary-mini-value">{{ $pendingCount }}</div>
                        </div>

                        <div class="summary-mini-card">
                            <div class="summary-mini-label">Gagal</div>
                            <div class="summary-mini-value">{{ $failedCount }}</div>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-row">
                            <span>Status Tunggakan</span>
                            <span>Perlu Semakan</span>
                        </div>
                        <div class="detail-row">
                            <span>Jumlah Kes</span>
                            <span>{{ count($unpaidMembers) }} Ahli</span>
                        </div>
                        <div class="detail-row">
                            <span>Cadangan Tindakan</span>
                            <span>Semakan Admin</span>
                        </div>
                    </div>
                </div>

            @elseif($activeTab == 'audit')
                <div class="p-4">
                    <h5 class="fw-bold mb-3">Audit Bayaran</h5>

                    <ul class="report-list">
                        <li>
                            <div class="d-flex align-items-center gap-3">
                                <div class="month-dot"><i class="ri-check-line"></i></div>
                                <div class="flex-fill">
                                    <div class="fw-bold">Rekod bayaran disemak</div>
                                    <div class="muted-small">Semakan laporan yuran tahun {{ $currentYear }}</div>
                                </div>
                                <div class="text-end muted-small">{{ now()->format('d/m/Y') }}</div>
                            </div>
                        </li>

                        <li>
                            <div class="d-flex align-items-center gap-3">
                                <div class="month-dot"><i class="ri-file-list-line"></i></div>
                                <div class="flex-fill">
                                    <div class="fw-bold">Laporan dijana oleh sistem</div>
                                    <div class="muted-small">Modul laporan kewangan yuran</div>
                                </div>
                                <div class="text-end muted-small">{{ now()->format('d/m/Y') }}</div>
                            </div>
                        </li>
                    </ul>
                </div>

            @elseif($activeTab == 'export')
                <div class="p-4">
                    <h5 class="fw-bold mb-3">Export Laporan</h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('admin.khairat.fees.export.excel', request()->query()) }}"
                            class="btn btn-success w-100 py-3">
                                <i class="ri-file-excel-2-line me-1"></i>
                                Export Excel
                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="{{ route('admin.khairat.fees.export.pdf', request()->query()) }}"
                            class="btn btn-danger w-100 py-3"
                            target="_blank">
                                <i class="ri-file-pdf-2-line me-1"></i>
                                Preview PDF
                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="{{ route('admin.khairat.fees.export.pdf', request()->query()) }}"
                            class="btn btn-light w-100 py-3"
                            target="_blank">
                                <i class="ri-printer-line me-1"></i>
                                Cetak Laporan
                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="{{ request()->fullUrlWithQuery(['tab' => 'summary']) }}"
                            class="btn btn-primary w-100 py-3">
                                <i class="ri-eye-line me-1"></i>
                                Lihat Ringkasan
                            </a>
                        </div>
                    </div>

                    <div class="audit-note mt-4">
                        <i class="ri-information-line me-1"></i>
                        Excel sesuai untuk audit dan semakan detail. PDF sesuai untuk laporan rasmi yang boleh dicetak.
                    </div>
                </div>
            @endif
        </div>

        <div class="fee-panel fee-detail-panel">
            <div class="fee-panel-header d-flex align-items-center justify-content-between gap-3">
                <div>
                    <h5 class="fw-bold mb-1">Detail Laporan</h5>
                    <div class="muted-small">Preview audit dan maklumat pilihan</div>
                </div>

                <span class="status-pill status-paid">
                    <i class="ri-check-line"></i>
                    Aktif
                </span>
            </div>

            <div class="detail-body">
                @if($activeTab == 'summary')
                    <div class="mb-4">
                        <div class="detail-title">
                            Ringkasan 
                            @if($currentMonth)
                                {{ $months[$currentMonth]['name'] ?? '' }} {{ $currentYear }}
                            @else
                                {{ $currentYear }}
                            @endif
                        </div>
                        <div class="muted-small">Gambaran keseluruhan kutipan yuran ahli.</div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-row">
                            <span>Jumlah Kutipan</span>
                            <span>RM {{ number_format($displayTotalAmount, 2) }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Jumlah Ahli</span>
                            <span>{{ $totalCount }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Bayaran Lengkap</span>
                            <span>{{ $paidCount }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Belum Lengkap</span>
                            <span>{{ $pendingCount }}</span>
                        </div>
                    </div>

                    <div class="detail-card">
                        <h6 class="fw-bold mb-3">Pecahan Kutipan</h6>
                        @if($totalRegistration > 0)
                            <div class="detail-row">
                                <span>Yuran Pendaftaran</span>
                                <span>RM {{ number_format($totalRegistration, 2) }}</span>
                            </div>
                        @endif
                        <div class="detail-row">
                            <span>Yuran Bulanan</span>
                            <span>RM {{ number_format($totalMonthly, 2) }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Yuran Tahunan</span>
                            <span>RM {{ number_format($totalAnnual, 2) }}</span>
                        </div>
                    </div>

                @elseif($activeTab == 'monthly')
                <div class="mb-4">
                    <div class="detail-title">{{ $selectedMonthData['name'] }} {{ $currentYear }}</div>
                    <div class="muted-small">
                        Ringkasan kutipan yuran dan senarai transaksi untuk bulan dipilih.
                    </div>
                </div>

                <div class="detail-card">
                    <div class="detail-row">
                        <span>Jumlah Kutipan</span>
                        <span>RM {{ number_format($selectedMonthData['amount'], 2) }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Yuran Pendaftaran</span>
                        <span>RM {{ number_format($selectedMonthData['registration'], 2) }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Yuran Bulanan</span>
                        <span>RM {{ number_format($selectedMonthData['monthly'], 2) }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Yuran Tahunan</span>
                        <span>RM {{ number_format($selectedMonthData['annual'], 2) }}</span>
                    </div>
                </div>

                <div class="detail-card">
                    <div class="detail-row">
                        <span>Transaksi Berjaya</span>
                        <span>{{ $selectedPaidTransactions->count() }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Transaksi Pending</span>
                        <span>{{ $selectedPendingTransactions->count() }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Transaksi Gagal</span>
                        <span>{{ $selectedFailedTransactions->count() }}</span>
                    </div>
                    <div class="detail-row">
                        <span>No. Rujukan Laporan</span>
                        <span>YRN-{{ $currentYear }}-{{ str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>

                {{-- Transaksi Berjaya --}}
                <div class="detail-card">
                    <div class="transaction-section-title">
                        <i class="ri-checkbox-circle-line text-success me-1"></i>
                        Transaksi Berjaya
                    </div>

                    <div class="transaction-mini-list">
                        @forelse($selectedPaidTransactions->take(5) as $fee)
                            <div class="transaction-mini-item">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="transaction-avatar">
                                        {{ strtoupper(substr($fee->user_name ?? 'A', 0, 1)) }}
                                    </div>

                                    <div class="flex-fill">
                                        <div class="fw-bold">{{ $fee->user_name ?? '-' }}</div>
                                        <div class="muted-small">
                                            {{ $fee->receipt_no ?? '-' }} · {{ $fee->fee_label ?? '-' }}
                                        </div>
                                        <div class="muted-small">
                                            Tarikh:
                                            {{ !empty($fee->paid_at) ? \Carbon\Carbon::parse($fee->paid_at)->format('d/m/Y') : '-' }}
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <div class="amount-text">
                                            RM {{ number_format($fee->total_paid_amount ?? $fee->amount ?? 0, 2) }}
                                        </div>

                                        @if(!empty($fee->user_id))
                                            <a href="{{ route('admin.khairat.fees.show', $fee->user_id) }}"
                                            class="btn btn-sm btn-outline-primary mt-2">
                                                Lihat
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state py-3">
                                Tiada transaksi berjaya untuk bulan ini.
                            </div>
                        @endforelse
                    </div>

                    @if($selectedPaidTransactions->count() > 5)
                        <div class="mt-3 text-end">
                            <a href="{{ request()->fullUrlWithQuery(['tab' => 'transactions', 'month' => $selectedMonth, 'status' => 'paid']) }}"
                            class="btn btn-sm btn-success">
                                Lihat Semua Berjaya
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Transaksi Pending --}}
                <div class="detail-card">
                    <div class="transaction-section-title">
                        <i class="ri-time-line text-warning me-1"></i>
                        Transaksi Pending
                    </div>

                    <div class="transaction-mini-list">
                        @forelse($selectedPendingTransactions->take(5) as $fee)
                            <div class="transaction-mini-item">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="transaction-avatar">
                                        {{ strtoupper(substr($fee->user_name ?? 'A', 0, 1)) }}
                                    </div>

                                    <div class="flex-fill">
                                        <div class="fw-bold">{{ $fee->user_name ?? '-' }}</div>
                                        <div class="muted-small">
                                            {{ $fee->receipt_no ?? '-' }} · {{ $fee->fee_label ?? '-' }}
                                        </div>
                                        <div class="muted-small">
                                            Reference: {{ $fee->payment_reference ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <div class="amount-text">
                                            RM {{ number_format($fee->total_paid_amount ?? $fee->amount ?? 0, 2) }}
                                        </div>

                                        @if(!empty($fee->user_id))
                                            <a href="{{ route('admin.khairat.fees.show', $fee->user_id) }}"
                                            class="btn btn-sm btn-outline-warning mt-2">
                                                Lihat
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state py-3">
                                Tiada transaksi pending untuk bulan ini.
                            </div>
                        @endforelse
                    </div>

                    @if($selectedPendingTransactions->count() > 5)
                        <div class="mt-3 text-end">
                            <a href="{{ request()->fullUrlWithQuery(['tab' => 'transactions', 'month' => $selectedMonth, 'status' => 'pending']) }}"
                            class="btn btn-sm btn-warning">
                                Lihat Semua Pending
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Transaksi Gagal --}}
                <div class="detail-card">
                    <div class="transaction-section-title">
                        <i class="ri-close-circle-line text-danger me-1"></i>
                        Transaksi Gagal
                    </div>

                    <div class="transaction-mini-list">
                        @forelse($selectedFailedTransactions->take(5) as $fee)
                            <div class="transaction-mini-item">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="transaction-avatar">
                                        {{ strtoupper(substr($fee->user_name ?? 'A', 0, 1)) }}
                                    </div>

                                    <div class="flex-fill">
                                        <div class="fw-bold">{{ $fee->user_name ?? '-' }}</div>
                                        <div class="muted-small">
                                            {{ $fee->receipt_no ?? '-' }} · {{ $fee->fee_label ?? '-' }}
                                        </div>
                                        <div class="muted-small">
                                            Reference: {{ $fee->payment_reference ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <div class="amount-text">
                                            RM {{ number_format($fee->total_paid_amount ?? $fee->amount ?? 0, 2) }}
                                        </div>

                                        @if(!empty($fee->user_id))
                                            <a href="{{ route('admin.khairat.fees.show', $fee->user_id) }}"
                                            class="btn btn-sm btn-outline-danger mt-2">
                                                Lihat
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state py-3">
                                Tiada transaksi gagal untuk bulan ini.
                            </div>
                        @endforelse
                    </div>

                    @if($selectedFailedTransactions->count() > 5)
                        <div class="mt-3 text-end">
                            <a href="{{ request()->fullUrlWithQuery(['tab' => 'transactions', 'month' => $selectedMonth, 'status' => 'failed']) }}"
                            class="btn btn-sm btn-danger">
                                Lihat Semua Gagal
                            </a>
                        </div>
                    @endif
                </div>

                @elseif($activeTab == 'type')
                    <div class="mb-4">
                        <div class="detail-title">Pecahan Jenis Yuran</div>
                        <div class="muted-small">Semakan kutipan berdasarkan kategori yuran.</div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-row">
                            <span>Yuran Pendaftaran</span>
                            <span>RM {{ number_format($totalRegistration, 2) }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Yuran Bulanan</span>
                            <span>RM {{ number_format($totalMonthly, 2) }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Yuran Tahunan</span>
                            <span>RM {{ number_format($totalAnnual, 2) }}</span>
                        </div>
                    </div>

                @elseif($activeTab == 'transactions')
                    <div class="mb-4">
                        <div class="detail-title">Detail Transaksi</div>
                        <div class="muted-small">Paparan rujukan transaksi untuk tujuan audit.</div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-row">
                            <span>Jumlah Rekod</span>
                            <span>{{ $groupedFees->count() }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Berjaya</span>
                            <span>{{ $paidCount }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Pending</span>
                            <span>{{ $pendingCount }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Gagal</span>
                            <span>{{ $failedCount }}</span>
                        </div>
                    </div>

                @elseif($activeTab == 'unpaid')
                    <div class="mb-4">
                        <div class="detail-title">Ahli Belum Bayar</div>
                        <div class="muted-small">Senarai ahli yang belum melengkapkan bayaran.</div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-row">
                            <span>Jumlah Ahli Belum Bayar</span>
                            <span>{{ count($unpaidMembers) }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Anggaran Tunggakan</span>
                            <span>RM {{ number_format(collect($unpaidMembers)->sum('arrears'), 2) }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Status</span>
                            <span>Perlu Semakan</span>
                        </div>
                    </div>

                @elseif($activeTab == 'arrears')
                    <div class="mb-4">
                        <div class="detail-title">Detail Tunggakan</div>
                        <div class="muted-small">Semakan ahli yang mempunyai tunggakan yuran.</div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-row">
                            <span>Jumlah Kes</span>
                            <span>{{ count($unpaidMembers) }} Ahli</span>
                        </div>
                        <div class="detail-row">
                            <span>Jumlah Anggaran</span>
                            <span>RM {{ number_format(collect($unpaidMembers)->sum('arrears'), 2) }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Tindakan</span>
                            <span>Semakan Admin</span>
                        </div>
                    </div>

                @elseif($activeTab == 'audit')
                    <div class="mb-4">
                        <div class="detail-title">Audit Bayaran</div>
                        <div class="muted-small">Rekod perubahan dan semakan transaksi bayaran.</div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-row">
                            <span>Log Terakhir</span>
                            <span>{{ now()->format('d/m/Y') }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Aktiviti</span>
                            <span>Laporan disemak</span>
                        </div>
                        <div class="detail-row">
                            <span>Oleh</span>
                            <span>Admin</span>
                        </div>
                    </div>

                @elseif($activeTab == 'export')
                    <div class="mb-4">
                        <div class="detail-title">Export Laporan</div>
                        <div class="muted-small">Pilih format laporan untuk simpanan atau audit.</div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-row">
                            <span>Format Excel</span>
                            <span>Untuk semakan data</span>
                        </div>
                        <div class="detail-row">
                            <span>Format PDF</span>
                            <span>Untuk laporan rasmi</span>
                        </div>
                        <div class="detail-row">
                            <span>Tempoh</span>
                            <span>{{ $currentYear }}</span>
                        </div>
                    </div>
                @endif

                <div class="audit-note mb-3">
                    <i class="ri-information-line me-1"></i>
                    Bahagian ini digunakan untuk semakan audit, rujukan transaksi, no resit dan jumlah kutipan yuran.
                </div>
            </div>

            <div class="export-footer">
                <div class="d-flex flex-wrap gap-2 justify-content-between">
                    <a href="{{ route('admin.khairat.fees.export.excel', request()->query()) }}" class="btn btn-success">
                        <i class="ri-file-excel-2-line me-1"></i>
                        Excel
                    </a>

                    <a href="{{ route('admin.khairat.fees.export.pdf', request()->query()) }}" class="btn btn-danger" target="_blank">
                        <i class="ri-file-pdf-2-line me-1"></i>
                        Preview PDF
                    </a>

                    <a href="{{ route('admin.khairat.fees.export.pdf', request()->query()) }}" class="btn btn-light" target="_blank">
                        <i class="ri-printer-line me-1"></i>
                        Cetak
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartElement = document.querySelector("#fee-column-datalabels");

        if (!chartElement || typeof ApexCharts === 'undefined') {
            return;
        }

        const options = {
            series: [{
                name: 'Jumlah Kutipan',
                data: @json($chartMonthAmounts)
            }],
            chart: {
                type: 'bar',
                height: 265,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '45%',
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val > 0 ? 'RM ' + Number(val).toFixed(0) : '';
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ['#304758']
                }
            },
            xaxis: {
                categories: @json($chartMonthLabels),
                position: 'bottom',
                labels: {
                    style: {
                        fontSize: '12px',
                        fontWeight: 600
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return 'RM ' + Number(val).toFixed(0);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return 'RM ' + Number(val).toFixed(2);
                    }
                }
            },
            colors: ['#845adf'],
            grid: {
                borderColor: '#f1f1f1'
            }
        };

        const chart = new ApexCharts(chartElement, options);
        chart.render();
    });
</script>
@endpush
@endsection