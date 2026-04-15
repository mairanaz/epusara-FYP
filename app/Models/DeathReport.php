<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeathReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'deceased_type',
        'user_id',
        'dependent_id',
        'nama_si_mati',
        'no_kp_si_mati',
        'jantina',
        'alamat_terakhir',
        'tarikh_meninggal',
        'umur',
        'no_permit_kebumi',
        'nama_pelapor',
        'no_kp_pelapor',
        'no_tel_pelapor',
        'pertalian_pelapor',
        'sijil_mati_path',
        'permit_kebumi_path',
        'dokumen_sokongan_path',
        'status',
        'catatan_admin',
        'burial_plot_id',
        'verification_category',
        'verified_by',
        'verified_at',
        'burial_lot_no',
        'burial_date',
        'admin_notes',
    ];

    protected $casts = [
        'tarikh_meninggal' => 'date',
        'burial_date' => 'date',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dependent()
    {
        return $this->belongsTo(Dependent::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function burialPlot()
    {
        return $this->belongsTo(BurialPlot::class, 'burial_plot_id');
    }

}