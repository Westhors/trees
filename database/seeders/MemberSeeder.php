<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

        public function run()
        {
             Member::insert([
            ['id' => 1, 'name' => 'سالم'],
            ['id' => 2, 'name' => 'مهل'],
            ['id' => 3, 'name' => 'قياض'],
            ['id' => 4, 'name' => 'حسن'],
            ['id' => 5, 'name' => 'سعد'],
        ]);
           
        }

}
