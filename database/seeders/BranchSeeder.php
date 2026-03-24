<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Branch::insert([
            ['id' => 1, 'name' => 'سالم'],
            ['id' => 2, 'name' => 'مهل'],
            ['id' => 3, 'name' => 'قياض'],
            ['id' => 4, 'name' => 'حسن'],
            ['id' => 5, 'name' => 'سعد'],
        ]);
    }
}
