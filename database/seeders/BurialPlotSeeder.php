<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BurialPlot;

class BurialPlotSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            'K' => ['rows' => 10, 'lots' => 10],
            'P' => ['rows' => 10, 'lots' => 10],
            'L' => ['rows' => 10, 'lots' => 10],
        ];

        foreach ($zones as $zone => $config) {
            for ($row = 1; $row <= $config['rows']; $row++) {
                for ($lot = 1; $lot <= $config['lots']; $lot++) {
                    $plotCode = $zone . '-B' . $row . '-' . str_pad($lot, 2, '0', STR_PAD_LEFT);

                    BurialPlot::firstOrCreate(
                        ['plot_code' => $plotCode],
                        [
                            'zone' => $zone,
                            'row_number' => $row,
                            'lot_number' => $lot,
                            'status' => 'available',
                        ]
                    );
                }
            }
        }
    }
}