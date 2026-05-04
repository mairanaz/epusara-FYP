<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\BurialPlotSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BurialPlotSeeder::class,
        ]);
    }
}