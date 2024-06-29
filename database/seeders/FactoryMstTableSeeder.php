<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FactoryMst;

class FactoryMstTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $factorys = [
            ['level' => 1, 'product_count' => 1,  'need_count' => 100],
            ['level' => 2, 'product_count' => 5,  'need_count' => 500],
            ['level' => 3, 'product_count' => 10, 'need_count' => 1000],
            ['level' => 4, 'product_count' => 15, 'need_count' => 1500],
            ['level' => 5, 'product_count' => 20, 'need_count' => 0],
        ];
        foreach ($factorys as $factory) {
            FactoryMst::create($factory);
        }
    }
}
