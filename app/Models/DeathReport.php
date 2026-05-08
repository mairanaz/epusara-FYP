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
        'sebab_kematian',
        'lokasi_mandi_jenazah',
        'pengurusan_jenazah_oleh',
        'lokasi_pengkebumian',
        'nama_tanah_perkuburan',
        'alamat_tanah_perkuburan',
        'negeri_tanah_perkuburan',
        'catatan_pengurusan',
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

        // Maklumat plot kubur
        'burial_plot_id',
        'burial_zone',
        'burial_plot_code',
        'tarikh_kebumi',
        'burial_lot_no',
        'burial_date',

        // Semakan admin
        'verification_category',
        'verified_by',
        'verified_at',
        'admin_notes',
        'catatan_admin',
    ];

    protected $casts = [
        'tarikh_meninggal' => 'date',
        'tarikh_kebumi' => 'date',
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

    /**
     * Relationship utama jika death_reports.burial_plot_id digunakan.
     */
    public function burialPlot()
    {
        return $this->belongsTo(BurialPlot::class, 'burial_plot_id');
    }

    /**
     * Relationship alternatif jika burial_plots.death_report_id digunakan.
     */
    public function assignedBurialPlot()
    {
        return $this->hasOne(BurialPlot::class, 'death_report_id');
    }

    public function getFinalBurialPlotAttribute()
    {
        return $this->burialPlot ?: $this->assignedBurialPlot;
    }

    public function getFinalBurialDateAttribute()
    {
        return $this->burial_date ?: $this->tarikh_kebumi;
    }

    public function getFinalBurialLotNoAttribute()
    {
        return $this->burial_lot_no ?: $this->burial_plot_code;
    }

    public function graveOrder()
    {
        return $this->hasOne(\App\Models\GraveOrder::class);
    }

}