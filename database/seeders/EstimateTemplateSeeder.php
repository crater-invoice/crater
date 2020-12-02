<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Crater\Models\EstimateTemplate;

class EstimateTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EstimateTemplate::create([
            'name' => 'Template 1',
            'view' => 'estimate1',
            'path' => '/assets/img/PDF/Template1.png'
        ]);

        EstimateTemplate::create([
            'name' => 'Template 2',
            'view' => 'estimate2',
            'path' => '/assets/img/PDF/Template2.png'
        ]);

        EstimateTemplate::create([
            'name' => 'Template 3',
            'view' => 'estimate3',
            'path' => '/assets/img/PDF/Template3.png'
        ]);
    }
}
