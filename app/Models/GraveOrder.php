<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraveOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'death_report_id',
        'burial_plot_id',
        'category',
        'order_type',
        'order_label',
        'amount',
        'declaration',
        'status',
        'admin_note',
        'receipt_no',
        'approved_at',
        'completed_at',
    ];

    protected $casts = [
        'declaration' => 'boolean',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deathReport()
    {
        return $this->belongsTo(DeathReport::class);
    }

    public function burialPlot()
    {
        return $this->belongsTo(BurialPlot::class);
    }

    public static function orderOptions()
    {
        return [
            'dewasa' => [
                'marble_full' => [
                    'label' => 'Set Kepungan Marmar Penuh',
                    'description' => 'Reka bentuk penuh dengan kemasan marmar.',
                    'amount' => 1750.00,
                    'images' => [
                        'assets/images/kepuk/marmar-penuh-1.jpg',
                        'assets/images/kepuk/marmar-penuh-2.jpg',
                        'assets/images/kepuk/marmar-penuh-3.jpg',
                    ],
                ],

                'tiles_nisan' => [
                    'label' => 'Set Kepungan Jubin + Batu Nisan Marmar',
                    'description' => 'Gabungan jubin dan batu nisan marmar.',
                    'amount' => 1150.00,
                    'images' => [
                        'assets/images/kepuk/jubin-nisan-1.jpg',
                        'assets/images/kepuk/jubin-nisan-2.jpg',
                        'assets/images/kepuk/jubin-nisan-3.jpg',
                    ],
                ],

                'terazo_nisan' => [
                    'label' => 'Set Kepungan Terazo + Batu Nisan Marmar',
                    'description' => 'Pilihan ekonomi dengan kemasan terazo.',
                    'amount' => 950.00,
                    'images' => [
                        'assets/images/kepuk/terazo-nisan-1.jpg',
                        'assets/images/kepuk/terazo-nisan-2.jpg',
                        'assets/images/kepuk/terazo-nisan-3.jpg',
                    ],
                ],

                'marble_only' => [
                    'label' => 'Kepungan Marmar Sahaja',
                    'description' => 'Kepungan marmar tanpa set penuh.',
                    'amount' => 1400.00,
                    'images' => [
                        'assets/images/kepuk/marmar-sahaja-1.jpg',
                        'assets/images/kepuk/marmar-sahaja-2.jpg',
                        'assets/images/kepuk/marmar-sahaja-3.jpg',
                    ],
                ],

                'tiles_only' => [
                    'label' => 'Kepungan Jubin Sahaja',
                    'description' => 'Kemasan jubin yang ringkas dan kemas.',
                    'amount' => 800.00,
                    'images' => [
                        'assets/images/kepuk/jubin-sahaja-1.jpg',
                        'assets/images/kepuk/jubin-sahaja-2.jpg',
                        'assets/images/kepuk/jubin-sahaja-3.jpg',
                    ],
                ],

                'terazo_only' => [
                    'label' => 'Kepungan Terazo Sahaja',
                    'description' => 'Pilihan asas dengan kos lebih rendah.',
                    'amount' => 550.00,
                    'images' => [
                        'assets/images/kepuk/terazo-sahaja-1.jpg',
                        'assets/images/kepuk/terazo-sahaja-2.jpg',
                        'assets/images/kepuk/terazo-sahaja-3.jpg',
                    ],
                ],

                'nisan_only' => [
                    'label' => 'Batu Nisan Dewasa',
                    'description' => 'Tempahan batu nisan sahaja.',
                    'amount' => 650.00,
                    'images' => [
                        'assets/images/kepuk/nisan-dewasa-1.jpg',
                        'assets/images/kepuk/nisan-dewasa-2.jpg',
                        'assets/images/kepuk/nisan-dewasa-3.jpg',
                    ],
                ],
            ],

            'kanak-kanak' => [
                'marble_full' => [
                    'label' => 'Set Kepungan Marmar Penuh',
                    'description' => 'Reka bentuk penuh dengan kemasan marmar untuk kanak-kanak.',
                    'amount' => 1100.00,
                    'images' => [
                        'assets/images/kepuk/kanak-marmar-penuh-1.jpg',
                        'assets/images/kepuk/kanak-marmar-penuh-2.jpg',
                        'assets/images/kepuk/kanak-marmar-penuh-3.jpg',
                    ],
                ],

                'tiles_nisan' => [
                    'label' => 'Set Kepungan Jubin + Batu Nisan Marmar',
                    'description' => 'Gabungan jubin dan batu nisan marmar untuk kanak-kanak.',
                    'amount' => 950.00,
                    'images' => [
                        'assets/images/kepuk/kanak-jubin-nisan-1.jpg',
                        'assets/images/kepuk/kanak-jubin-nisan-2.jpg',
                        'assets/images/kepuk/kanak-jubin-nisan-3.jpg',
                    ],
                ],

                'terazo_nisan' => [
                    'label' => 'Set Kepungan Terazo + Batu Nisan Marmar',
                    'description' => 'Pilihan ekonomi dengan kemasan terazo.',
                    'amount' => 500.00,
                    'images' => [
                        'assets/images/kepuk/kanak-terazo-nisan-1.jpg',
                        'assets/images/kepuk/kanak-terazo-nisan-2.jpg',
                        'assets/images/kepuk/kanak-terazo-nisan-3.jpg',
                    ],
                ],

                'marble_only' => [
                    'label' => 'Kepungan Marmar Sahaja',
                    'description' => 'Kepungan marmar tanpa set penuh.',
                    'amount' => 850.00,
                    'images' => [
                        'assets/images/kepuk/kanak-marmar-sahaja-1.jpg',
                        'assets/images/kepuk/kanak-marmar-sahaja-2.jpg',
                        'assets/images/kepuk/kanak-marmar-sahaja-3.jpg',
                    ],
                ],

                'tiles_only' => [
                    'label' => 'Kepungan Jubin Sahaja',
                    'description' => 'Kemasan jubin yang ringkas dan kemas.',
                    'amount' => 750.00,
                    'images' => [
                        'assets/images/kepuk/kanak-jubin-sahaja-1.jpg',
                        'assets/images/kepuk/kanak-jubin-sahaja-2.jpg',
                        'assets/images/kepuk/kanak-jubin-sahaja-3.jpg',
                    ],
                ],

                'terazo_only' => [
                    'label' => 'Kepungan Terazo Sahaja',
                    'description' => 'Pilihan asas dengan kos lebih rendah.',
                    'amount' => 300.00,
                    'images' => [
                        'assets/images/kepuk/kanak-terazo-sahaja-1.jpg',
                        'assets/images/kepuk/kanak-terazo-sahaja-2.jpg',
                        'assets/images/kepuk/kanak-terazo-sahaja-3.jpg',
                    ],
                ],

                'nisan_only' => [
                    'label' => 'Batu Nisan Kanak-kanak',
                    'description' => 'Tempahan batu nisan sahaja.',
                    'amount' => 300.00,
                    'images' => [
                        'assets/images/kepuk/nisan-kanak-1.jpg',
                        'assets/images/kepuk/nisan-kanak-2.jpg',
                        'assets/images/kepuk/nisan-kanak-3.jpg',
                    ],
                ],
            ],
        ];
    }

    public function statusLabel()
    {
        return match ($this->status) {
            'pending' => 'Menunggu Kelulusan',
            'approved' => 'Diluluskan',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function statusBadge()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }
    
}