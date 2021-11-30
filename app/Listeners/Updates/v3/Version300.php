<?php

namespace Crater\Listeners\Updates\v3;

use Crater\Listeners\Updates\Listener;
use Crater\Models\Currency;
use Crater\Models\Item;
use Crater\Models\Payment;
use Crater\Models\PaymentMethod;
use Crater\Models\Setting;
use Crater\Models\Unit;
use Crater\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Vinkla\Hashids\Facades\Hashids;

class Version300 extends Listener
{
    public const VERSION = '3.0.0';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($this->isListenerFired($event)) {
            return;
        }

        $this->changeMigrations();

        $this->addSeederData();

        $this->databaseChanges();

        $this->changeMigrations(true);

        Setting::setSetting('version', static::VERSION);
    }

    public function changeMigrations($removeColumn = false)
    {
        if ($removeColumn) {
            \Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('unit');
            });

            \Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('payment_mode');
            });

            return true;
        }

        \Schema::create('units', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->timestamps();
        });

        \Schema::table('items', function (Blueprint $table) {
            $table->integer('unit_id')->unsigned()->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });

        \Schema::create('payment_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->timestamps();
        });

        \Schema::table('payments', function (Blueprint $table) {
            $table->string('unique_hash')->nullable();
            $table->integer('payment_method_id')->unsigned()->nullable();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade');
        });

        return true;
    }

    public function addSeederData()
    {
        $company_id = User::where('role', 'admin')->first()->company_id;

        Unit::create(['name' => 'box', 'company_id' => $company_id]);
        Unit::create(['name' => 'cm', 'company_id' => $company_id]);
        Unit::create(['name' => 'dz', 'company_id' => $company_id]);
        Unit::create(['name' => 'ft', 'company_id' => $company_id]);
        Unit::create(['name' => 'g', 'company_id' => $company_id]);
        Unit::create(['name' => 'in', 'company_id' => $company_id]);
        Unit::create(['name' => 'kg', 'company_id' => $company_id]);
        Unit::create(['name' => 'km', 'company_id' => $company_id]);
        Unit::create(['name' => 'lb', 'company_id' => $company_id]);
        Unit::create(['name' => 'mg', 'company_id' => $company_id]);
        Unit::create(['name' => 'pc', 'company_id' => $company_id]);

        PaymentMethod::create(['name' => 'Cash', 'company_id' => $company_id]);
        PaymentMethod::create(['name' => 'Check', 'company_id' => $company_id]);
        PaymentMethod::create(['name' => 'Credit Card', 'company_id' => $company_id]);
        PaymentMethod::create(['name' => 'Bank Transfer', 'company_id' => $company_id]);

        Currency::create([
            'name' => 'Serbian Dinar',
            'code' => 'RSD',
            'symbol' => 'RSD',
            'precision' => '2',
            'thousand_separator' => '.',
            'decimal_separator' => ',',
        ]);
    }

    public function databaseChanges()
    {
        $payments = Payment::all();

        if ($payments) {
            foreach ($payments as $payment) {
                $payment->unique_hash = Hashids::connection(Payment::class)->encode($payment->id);
                $payment->save();

                $paymentMethod = PaymentMethod::where('name', $payment->payment_mode)
                    ->first();

                if ($paymentMethod) {
                    $payment->payment_method_id = $paymentMethod->id;
                    $payment->save();
                }
            }
        }

        $items = Item::all();

        if ($items) {
            foreach ($items as $item) {
                $unit = Unit::where('name', $item->unit)
                    ->first();

                if ($unit) {
                    $item->unit_id = $unit->id;
                    $item->save();
                }
            }
        }
    }
}
