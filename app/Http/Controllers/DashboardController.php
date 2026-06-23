<?php

namespace App\Http\Controllers;

use App\Models\DeathReport;
use App\Models\Dependent;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD AHLI UTAMA
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | SEMAKAN JENIS PENGGUNA
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->account_type === null) {
            return redirect()->route('user.profile.create.step1')
                ->with('error', 'Sila lengkapkan profil anda terlebih dahulu.');
        }

        if ($user->account_type === 'tanggungan') {
            return redirect()->route('dependent.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | DATA TANGGUNGAN
        |--------------------------------------------------------------------------
        | dependentCount = jumlah semua tanggungan untuk dipaparkan pada kad dashboard.
        | dependents = senarai ringkas yang masih aktif dan layak sahaja.
        */

        $dependentCount = $user->dependents()->count();

        $dependents = $user->dependents()
            ->whereIn('status_kehidupan', ['aktif', 'hidup', 'active'])
            ->whereIn('status_tanggungan', [
                'layak',
                'aktif',
                'active',
                'layak_aktif',
                'layak / aktif',
                'Layak / Aktif',
            ])
            ->latest()
            ->take(4)
            ->get();
        /*
        |--------------------------------------------------------------------------
        | DATA BAYARAN TERKINI
        |--------------------------------------------------------------------------
        */

        $latestPayment = $user->payments()
            ->orderByDesc('paid_at')
            ->orderByDesc('created_at')
            ->first();

        $paymentStatus = $this->formatPaymentStatus($latestPayment?->status);
        $paymentPeriod = $this->formatPaymentPeriod($latestPayment);
        $paymentAmount = $latestPayment?->amount;
        $lastPaymentDate = $latestPayment?->paid_at?->translatedFormat('d F Y');

        /*
        |--------------------------------------------------------------------------
        | DATA LAPORAN KEMATIAN
        |--------------------------------------------------------------------------
        */

        $latestDeathReport = DeathReport::where('user_id', $user->id)
            ->latest()
            ->first();

        $deathReportStatus = $this->formatDeathReportStatus($latestDeathReport?->status);

        /*
        |--------------------------------------------------------------------------
        | DATA PERMOHONAN KHAIRAT
        |--------------------------------------------------------------------------
        */

        $khairatStatus = 'Belum Disambungkan';

        /*
        |--------------------------------------------------------------------------
        | MAKLUMAN DAN AKTIVITI
        |--------------------------------------------------------------------------
        */

        $announcements = $this->buildAnnouncements($latestPayment, $latestDeathReport);

        $activities = $this->buildActivities(
            $latestPayment,
            $latestDeathReport,
            $dependents
        );

        return view('user.dashboard', compact(
            'dependents',
            'dependentCount',
            'latestPayment',
            'paymentStatus',
            'paymentPeriod',
            'paymentAmount',
            'lastPaymentDate',
            'latestDeathReport',
            'deathReportStatus',
            'khairatStatus',
            'announcements',
            'activities'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD AHLI TANGGUNGAN
    |--------------------------------------------------------------------------
    */

    public function dependentDashboard()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->account_type === null) {
            return redirect()->route('user.profile.create.step1')
                ->with('error', 'Sila lengkapkan profil anda terlebih dahulu.');
        }

        if ($user->account_type !== 'tanggungan') {
            return redirect()->route('user.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | Cari rekod tanggungan
        |--------------------------------------------------------------------------
        | 1. Cari guna linked_dependent_id
        | 2. Kalau tiada, fallback cari guna no_kp dalam user_profiles
        */

        $dependentProfile = null;

        if ($user->linked_dependent_id) {
            $dependentProfile = Dependent::with('user')
                ->find($user->linked_dependent_id);
        }

        if (!$dependentProfile) {
            $profileNoKp = \Illuminate\Support\Facades\DB::table('user_profiles')
                ->where('user_id', $user->id)
                ->value('no_kp');

            if ($profileNoKp) {
                $dependentProfile = Dependent::with('user')
                    ->where('no_kp', $profileNoKp)
                    ->first();

                if ($dependentProfile) {
                    $user->update([
                        'linked_dependent_id' => $dependentProfile->id,
                    ]);
                }
            }
        }

        $mainMember = $dependentProfile?->user;

        /*
        |--------------------------------------------------------------------------
        | Status paparan
        |--------------------------------------------------------------------------
        */

        $dependentStatus = $dependentProfile ? 'Aktif' : 'Belum Dipautkan';

        $dependentStatusClass = match ($dependentStatus) {
            'Aktif' => 'success',
            'Tidak Aktif' => 'danger',
            default => 'warning',
        };

        $lifeStatus = $this->formatLifeStatus(
            $dependentProfile?->status_kehidupan
        );

        $lifeStatusClass = match ($lifeStatus) {
            'Hidup' => 'success',
            'Meninggal Dunia' => 'secondary',
            default => 'warning',
        };

        $relationship = $dependentProfile?->pertalian ?? '-';
        $ineligibilityReason = null;
        $deathDate = $dependentProfile?->tarikh_kematian?->translatedFormat('d F Y');

        /*
        |--------------------------------------------------------------------------
        | Makluman akaun
        |--------------------------------------------------------------------------
        */

        if (!$dependentProfile) {
            $accountNoticeClass = 'warning';
            $accountNoticeIcon = 'bx-error-circle';
            $accountNoticeTitle = 'Rekod Tanggungan Belum Dipautkan';
            $accountNoticeMessage = 'Akaun ini telah ditandakan sebagai tanggungan, tetapi sistem belum berjaya memautkan akaun ini dengan rekod tanggungan dalam senarai ahli utama.';
        } elseif ($lifeStatus === 'Meninggal Dunia') {
            $accountNoticeClass = 'secondary';
            $accountNoticeIcon = 'bx-info-circle';
            $accountNoticeTitle = 'Status Rekod: Meninggal Dunia';
            $accountNoticeMessage = 'Rekod tanggungan ini telah dikemas kini sebagai meninggal dunia dalam sistem e-Pusara.';
        } else {
            $accountNoticeClass = 'success';
            $accountNoticeIcon = 'bx-check-circle';
            $accountNoticeTitle = 'Akaun Tanggungan Aktif';
            $accountNoticeMessage = 'Anda berdaftar sebagai tanggungan di bawah ahli utama. Akaun ini tidak perlu membuat bayaran yuran khairat secara berasingan.';
        }

        return view('dependent.dashboard', compact(
            'user',
            'dependentProfile',
            'mainMember',
            'dependentStatus',
            'dependentStatusClass',
            'lifeStatus',
            'lifeStatusClass',
            'relationship',
            'ineligibilityReason',
            'deathDate',
            'accountNoticeClass',
            'accountNoticeIcon',
            'accountNoticeTitle',
            'accountNoticeMessage'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT STATUS BAYARAN
    |--------------------------------------------------------------------------
    */

    private function formatPaymentStatus(?string $status): string
    {
        if (!$status) {
            return 'Belum Ada Rekod';
        }

        return match (strtolower($status)) {
            'paid', 'success', 'successful', 'completed', 'complete', 'settled' => 'Telah Dibayar',
            'pending', 'processing', 'unpaid' => 'Dalam Proses',
            'failed', 'failure' => 'Gagal',
            'cancelled', 'canceled' => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT TEMPOH BAYARAN
    |--------------------------------------------------------------------------
    */

    private function formatPaymentPeriod($payment): string
    {
        if (!$payment) {
            return '-';
        }

        if ($payment->payment_period) {
            try {
                return Carbon::createFromFormat('Y-m', $payment->payment_period)
                    ->translatedFormat('F Y');
            } catch (\Throwable $e) {
                return (string) $payment->payment_period;
            }
        }

        if ($payment->membership_year && $payment->paid_month) {
            try {
                return Carbon::create(
                    (int) $payment->membership_year,
                    (int) $payment->paid_month,
                    1
                )->translatedFormat('F Y');
            } catch (\Throwable $e) {
                // Gunakan tarikh bayaran sebagai fallback.
            }
        }

        return $payment->paid_at?->translatedFormat('F Y') ?? '-';
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT STATUS LAPORAN KEMATIAN
    |--------------------------------------------------------------------------
    */

    private function formatDeathReportStatus(?string $status): string
    {
        if (!$status) {
            return 'Tiada Laporan';
        }

        return match (strtolower($status)) {
            'pending', 'menunggu', 'submitted', 'menunggu_semakan' => 'Dalam Semakan',
            'verified', 'approved', 'disahkan', 'diluluskan' => 'Disahkan',
            'rejected', 'ditolak' => 'Ditolak',
            'perlukan_dokumen_tambahan' => 'Perlu Tindakan',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT STATUS TANGGUNGAN
    |--------------------------------------------------------------------------
    */

    private function formatDependentStatus(?string $status): string
    {
        if (!$status) {
            return 'Belum Ditentukan';
        }

        $normalized = strtolower(str_replace([' ', '-'], '_', trim($status)));

        return match ($normalized) {
            'aktif', 'active', 'layak' => 'Aktif',
            'tidak_aktif', 'inactive', 'tidak_layak' => 'Tidak Aktif',
            default => ucfirst(str_replace('_', ' ', $normalized)),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT STATUS KEHIDUPAN
    |--------------------------------------------------------------------------
    */

    private function formatLifeStatus(?string $status): string
    {
        if (!$status) {
            return 'Belum Ditentukan';
        }

        $normalized = strtolower(str_replace([' ', '-'], '_', trim($status)));

        return match ($normalized) {
            'aktif', 'active', 'hidup', 'alive' => 'Hidup',
            'meninggal', 'meninggal_dunia', 'deceased' => 'Meninggal Dunia',
            default => ucfirst(str_replace('_', ' ', $normalized)),
        };
    }
    /*
    |--------------------------------------------------------------------------
    | MAKLUMAN DASHBOARD AHLI UTAMA
    |--------------------------------------------------------------------------
    */

    private function buildAnnouncements($payment, $deathReport): Collection
    {
        $items = collect();

        if (!$payment) {
            $items->push([
                'title' => 'Rekod bayaran',
                'message' => 'Belum ada rekod bayaran yang dipaparkan dalam akaun anda.',
            ]);
        }

        if (
            $deathReport &&
            in_array(strtolower((string) $deathReport->status), [
                'pending',
                'menunggu',
                'submitted',
                'menunggu_semakan',
            ])
        ) {
            $items->push([
                'title' => 'Laporan kematian sedang disemak',
                'message' => 'Laporan yang dihantar sedang menunggu semakan pentadbir.',
            ]);
        }

        return $items;
    }

    /*
    |--------------------------------------------------------------------------
    | AKTIVITI DASHBOARD AHLI UTAMA
    |--------------------------------------------------------------------------
    */

    private function buildActivities($payment, $deathReport, Collection $dependents): Collection
    {
        $items = collect();

        if ($payment) {
            $items->push([
                'title' => 'Rekod bayaran: ' . $this->formatPaymentStatus($payment->status),
                'date' => ($payment->paid_at ?? $payment->created_at)?->translatedFormat('d F Y'),
                'sort_date' => $payment->paid_at ?? $payment->created_at,
            ]);
        }

        if ($deathReport) {
            $items->push([
                'title' => 'Laporan kematian: ' . $this->formatDeathReportStatus($deathReport->status),
                'date' => $deathReport->created_at?->translatedFormat('d F Y'),
                'sort_date' => $deathReport->created_at,
            ]);
        }

        foreach ($dependents as $dependent) {
            $items->push([
                'title' => 'Tanggungan direkodkan: ' . $dependent->name,
                'date' => $dependent->created_at?->translatedFormat('d F Y'),
                'sort_date' => $dependent->created_at,
            ]);
        }

        return $items
            ->sortByDesc('sort_date')
            ->values()
            ->take(3);
    }
}