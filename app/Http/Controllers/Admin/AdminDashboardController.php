<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Models\Dependent;
use App\Models\DeathReport;
use App\Models\GraveOrder;

class AdminDashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Status Yang Diterima
        |--------------------------------------------------------------------------
        */
        $approvedStatuses = [
            'approved',
            'active',
            'lulus',
            'diterima',
            'completed',
            'selesai',
        ];

        /*
        |--------------------------------------------------------------------------
        | Query Ahli Utama
        |--------------------------------------------------------------------------
        | Dashboard mesti kira daripada user_profiles, bukan users.
        | Ini supaya jumlah dashboard sama dengan Senarai Ahli Khairat.
        */
        $mainMemberQuery = UserProfile::where(function ($query) use ($approvedStatuses) {
                $query->whereNull('status_permohonan')
                    ->orWhere('status_permohonan', '')
                    ->orWhereIn('status_permohonan', $approvedStatuses);
            })
            ->whereHas('user', function ($query) {
                $query->where(function ($q) {
                    $q->whereIn('account_type', [
                            'utama',
                            'main_member',
                            'main',
                            'ahli_utama',
                            'member',
                        ])
                        ->orWhereNull('account_type')
                        ->orWhere('account_type', '');
                });
            });

        /*
        |--------------------------------------------------------------------------
        | Jumlah Ahli Utama
        |--------------------------------------------------------------------------
        */
        $totalAhliUtama = (clone $mainMemberQuery)->count();

        /*
        |--------------------------------------------------------------------------
        | Jumlah Tanggungan
        |--------------------------------------------------------------------------
        | Kira tanggungan yang dimiliki oleh ahli utama sahaja.
        */
        $mainMemberUserIds = (clone $mainMemberQuery)->pluck('user_id');

        $totalTanggungan = Dependent::whereIn('user_id', $mainMemberUserIds)->count();

        /*
        |--------------------------------------------------------------------------
        | Laporan Kematian Menunggu Semakan
        |--------------------------------------------------------------------------
        */
        $laporanKematianMenunggu = DeathReport::whereIn('status', [
            'pending',
            'menunggu',
            'menunggu_semakan',
            'belum_disahkan',
        ])->count();

        /*
        |--------------------------------------------------------------------------
        | Tempahan Kepukan Menunggu Kelulusan
        |--------------------------------------------------------------------------
        */
        $tempahanKepukanMenunggu = GraveOrder::whereIn('status', [
            'pending',
            'menunggu',
            'menunggu_kelulusan',
        ])->count();

        return view('admin.dashboard', compact(
            'totalAhliUtama',
            'totalTanggungan',
            'laporanKematianMenunggu',
            'tempahanKepukanMenunggu'
        ));
    }
}