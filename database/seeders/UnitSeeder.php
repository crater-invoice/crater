<?php

namespace Database\Seeders;

use Crater\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::create(['name' => 'box', 'company_id' => 1]);
        Unit::create(['name' => 'cm', 'company_id' => 1]);
        Unit::create(['name' => 'dz', 'company_id' => 1]);
        Unit::create(['name' => 'ft', 'company_id' => 1]);
        Unit::create(['name' => 'g', 'company_id' => 1]);
        Unit::create(['name' => 'in', 'company_id' => 1]);
        Unit::create(['name' => 'kg', 'company_id' => 1]);
        Unit::create(['name' => 'km', 'company_id' => 1]);
        Unit::create(['name' => 'lb', 'company_id' => 1]);
        Unit::create(['name' => 'mg', 'company_id' => 1]);
        Unit::create(['name' => 'pc', 'company_id' => 1]);
    }
}
