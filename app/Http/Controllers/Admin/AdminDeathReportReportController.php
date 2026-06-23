<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeathReport;
use App\Models\Dependent;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminDeathReportReportController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Filter Laporan
        |--------------------------------------------------------------------------
        */
        $year = (int) $request->get('year', now()->year);
        $month = $request->get('month');
        $memberType = $request->get('member_type');
        $gender = $request->get('gender');

        /*
        |--------------------------------------------------------------------------
        | Tahun Dropdown
        |--------------------------------------------------------------------------
        */
        $currentYear = now()->year;
        $years = range($currentYear, $currentYear - 5);

        /*
        |--------------------------------------------------------------------------
        | Status Hidup / Meninggal
        |--------------------------------------------------------------------------
        */
        $aliveStatusValues = ['aktif', 'hidup', 'active'];
        $deceasedStatusValues = ['meninggal', 'meninggal_dunia', 'sudah_meninggal', 'deceased'];

        /*
        |--------------------------------------------------------------------------
        | Query Ahli Utama
        |--------------------------------------------------------------------------
        */
        $mainMemberQuery = UserProfile::query()
            ->whereIn('status_permohonan', ['approved', 'active'])
            ->whereNotExists(function ($query) {
                $query->selectRaw(1)
                    ->from('dependents')
                    ->whereColumn('dependents.no_kp', 'user_profiles.no_kp');
            });

        /*
        |--------------------------------------------------------------------------
        | Query Tanggungan
        |--------------------------------------------------------------------------
        */
        $dependentQuery = Dependent::query();

        /*
        |--------------------------------------------------------------------------
        | Helper Filter Jantina
        |--------------------------------------------------------------------------
        | user_profiles ada column jantina.
        | dependents tiada column jantina.
        |
        | Jadi untuk dependents, jantina dikira melalui no_kp:
        | nombor akhir IC ganjil = lelaki
        | nombor akhir IC genap = perempuan
        */
        $applyGenderFilter = function ($query, string $table, ?string $gender) {
            if (empty($gender)) {
                return $query;
            }

            $gender = strtolower($gender);

            /*
            | Kalau table memang ada column jantina, guna column jantina.
            */
            if (Schema::hasColumn($table, 'jantina')) {
                if (in_array($gender, ['lelaki', 'male'])) {
                    return $query->whereRaw("LOWER({$table}.jantina) IN (?, ?)", ['lelaki', 'male']);
                }

                if (in_array($gender, ['perempuan', 'female'])) {
                    return $query->whereRaw("LOWER({$table}.jantina) IN (?, ?)", ['perempuan', 'female']);
                }

                return $query;
            }

            /*
            | Kalau table tiada column jantina, guna no_kp.
            */
            if (Schema::hasColumn($table, 'no_kp')) {
                $icColumn = "{$table}.no_kp";
                $cleanIc = "REPLACE(REPLACE(COALESCE({$icColumn}, ''), '-', ''), ' ', '')";

                if (in_array($gender, ['lelaki', 'male'])) {
                    return $query
                        ->whereRaw("{$cleanIc} REGEXP '^[0-9]+$'")
                        ->whereRaw("MOD(CAST(RIGHT({$cleanIc}, 1) AS UNSIGNED), 2) = 1");
                }

                if (in_array($gender, ['perempuan', 'female'])) {
                    return $query
                        ->whereRaw("{$cleanIc} REGEXP '^[0-9]+$'")
                        ->whereRaw("MOD(CAST(RIGHT({$cleanIc}, 1) AS UNSIGNED), 2) = 0");
                }
            }

            /*
            | Kalau tak boleh tentukan jantina, jangan kira.
            */
            return $query->whereRaw('1 = 0');
        };

        /*
        |--------------------------------------------------------------------------
        | Jumlah Ahli Berdaftar
        |--------------------------------------------------------------------------
        */
        $totalMainMembers = (clone $mainMemberQuery)->count();
        $totalDependents = (clone $dependentQuery)->count();
        $totalMembers = $totalMainMembers + $totalDependents;

        /*
        |--------------------------------------------------------------------------
        | Jumlah Meninggal Dunia
        |--------------------------------------------------------------------------
        */
        $mainDeceased = (clone $mainMemberQuery)
            ->whereIn('status_kehidupan', $deceasedStatusValues)
            ->count();

        $dependentDeceased = (clone $dependentQuery)
            ->where(function ($query) use ($deceasedStatusValues) {
                $query->whereIn('status_kehidupan', $deceasedStatusValues)
                    ->orWhere('status_tanggungan', 'meninggal');
            })
            ->count();

        $totalDeceased = $mainDeceased + $dependentDeceased;

        /*
        |--------------------------------------------------------------------------
        | Jumlah Masih Hidup
        |--------------------------------------------------------------------------
        */
        $mainAlive = (clone $mainMemberQuery)
            ->where(function ($query) use ($aliveStatusValues, $deceasedStatusValues) {
                $query->whereIn('status_kehidupan', $aliveStatusValues)
                    ->orWhereNull('status_kehidupan')
                    ->orWhereNotIn('status_kehidupan', $deceasedStatusValues);
            })
            ->count();

        $dependentAlive = (clone $dependentQuery)
            ->where(function ($query) use ($aliveStatusValues, $deceasedStatusValues) {
                $query->whereIn('status_kehidupan', $aliveStatusValues)
                    ->orWhereNull('status_kehidupan')
                    ->orWhereNotIn('status_kehidupan', $deceasedStatusValues);
            })
            ->where(function ($query) {
                $query->whereNull('status_tanggungan')
                    ->orWhere('status_tanggungan', '!=', 'meninggal');
            })
            ->count();

        $alive = $mainAlive + $dependentAlive;

        /*
        |--------------------------------------------------------------------------
        | Query Detail Death Reports
        |--------------------------------------------------------------------------
        */
        $deathReportQuery = DeathReport::query()
            ->with(['burialPlot']);

        /*
        |--------------------------------------------------------------------------
        | Filter Tahun
        |--------------------------------------------------------------------------
        */
        $deathReportQuery->whereYear('tarikh_meninggal', $year);

        /*
        |--------------------------------------------------------------------------
        | Filter Bulan
        |--------------------------------------------------------------------------
        */
        if (!empty($month)) {
            $deathReportQuery->whereMonth('tarikh_meninggal', $month);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Jenis Ahli
        |--------------------------------------------------------------------------
        */
        if ($memberType === 'utama') {
            $deathReportQuery->whereIn('deceased_type', ['utama', 'user', 'main']);
        }

        if ($memberType === 'tanggungan') {
            $deathReportQuery->whereIn('deceased_type', ['tanggungan', 'dependent']);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Jantina
        |--------------------------------------------------------------------------
        */
        if (!empty($gender)) {
            $gender = strtolower($gender);

            $deathReportQuery->where(function ($query) use ($gender) {
                if (in_array($gender, ['lelaki', 'male'])) {
                    $query->whereRaw('LOWER(jantina) IN (?, ?)', ['lelaki', 'male']);
                }

                if (in_array($gender, ['perempuan', 'female'])) {
                    $query->whereRaw('LOWER(jantina) IN (?, ?)', ['perempuan', 'female']);
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Senarai Detail Kematian
        |--------------------------------------------------------------------------
        */
        $deathReports = $deathReportQuery
            ->orderByDesc('tarikh_meninggal')
            ->orderByDesc('created_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Kematian Tahun Dipilih
        |--------------------------------------------------------------------------
        */
        $deathThisYear = DeathReport::query()
            ->whereYear('tarikh_meninggal', $year)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Kematian Ahli Utama Tahun Dipilih
        |--------------------------------------------------------------------------
        */
        $mainDeceasedThisYear = DeathReport::query()
            ->whereYear('tarikh_meninggal', $year)
            ->whereIn('deceased_type', ['utama', 'user', 'main'])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Kematian Tanggungan Tahun Dipilih
        |--------------------------------------------------------------------------
        */
        $dependentDeceasedThisYear = DeathReport::query()
            ->whereYear('tarikh_meninggal', $year)
            ->whereIn('deceased_type', ['tanggungan', 'dependent'])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Pecahan Jantina Tahun Dipilih
        |--------------------------------------------------------------------------
        */
        $maleDeceased = DeathReport::query()
            ->whereYear('tarikh_meninggal', $year)
            ->whereRaw('LOWER(jantina) IN (?, ?)', ['lelaki', 'male'])
            ->count();

        $femaleDeceased = DeathReport::query()
            ->whereYear('tarikh_meninggal', $year)
            ->whereRaw('LOWER(jantina) IN (?, ?)', ['perempuan', 'female'])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Summary Card
        |--------------------------------------------------------------------------
        */
        $summary = [
            'total_members' => $totalMembers,
            'alive' => $alive,
            'deceased' => $totalDeceased,
            'death_this_year' => $deathThisYear,

            /*
            | Ini ikut status sebenar ahli dalam sistem.
            | Supaya jumlah Ahli Utama Meninggal + Tanggungan Meninggal
            | sama dengan jumlah Meninggal Dunia.
            */
            'main_deceased' => $mainDeceased,
            'dependent_deceased' => $dependentDeceased,

            /*
            | Ini masih ikut death_reports sebab jantina kematian disimpan di sana.
            */
            'male_deceased' => $maleDeceased,
            'female_deceased' => $femaleDeceased,
        ];

        /*
        |--------------------------------------------------------------------------
        | Ringkasan Ahli Mengikut Status
        |--------------------------------------------------------------------------
        */
        $countMainForTable = function (string $lifeStatus, ?string $gender = null) use (
            $mainMemberQuery,
            $aliveStatusValues,
            $deceasedStatusValues,
            $applyGenderFilter
        ) {
            $query = clone $mainMemberQuery;

            if ($lifeStatus === 'alive') {
                $query->where(function ($sub) use ($aliveStatusValues, $deceasedStatusValues) {
                    $sub->whereIn('status_kehidupan', $aliveStatusValues)
                        ->orWhereNull('status_kehidupan')
                        ->orWhereNotIn('status_kehidupan', $deceasedStatusValues);
                });
            }

            if ($lifeStatus === 'dead') {
                $query->whereIn('status_kehidupan', $deceasedStatusValues);
            }

            $query = $applyGenderFilter($query, 'user_profiles', $gender);

            return $query->count();
        };

        $countDependentForTable = function (string $lifeStatus, ?string $gender = null) use (
            $dependentQuery,
            $aliveStatusValues,
            $deceasedStatusValues,
            $applyGenderFilter
        ) {
            $query = clone $dependentQuery;

            if ($lifeStatus === 'alive') {
                $query->where(function ($sub) use ($aliveStatusValues, $deceasedStatusValues) {
                    $sub->whereIn('status_kehidupan', $aliveStatusValues)
                        ->orWhereNull('status_kehidupan')
                        ->orWhereNotIn('status_kehidupan', $deceasedStatusValues);
                });

                $query->where(function ($sub) {
                    $sub->whereNull('status_tanggungan')
                        ->orWhere('status_tanggungan', '!=', 'meninggal');
                });
            }

            if ($lifeStatus === 'dead') {
                $query->where(function ($sub) use ($deceasedStatusValues) {
                    $sub->whereIn('status_kehidupan', $deceasedStatusValues)
                        ->orWhere('status_tanggungan', 'meninggal');
                });
            }

            $query = $applyGenderFilter($query, 'dependents', $gender);

            return $query->count();
        };

        $buildSummaryTableRow = function (string $jenis, $counter) {
            $hidupLelaki = $counter('alive', 'lelaki');
            $hidupPerempuan = $counter('alive', 'perempuan');

            $meninggalLelaki = $counter('dead', 'lelaki');
            $meninggalPerempuan = $counter('dead', 'perempuan');

            return [
                'jenis' => $jenis,

                'hidup_lelaki' => $hidupLelaki,
                'hidup_perempuan' => $hidupPerempuan,
                'jumlah_hidup' => $hidupLelaki + $hidupPerempuan,

                'meninggal_lelaki' => $meninggalLelaki,
                'meninggal_perempuan' => $meninggalPerempuan,
                'jumlah_meninggal' => $meninggalLelaki + $meninggalPerempuan,

                'jumlah_ahli' => $hidupLelaki + $hidupPerempuan + $meninggalLelaki + $meninggalPerempuan,
            ];
        };

        $mainSummaryRow = $buildSummaryTableRow('Ahli Utama', $countMainForTable);
        $dependentSummaryRow = $buildSummaryTableRow('Tanggungan', $countDependentForTable);

        $summaryRows = collect([
            $mainSummaryRow,
            $dependentSummaryRow,
        ]);

        $totalSummaryRow = [
            'jenis' => 'Jumlah',

            'hidup_lelaki' => $summaryRows->sum('hidup_lelaki'),
            'hidup_perempuan' => $summaryRows->sum('hidup_perempuan'),
            'jumlah_hidup' => $summaryRows->sum('jumlah_hidup'),

            'meninggal_lelaki' => $summaryRows->sum('meninggal_lelaki'),
            'meninggal_perempuan' => $summaryRows->sum('meninggal_perempuan'),
            'jumlah_meninggal' => $summaryRows->sum('jumlah_meninggal'),

            'jumlah_ahli' => $summaryRows->sum('jumlah_ahli'),
        ];

        $summaryTable = $summaryRows->push($totalSummaryRow);

        /*
        |--------------------------------------------------------------------------
        | Nama Bulan
        |--------------------------------------------------------------------------
        */
        $monthOptions = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Mac',
            4 => 'April',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Julai',
            8 => 'Ogos',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Disember',
        ];

        /*
        |--------------------------------------------------------------------------
        | Ringkasan Bulanan
        |--------------------------------------------------------------------------
        */
        $monthlySummary = collect();

        foreach ($monthOptions as $monthNo => $monthName) {
            $monthlyBaseQuery = DeathReport::query()
                ->whereYear('tarikh_meninggal', $year)
                ->whereMonth('tarikh_meninggal', $monthNo);

            $monthlyTotal = (clone $monthlyBaseQuery)->count();

            $monthlyMain = (clone $monthlyBaseQuery)
                ->whereIn('deceased_type', ['utama', 'user', 'main'])
                ->count();

            $monthlyDependent = (clone $monthlyBaseQuery)
                ->whereIn('deceased_type', ['tanggungan', 'dependent'])
                ->count();

            $monthlyMale = (clone $monthlyBaseQuery)
                ->whereRaw('LOWER(jantina) IN (?, ?)', ['lelaki', 'male'])
                ->count();

            $monthlyFemale = (clone $monthlyBaseQuery)
                ->whereRaw('LOWER(jantina) IN (?, ?)', ['perempuan', 'female'])
                ->count();

            $monthlySummary->push([
                'month_no' => $monthNo,
                'month_name' => $monthName,
                'total' => $monthlyTotal,
                'main' => $monthlyMain,
                'dependent' => $monthlyDependent,
                'male' => $monthlyMale,
                'female' => $monthlyFemale,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Data Chart
        |--------------------------------------------------------------------------
        */
        $chartLabels = $monthlySummary->pluck('month_name')->toArray();
        $chartTotals = $monthlySummary->pluck('total')->toArray();

        return view('admin.reports.deaths.index', compact(
            'year',
            'years',
            'month',
            'memberType',
            'gender',
            'summary',
            'summaryTable',
            'monthlySummary',
            'deathReports',
            'chartLabels',
            'chartTotals'
        ));
    }
}