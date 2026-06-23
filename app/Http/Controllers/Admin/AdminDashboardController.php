<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Statistik Utama Dashboard Admin
        |--------------------------------------------------------------------------
        */

        $totalAhli = $this->safeCount('users', function ($query) {
            if (Schema::hasColumn('users', 'role')) {
                $query->where('role', 'user');
            }
        });

        $totalTanggungan = $this->safeCount('dependents');

        $permohonanAhliMenunggu = $this->safeCount('users', function ($query) {
            if (Schema::hasColumn('users', 'status_ahli')) {
                $query->whereIn('status_ahli', [
                    'pending',
                    'menunggu',
                    'belum_disahkan',
                    'menunggu_pengesahan'
                ]);
            } elseif (Schema::hasColumn('users', 'status')) {
                $query->whereIn('status', [
                    'pending',
                    'menunggu',
                    'belum_disahkan',
                    'menunggu_pengesahan'
                ]);
            } else {
                $query->whereRaw('1 = 0');
            }
        });

        $kematianMenunggu = $this->safeCount('death_reports', function ($query) {
            if (Schema::hasColumn('death_reports', 'status_pengesahan')) {
                $query->whereIn('status_pengesahan', [
                    'pending',
                    'menunggu',
                    'belum_disahkan',
                    'menunggu_pengesahan'
                ]);
            } elseif (Schema::hasColumn('death_reports', 'status')) {
                $query->whereIn('status', [
                    'pending',
                    'menunggu',
                    'belum_disahkan',
                    'menunggu_pengesahan'
                ]);
            }
        });

        $tempahanMenunggu = $this->safeCount('grave_orders', function ($query) {
            if (Schema::hasColumn('grave_orders', 'status_tempahan')) {
                $query->whereIn('status_tempahan', [
                    'pending',
                    'menunggu',
                    'belum_disahkan',
                    'menunggu_pengesahan'
                ]);
            } elseif (Schema::hasColumn('grave_orders', 'status')) {
                $query->whereIn('status', [
                    'pending',
                    'menunggu',
                    'belum_disahkan',
                    'menunggu_pengesahan'
                ]);
            }
        });

        $lotTersedia = $this->safeCount('burial_plots', function ($query) {
            if (Schema::hasColumn('burial_plots', 'status_lot')) {
                $query->whereIn('status_lot', [
                    'tersedia',
                    'kosong',
                    'available'
                ]);
            } elseif (Schema::hasColumn('burial_plots', 'status')) {
                $query->whereIn('status', [
                    'tersedia',
                    'kosong',
                    'available'
                ]);
            }
        });

        $jumlahRekodKubur = $this->safeCount('grave_records');

        /*
        |--------------------------------------------------------------------------
        | Jumlah Yuran Bulan Ini
        |--------------------------------------------------------------------------
        */

        $jumlahYuranBulanIni = 0;

        if (Schema::hasTable('payments')) {
            $paymentQuery = DB::table('payments');

            if (Schema::hasColumn('payments', 'created_at')) {
                $paymentQuery->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            }

            if (Schema::hasColumn('payments', 'jumlah')) {
                $jumlahYuranBulanIni = $paymentQuery->sum('jumlah');
            } elseif (Schema::hasColumn('payments', 'amount')) {
                $jumlahYuranBulanIni = $paymentQuery->sum('amount');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Chart Status Tempahan Kepukan
        |--------------------------------------------------------------------------
        */

        $statusTempahan = [
            'Menunggu' => 0,
            'Diluluskan' => 0,
            'Ditolak' => 0,
            'Selesai' => 0,
        ];

        if (Schema::hasTable('grave_orders')) {
            $statusColumn = null;

            if (Schema::hasColumn('grave_orders', 'status_tempahan')) {
                $statusColumn = 'status_tempahan';
            } elseif (Schema::hasColumn('grave_orders', 'status')) {
                $statusColumn = 'status';
            }

            if ($statusColumn !== null) {
                $rawStatus = DB::table('grave_orders')
                    ->select($statusColumn, DB::raw('COUNT(*) as total'))
                    ->groupBy($statusColumn)
                    ->pluck('total', $statusColumn);

                foreach ($rawStatus as $status => $total) {
                    $label = $this->normalizeStatusLabel($status);

                    if (isset($statusTempahan[$label])) {
                        $statusTempahan[$label] += $total;
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Chart Laporan Kematian Bulanan
        |--------------------------------------------------------------------------
        */

        $bulanLabels = [];
        $kematianBulanan = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $bulanLabels[] = $date->format('M Y');

            $count = 0;

            if (Schema::hasTable('death_reports')) {
                $dateColumn = null;

                if (Schema::hasColumn('death_reports', 'tarikh_meninggal')) {
                    $dateColumn = 'tarikh_meninggal';
                } elseif (Schema::hasColumn('death_reports', 'created_at')) {
                    $dateColumn = 'created_at';
                }

                if ($dateColumn !== null) {
                    $count = DB::table('death_reports')
                        ->whereMonth($dateColumn, $date->month)
                        ->whereYear($dateColumn, $date->year)
                        ->count();
                }
            }

            $kematianBulanan[] = $count;
        }

        /*
        |--------------------------------------------------------------------------
        | Permohonan Ahli Terkini
        |--------------------------------------------------------------------------
        */

        $permohonanTerkini = collect();

        if (Schema::hasTable('users')) {
            $selectNama = Schema::hasColumn('users', 'name') ? 'name' : DB::raw("'-'");
            $selectStatus = Schema::hasColumn('users', 'status_ahli')
                ? 'status_ahli'
                : (Schema::hasColumn('users', 'status') ? 'status' : DB::raw("'Aktif'"));

            $permohonanTerkini = DB::table('users')
                ->select(
                    'id',
                    DB::raw($this->columnOrValue('users', 'name', "'-'") . ' as nama'),
                    DB::raw($this->columnOrValue('users', 'status_ahli', $this->columnOrValue('users', 'status', "'Aktif'")) . ' as status'),
                    DB::raw($this->columnOrValue('users', 'created_at', 'NULL') . ' as created_at')
                )
                ->when(Schema::hasColumn('users', 'role'), function ($query) {
                    $query->where('role', 'user');
                })
                ->orderByDesc(Schema::hasColumn('users', 'created_at') ? 'created_at' : 'id')
                ->limit(5)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Tempahan Kepukan Terkini
        |--------------------------------------------------------------------------
        */

        $tempahanTerkini = collect();

        if (Schema::hasTable('grave_orders')) {
            $tempahanTerkini = DB::table('grave_orders')
                ->select(
                    'id',
                    DB::raw($this->columnOrValue('grave_orders', 'nama_pemohon', "'-'") . ' as nama_pemohon'),
                    DB::raw($this->columnOrValue('grave_orders', 'status_tempahan', $this->columnOrValue('grave_orders', 'status', "'-'")) . ' as status'),
                    DB::raw($this->columnOrValue('grave_orders', 'created_at', 'NULL') . ' as created_at')
                )
                ->orderByDesc(Schema::hasColumn('grave_orders', 'created_at') ? 'created_at' : 'id')
                ->limit(5)
                ->get();
        }

        return view('admin.dashboard', compact(
            'totalAhli',
            'totalTanggungan',
            'permohonanAhliMenunggu',
            'kematianMenunggu',
            'tempahanMenunggu',
            'lotTersedia',
            'jumlahRekodKubur',
            'jumlahYuranBulanIni',
            'statusTempahan',
            'bulanLabels',
            'kematianBulanan',
            'permohonanTerkini',
            'tempahanTerkini'
        ));
    }

    private function safeCount($table, $callback = null)
    {
        if (!Schema::hasTable($table)) {
            return 0;
        }

        $query = DB::table($table);

        if ($callback) {
            $callback($query);
        }

        return $query->count();
    }

    private function columnOrValue($table, $column, $fallback)
    {
        return Schema::hasColumn($table, $column) ? $column : $fallback;
    }

    private function normalizeStatusLabel($status)
    {
        $status = strtolower((string) $status);

        return match ($status) {
            'pending',
            'menunggu',
            'belum_disahkan',
            'menunggu_pengesahan' => 'Menunggu',

            'approved',
            'approve',
            'diluluskan',
            'lulus' => 'Diluluskan',

            'rejected',
            'reject',
            'ditolak',
            'tolak' => 'Ditolak',

            'completed',
            'complete',
            'selesai' => 'Selesai',

            default => 'Menunggu',
        };
    }
}