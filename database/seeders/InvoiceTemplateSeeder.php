<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Crater\Models\InvoiceTemplate;

class InvoiceTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InvoiceTemplate::create([
            'name' => 'Template 1',
            'view' => 'invoice1',
            'path' => '/assets/img/PDF/Template1.png'
        ]);

        InvoiceTemplate::create([
            'name' => ' Template 2',
            'view' => 'invoice2',
            'path' => '/assets/img/PDF/Template2.png'
        ]);

        InvoiceTemplate::create([
            'name' => 'Template 3',
            'view' => 'invoice3',
            'path' => '/assets/img/PDF/Template3.png'
        ]);
    }
}
