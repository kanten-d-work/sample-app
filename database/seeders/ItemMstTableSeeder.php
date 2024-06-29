<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ItemMst;

class ItemMstTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['item_id' => 1, 'name' => 'item_1'],
            ['item_id' => 2, 'name' => 'item_2'],
            ['item_id' => 3, 'name' => 'item_3'],
            ['item_id' => 4, 'name' => 'item_4'],
            ['item_id' => 5, 'name' => 'item_5'],
        ];
        foreach ($items as $item) {
            ItemMst::create($item);
        }
    }
}
