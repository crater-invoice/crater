<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use InvoiceShelf\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        try {
            DB::update("UPDATE abilities SET entity_type = REPLACE(entity_type, 'Crater', 'InvoiceShelf')");
            DB::update("UPDATE assigned_roles SET entity_type = REPLACE(entity_type, 'Crater', 'InvoiceShelf')");
        } catch (\Exception $e) {
        }

        Setting::setSetting('version', '1.1.0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::update("UPDATE abilities SET entity_type = REPLACE(entity_type, 'InvoiceShelf', 'Crater')");
            DB::update("UPDATE assigned_roles SET entity_type = REPLACE(entity_type, 'InvoiceShelf', 'Crater')");
        } catch (\Exception $e) {

        }

        Setting::setSetting('version', '1.0.0');

    }
};
