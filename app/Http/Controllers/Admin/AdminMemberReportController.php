<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Models\Dependent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdminMemberReportController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->get('year', now()->year);
        $month = $request->get('month');
        $recordType = $request->get('record_type');
        $statusKehidupan = $request->get('status_kehidupan');
        $gender = $request->get('gender');
        $search = $request->get('search');

        $years = range(now()->year, now()->year - 5);

        /*
        |--------------------------------------------------------------------------
        | Data Ahli Utama
        |--------------------------------------------------------------------------
        */
        $mainMembers = UserProfile::with('user')
            ->get()
            ->map(function ($profile) {
                $noKp = $profile->no_kp ?? null;
                $status = strtolower($profile->status_kehidupan ?? 'aktif');

                $isDeceased = in_array($status, [
                    'meninggal',
                    'meninggal dunia',
                    'meninggal_dunia',
                    'mati',
                ]);

                return [
                    'id' => $profile->id,
                    'name' => $profile->nama ?? $profile->user?->name ?? '-',
                    'type' => 'utama',
                    'type_label' => 'Ahli Utama',
                    'relation' => 'Ahli Utama',
                    'no_kp' => $noKp,
                    'phone' => $profile->no_tel_bimbit ?? $profile->no_tel ?? '-',
                    'gender' => $this->normalizeGender($profile->jantina ?? $this->guessGenderFromIc($noKp)),
                    'age' => $this->calculateAgeFromIc($noKp),
                    'life_status' => $isDeceased ? 'meninggal' : 'aktif',
                    'life_status_label' => $isDeceased ? 'Meninggal Dunia' : 'Masih Hidup',
                    'payment_plan' => $profile->payment_plan ?? '-',
                    'registered_at' => $profile->created_at ?? $profile->user?->created_at,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Data Tanggungan
        |--------------------------------------------------------------------------
        */
        $dependents = Dependent::with(['user', 'user.profile'])
            ->get()
            ->map(function ($dependent) {
                $noKp = $dependent->no_kp ?? null;
                $hasDeathReport = (bool) ($dependent->has_death_report ?? false);

                return [
                    'id' => $dependent->id,
                    'name' => $dependent->name ?? '-',
                    'type' => 'tanggungan',
                    'type_label' => 'Tanggungan',
                    'relation' => ucfirst($dependent->pertalian ?? 'Tanggungan'),
                    'no_kp' => $noKp,
                    'phone' => $dependent->no_tel ?? '-',
                    'gender' => $this->normalizeGender($dependent->jantina ?? $this->guessGenderFromIc($noKp)),
                    'age' => $this->calculateAgeFromIc($noKp),
                    'life_status' => $hasDeathReport ? 'meninggal' : 'aktif',
                    'life_status_label' => $hasDeathReport ? 'Meninggal Dunia' : 'Masih Hidup',
                    'payment_plan' => '-',
                    'registered_at' => $dependent->created_at,
                ];
            });

        $allRecords = $mainMembers->merge($dependents)->values();

        /*
        |--------------------------------------------------------------------------
        | Summary Keseluruhan
        |--------------------------------------------------------------------------
        */
        $summary = [
            'total_main_members' => $mainMembers->count(),
            'total_dependents' => $dependents->count(),
            'total_all_members' => $allRecords->count(),
            'alive' => $allRecords->where('life_status', 'aktif')->count(),
            'deceased' => $allRecords->where('life_status', 'meninggal')->count(),
            'male' => $allRecords->where('gender', 'lelaki')->count(),
            'female' => $allRecords->where('gender', 'perempuan')->count(),
            'new_this_year' => $allRecords->filter(function ($record) use ($year) {
                return !empty($record['registered_at'])
                    && Carbon::parse($record['registered_at'])->year === $year;
            })->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Filter untuk jadual detail
        |--------------------------------------------------------------------------
        */
        $filteredRecords = $allRecords;

        if ($recordType) {
            $filteredRecords = $filteredRecords->where('type', $recordType)->values();
        }

        if ($statusKehidupan) {
            $filteredRecords = $filteredRecords->where('life_status', $statusKehidupan)->values();
        }

        if ($gender) {
            $filteredRecords = $filteredRecords->where('gender', $gender)->values();
        }

        if ($month) {
            $filteredRecords = $filteredRecords->filter(function ($record) use ($year, $month) {
                if (empty($record['registered_at'])) {
                    return false;
                }

                $date = Carbon::parse($record['registered_at']);

                return (int) $date->year === (int) $year
                    && (int) $date->month === (int) $month;
            })->values();
        } else {
            $filteredRecords = $filteredRecords->filter(function ($record) use ($year) {
                if (empty($record['registered_at'])) {
                    return false;
                }

                return (int) Carbon::parse($record['registered_at'])->year === (int) $year;
            })->values();
        }

        if ($search) {
            $filteredRecords = $filteredRecords->filter(function ($record) use ($search) {
                $keyword = strtolower($search);

                return str_contains(strtolower($record['name'] ?? ''), $keyword)
                    || str_contains(strtolower($record['no_kp'] ?? ''), $keyword)
                    || str_contains(strtolower($record['phone'] ?? ''), $keyword)
                    || str_contains(strtolower($record['relation'] ?? ''), $keyword);
            })->values();
        }

        /*
        |--------------------------------------------------------------------------
        | Carta Pendaftaran Bulanan
        |--------------------------------------------------------------------------
        */
        $monthNames = collect([
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mac',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Ogos',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Dis',
        ]);

        $monthlySummary = $monthNames->map(function ($monthName, $monthNo) use ($allRecords, $year) {
            $records = $allRecords->filter(function ($record) use ($year, $monthNo) {
                if (empty($record['registered_at'])) {
                    return false;
                }

                $date = Carbon::parse($record['registered_at']);

                return (int) $date->year === (int) $year
                    && (int) $date->month === (int) $monthNo;
            });

            return [
                'month_no' => $monthNo,
                'month_name' => $monthName,
                'total' => $records->count(),
                'main' => $records->where('type', 'utama')->count(),
                'dependent' => $records->where('type', 'tanggungan')->count(),
            ];
        })->values();

        /*
        |--------------------------------------------------------------------------
        | Kumpulan Umur
        |--------------------------------------------------------------------------
        */
        $ageGroups = [
            '0-17' => $allRecords->filter(fn ($record) => $record['age'] !== null && $record['age'] <= 17)->count(),
            '18-30' => $allRecords->filter(fn ($record) => $record['age'] !== null && $record['age'] >= 18 && $record['age'] <= 30)->count(),
            '31-45' => $allRecords->filter(fn ($record) => $record['age'] !== null && $record['age'] >= 31 && $record['age'] <= 45)->count(),
            '46-60' => $allRecords->filter(fn ($record) => $record['age'] !== null && $record['age'] >= 46 && $record['age'] <= 60)->count(),
            '60+' => $allRecords->filter(fn ($record) => $record['age'] !== null && $record['age'] > 60)->count(),
            'Tidak Diketahui' => $allRecords->filter(fn ($record) => $record['age'] === null)->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Pagination manual untuk collection
        |--------------------------------------------------------------------------
        */
        $filteredRecords = $filteredRecords
            ->sortBy('name')
            ->values();

        $memberRecords = $this->paginateCollection(
            $filteredRecords,
            10,
            $request
        );

        return view('admin.reports.members.index', compact(
            'summary',
            'years',
            'year',
            'month',
            'recordType',
            'statusKehidupan',
            'gender',
            'search',
            'monthNames',
            'monthlySummary',
            'ageGroups',
            'memberRecords'
        ));
    }

    private function paginateCollection(Collection $items, int $perPage, Request $request): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    private function normalizeGender(?string $gender): string
    {
        $gender = strtolower(trim($gender ?? ''));

        return match ($gender) {
            'lelaki', 'male', 'l' => 'lelaki',
            'perempuan', 'female', 'p' => 'perempuan',
            default => 'tidak_diketahui',
        };
    }

    private function guessGenderFromIc(?string $ic): ?string
    {
        $ic = preg_replace('/[^0-9]/', '', $ic ?? '');

        if (strlen($ic) < 12) {
            return null;
        }

        $lastDigit = (int) substr($ic, -1);

        return $lastDigit % 2 === 0 ? 'perempuan' : 'lelaki';
    }

    private function calculateAgeFromIc(?string $ic): ?int
    {
        $ic = preg_replace('/[^0-9]/', '', $ic ?? '');

        if (strlen($ic) < 6) {
            return null;
        }

        $yy = (int) substr($ic, 0, 2);
        $mm = (int) substr($ic, 2, 2);
        $dd = (int) substr($ic, 4, 2);

        if ($mm < 1 || $mm > 12 || $dd < 1 || $dd > 31) {
            return null;
        }

        $currentYY = (int) now()->format('y');
        $year = $yy <= $currentYY ? 2000 + $yy : 1900 + $yy;

        try {
            return Carbon::createFromDate($year, $mm, $dd)->age;
        } catch (\Exception $e) {
            return null;
        }
    }
}