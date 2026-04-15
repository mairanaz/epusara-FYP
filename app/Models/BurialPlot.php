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

    public function deathReport()
    {
        return $this->belongsTo(DeathReport::class);
    }
}