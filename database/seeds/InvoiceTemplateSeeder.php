<?php

use Illuminate\Database\Seeder;
use Laraspace\InvoiceTemplate;

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
            'name' => 'Invoice Template1',
            'view' => 'invoice1',
            'path' => '/assets/img/PDF/Template1.png'
        ]);

        InvoiceTemplate::create([
            'name' => 'Invoice Template2',
            'view' => 'invoice2',
            'path' => '/assets/img/PDF/Template2.png'
        ]);

        InvoiceTemplate::create([
            'name' => 'Invoice Template3',
            'view' => 'invoice3',
            'path' => '/assets/img/PDF/Template3.png'
        ]);
    }
}
