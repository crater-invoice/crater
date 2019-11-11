<?php

use Illuminate\Database\Seeder;
use Laraspace\EstimateTemplate;

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
            'name' => 'Estimate Template1',
            'view' => 'estimate1',
            'path' => '/assets/img/PDF/Template1.png'
        ]);

        EstimateTemplate::create([
            'name' => 'Estimate Template2',
            'view' => 'estimate2',
            'path' => '/assets/img/PDF/Template2.png'
        ]);

        EstimateTemplate::create([
            'name' => 'Estimate Template3',
            'view' => 'estimate3',
            'path' => '/assets/img/PDF/Template3.png'
        ]);
    }
}
