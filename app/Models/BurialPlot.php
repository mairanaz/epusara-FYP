<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BurialPlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone',
        'row_number',
        'lot_number',
        'plot_code',
        'status',
        'death_report_id',
        'buried_at',
    ];

    protected $casts = [
        'buried_at' => 'date',
    ];

    public function deathReport()
    {
        return $this->belongsTo(DeathReport::class, 'death_report_id');
    }

    public function getZoneLabelAttribute()
    {
        return match ($this->zone) {
            'L' => 'Zon Lelaki',
            'P' => 'Zon Perempuan',
            'K' => 'Zon Kanak-kanak',
            default => 'Tidak Diketahui',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'available' => 'Kosong',
            'occupied' => 'Telah Digunakan',
            default => 'Tidak Diketahui',
        };
    }

    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function isOccupied()
    {
        return $this->status === 'occupied';
    }
}