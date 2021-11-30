<?php

use Crater\Models\Customer;
use Crater\Models\CustomField;
use Crater\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UpdateCustomerIdInAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = User::where('role', 'customer')
            ->get();

        $users->makeVisible('password', 'remember_token');

        if ($users) {
            foreach ($users as $user) {
                $newCustomer = Customer::create($user->toArray());

                $customFields = CustomField::where('model_type', 'User')->get();

                if ($customFields) {
                    $user->fields->map(function ($customFieldValue) use ($newCustomer) {
                        $customFieldValue->custom_field_valuable_type = "Crater\Models\Customer";
                        $customFieldValue->custom_field_valuable_id = $newCustomer->id;
                        $customFieldValue->save();

                        $customField = $customFieldValue->customField;
                        $customField->model_type = "Customer";
                        $customField->slug = Str::upper('CUSTOM_'.$customField->model_type.'_'.Str::slug($customField->label, '_'));
                        $customField->save();
                    });
                }

                if ($user->addresses()->exists()) {
                    $user->addresses->map(function ($address) use ($newCustomer) {
                        if ($address) {
                            $address->customer_id = $newCustomer->id;
                            $address->user_id = null;
                            $address->save();
                        }
                    });
                }

                if ($user->expenses()->exists()) {
                    $user->expenses->map(function ($expense) use ($newCustomer) {
                        if ($expense) {
                            $expense->customer_id = $newCustomer->id;
                            $expense->user_id = null;
                            $expense->save();
                        }
                    });
                }

                if ($user->estimates()->exists()) {
                    $user->estimates->map(function ($estimate) use ($newCustomer) {
                        if ($estimate) {
                            $estimate->customer_id = $newCustomer->id;
                            $estimate->user_id = null;
                            $estimate->save();
                        }
                    });
                }

                if ($user->invoices()->exists()) {
                    $user->invoices->map(function ($invoice) use ($newCustomer) {
                        if ($invoice) {
                            $invoice->customer_id = $newCustomer->id;
                            $invoice->user_id = null;
                            $invoice->save();
                        }
                    });
                }

                if ($user->payments()->exists()) {
                    $user->payments->map(function ($payment) use ($newCustomer) {
                        if ($payment) {
                            $payment->customer_id = $newCustomer->id;
                            $payment->save();
                        }
                    });
                }
            }
        }

        Schema::table('estimates', function (Blueprint $table) {
            if (config('database.default') !== 'sqlite') {
                $table->dropForeign(['user_id']);
            }
            $table->dropColumn('user_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (config('database.default') !== 'sqlite') {
                $table->dropForeign(['user_id']);
            }
            $table->dropColumn('user_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (config('database.default') !== 'sqlite') {
                $table->dropForeign(['user_id']);
            }
            $table->dropColumn('user_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            if (config('database.default') !== 'sqlite') {
                $table->dropForeign(['user_id']);
            }
            $table->dropColumn('user_id');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('unit');
        });

        $users = User::where('role', 'customer')
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
