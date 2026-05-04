<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BurialPlot;

class BurialPlotSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            'K' => [
                'prefix' => 'K-B',
                'rows' => [
                    1 => 9,
                    2 => 9,
                    3 => 10,
                    4 => 11,
                    5 => 12,
                    6 => 13,
                    7 => 13,
                    8 => 14,
                    9 => 15,
                    10 => 16,
                ],
            ],

            'P' => [
                'prefix' => 'W-B',
                'rows' => [
                    1 => 13,
                    2 => 14,
                    3 => 15,
                    4 => 16,
                    5 => 17,
                    6 => 17,
                    7 => 18,
                    8 => 20,
                    9 => 20,
                    10 => 21,
                    11 => 22,
                    12 => 23,
                    13 => 24,
                ],
            ],

            'L' => [
                'prefix' => 'L-B',
                'rows' => [
                    1 => 54,
                    2 => 55,
                    3 => 56,
                    4 => 56,
                    5 => 50,
                    6 => 44,
                    7 => 35,
                    8 => 29,
                    9 => 25,
                    10 => 18,
                ],
            ],
        ];

        $validPlotCodes = [];

        foreach ($zones as $zone => $config) {
            foreach ($config['rows'] as $row => $totalLots) {
                for ($lot = 1; $lot <= $totalLots; $lot++) {
                    $plotCode = $config['prefix'] . $row . '-' . str_pad($lot, 2, '0', STR_PAD_LEFT);

                    $validPlotCodes[] = $plotCode;

                    BurialPlot::updateOrCreate(
                        ['plot_code' => $plotCode],
                        [
                            'zone' => $zone,
                            'row_number' => $row,
                            'lot_number' => $lot,
                        ]
                    );
                }
            }
        }

        BurialPlot::whereNotIn('plot_code', $validPlotCodes)
            ->where('status', 'available')
            ->whereNull('death_report_id')
            ->delete();
    }
}