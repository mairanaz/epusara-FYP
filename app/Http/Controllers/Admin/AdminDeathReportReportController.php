<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeathReport;
use App\Models\Dependent;
use App\Models\UserProfile;
use Illuminate\Http\Request;

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
        | Berdasarkan table awak:
        | user_profiles.status_kehidupan default = aktif
        | dependents.status_kehidupan default = aktif
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
            ->whereIn('status_kehidupan', $aliveStatusValues)
            ->count();

        $dependentAlive = (clone $dependentQuery)
            ->whereIn('status_kehidupan', $aliveStatusValues)
            ->where('status_tanggungan', '!=', 'meninggal')
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
            $deathReportQuery->where(function ($query) use ($gender) {
                $query->where('jantina', $gender)
                    ->orWhere('jantina', ucfirst($gender))
                    ->orWhere('jantina', strtoupper($gender));
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
            ->where(function ($query) {
                $query->where('jantina', 'lelaki')
                    ->orWhere('jantina', 'Lelaki')
                    ->orWhere('jantina', 'LELAKI')
                    ->orWhere('jantina', 'male')
                    ->orWhere('jantina', 'Male');
            })
            ->count();

        $femaleDeceased = DeathReport::query()
            ->whereYear('tarikh_meninggal', $year)
            ->where(function ($query) {
                $query->where('jantina', 'perempuan')
                    ->orWhere('jantina', 'Perempuan')
                    ->orWhere('jantina', 'PEREMPUAN')
                    ->orWhere('jantina', 'female')
                    ->orWhere('jantina', 'Female');
            })
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
            | Ini ikut rekod death_reports tahun dipilih.
            | Lebih sesuai untuk laporan tahunan.
            */
            'main_deceased' => $mainDeceasedThisYear,
            'dependent_deceased' => $dependentDeceasedThisYear,
            'male_deceased' => $maleDeceased,
            'female_deceased' => $femaleDeceased,
        ];

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
                ->where(function ($query) {
                    $query->where('jantina', 'lelaki')
                        ->orWhere('jantina', 'Lelaki')
                        ->orWhere('jantina', 'LELAKI')
                        ->orWhere('jantina', 'male')
                        ->orWhere('jantina', 'Male');
                })
                ->count();

            $monthlyFemale = (clone $monthlyBaseQuery)
                ->where(function ($query) {
                    $query->where('jantina', 'perempuan')
                        ->orWhere('jantina', 'Perempuan')
                        ->orWhere('jantina', 'PEREMPUAN')
                        ->orWhere('jantina', 'female')
                        ->orWhere('jantina', 'Female');
                })
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
            'monthlySummary',
            'deathReports',
            'chartLabels',
            'chartTotals'
        ));
    }
}