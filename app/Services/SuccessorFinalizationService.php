<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Dependent;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class SuccessorFinalizationService
{
    /*
    |--------------------------------------------------------------------------
    | Flow 1:
    | Calon belum ada akaun semasa admin sahkan.
    | Calon register dan isi profile dahulu.
    | Selepas profile dicipta, sistem detect no_kp dan finalize.
    |--------------------------------------------------------------------------
    */
    public function finalizeIfMatched(User $newMainUser, UserProfile $newProfile): bool
    {
        $cleanIc = preg_replace('/[^0-9]/', '', $newProfile->no_kp ?? '');

        if (!$cleanIc) {
            return false;
        }

        $dependent = Dependent::whereRaw("REPLACE(no_kp, '-', '') = ?", [$cleanIc])
            ->first();

        if (!$dependent) {
            return false;
        }

        $oldProfile = UserProfile::with('user')
            ->where('replacement_dependent_id', $dependent->id)
            ->where('replacement_status', 'pending_registration')
            ->first();

        if (!$oldProfile || !$oldProfile->user) {
            return false;
        }

        $oldMainUser = $oldProfile->user;

        if ($oldMainUser->id === $newMainUser->id) {
            return false;
        }

        DB::transaction(function () use ($newMainUser, $newProfile, $dependent, $oldProfile, $oldMainUser) {
            $this->completeTransfer($newMainUser, $newProfile, $dependent, $oldProfile, $oldMainUser);
        });

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Flow 2:
    | Calon sudah ada akaun tanggungan.
    | Admin sahkan dan sistem terus upgrade akaun itu menjadi Ahli Utama.
    |--------------------------------------------------------------------------
    */
    public function finalizeExistingDependentAccount(User $existingUser, Dependent $dependent, User $oldMainUser): bool
    {
        $oldMainUser->load('profile');

        if (!$oldMainUser->profile) {
            return false;
        }

        $oldProfile = $oldMainUser->profile;

        if ($oldMainUser->id === $existingUser->id) {
            return false;
        }

        $accountType = strtolower((string) $existingUser->account_type);

        if (in_array($accountType, ['utama', 'main_member'])) {
            return false;
        }

        DB::transaction(function () use ($existingUser, $dependent, $oldProfile, $oldMainUser) {

            /*
            |--------------------------------------------------------------------------
            | Kalau akaun tanggungan belum ada profile, cipta profile asas.
            | Ini penting supaya Aufa/Siti muncul dalam Senarai Ahli Admin.
            |--------------------------------------------------------------------------
            */
            $newProfile = UserProfile::where('user_id', $existingUser->id)->first();

            if (!$newProfile) {
                $newProfile = UserProfile::create([
                    'user_id' => $existingUser->id,
                    'nama' => $dependent->name,
                    'no_kp' => $dependent->no_kp,
                    'no_tel_bimbit' => $dependent->no_tel,
                    'agama' => 'Islam',
                    'warganegara' => 'Malaysia',
                    'status_permohonan' => 'approved',
                    'status_kehidupan' => 'aktif',
                    'payment_plan' => $oldProfile->payment_plan,
                    'tarikh_permohonan' => $oldProfile->tarikh_permohonan
                        ?? optional($oldProfile->created_at)->toDateString()
                        ?? now()->toDateString(),
                ]);
            } else {
                $newProfile->update([
                    'status_permohonan' => 'approved',
                    'status_kehidupan' => 'aktif',
                    'payment_plan' => $newProfile->payment_plan ?? $oldProfile->payment_plan,
                ]);
            }

            $this->completeTransfer($existingUser, $newProfile, $dependent, $oldProfile, $oldMainUser);
        });

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Proses sebenar penggantian
    |--------------------------------------------------------------------------
    */
    private function completeTransfer(
        User $newMainUser,
        UserProfile $newProfile,
        Dependent $dependent,
        UserProfile $oldProfile,
        User $oldMainUser
    ): void {
        /*
        |--------------------------------------------------------------------------
        | 1. Jadikan user baru sebagai Ahli Utama
        |--------------------------------------------------------------------------
        */
        $newMainUser->update([
            'account_type' => 'utama',
            'linked_profile_id' => $newProfile->id,
            'linked_dependent_id' => $dependent->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. Profile baru kekal profile calon sendiri
        |--------------------------------------------------------------------------
        */
                $newProfile->update([
            'status_permohonan' => 'approved',
            'status_kehidupan' => 'aktif',

            // Sambung pelan yuran ahli utama lama.
            'payment_plan' => $oldProfile->payment_plan,

            // Penting: ikut tarikh permohonan ahli lama supaya cycle yuran tidak restart.
            'tarikh_permohonan' => $oldProfile->tarikh_permohonan
                ?? optional($oldProfile->created_at)->toDateString()
                ?? now()->toDateString(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3. Update profile Ahli Utama lama
        |--------------------------------------------------------------------------
        */
        $oldProfile->update([
            'status_kehidupan' => 'meninggal_dunia',
            'replaced_by_user_id' => $newMainUser->id,
            'replaced_at' => now(),
            'replacement_dependent_id' => $dependent->id,
            'replacement_status' => 'completed',
            'replacement_reason' => 'Ahli utama meninggal dunia dan penggantian telah disempurnakan kepada Ahli Utama Baharu.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 4. Pindahkan tanggungan keluarga kepada Ahli Utama Baharu
        | Calon sendiri tidak dipindahkan sebab dia sudah jadi Ahli Utama.
        |--------------------------------------------------------------------------
        */
        Dependent::where('user_id', $oldMainUser->id)
            ->where('id', '!=', $dependent->id)
            ->update([
                'user_id' => $newMainUser->id,
            ]);

        /*
        |--------------------------------------------------------------------------
        | 5. Tandakan calon sebagai sudah dinaikkan
        |--------------------------------------------------------------------------
        */
        $dependent->update([
            'promoted_user_id' => $newMainUser->id,
            'promoted_at' => now(),
            'status_tanggungan' => 'tidak_layak',
            'sebab_tidak_layak' => 'Telah menjadi Ahli Utama Baharu',
            'tarikh_keluar_tanggungan' => now()->toDateString(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 6. Pindahkan yuran / bayaran kepada Ahli Utama Baharu
        | Audit trail disimpan supaya sejarah asal masih jelas.
        |--------------------------------------------------------------------------
        */
        $payments = Payment::where('user_id', $oldMainUser->id)->get();

        foreach ($payments as $payment) {
            $payment->update([
                'original_user_id' => $payment->original_user_id ?? $oldMainUser->id,
                'transferred_from_user_id' => $oldMainUser->id,
                'transferred_to_user_id' => $newMainUser->id,
                'transferred_at' => now(),
                'transfer_reason' => 'Rekod yuran dipindahkan kepada Ahli Utama Baharu selepas kematian Ahli Utama lama.',
                'user_id' => $newMainUser->id,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Pindahkan death reports jika model wujud
        |--------------------------------------------------------------------------
        */
        if (class_exists(\App\Models\DeathReport::class)) {
            \App\Models\DeathReport::where('user_id', $oldMainUser->id)
                ->update([
                    'user_id' => $newMainUser->id,
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Pindahkan grave orders jika model wujud
        |--------------------------------------------------------------------------
        */
        if (class_exists(\App\Models\GraveOrder::class)) {
            \App\Models\GraveOrder::where('user_id', $oldMainUser->id)
                ->update([
                    'user_id' => $newMainUser->id,
                ]);
        }
    }
}