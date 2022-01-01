<?php

use Crater\Models\Address;
use Crater\Models\Customer;
use Crater\Models\CustomField;
use Crater\Models\CustomFieldValue;
use Crater\Models\Estimate;
use Crater\Models\Expense;
use Crater\Models\Invoice;
use Crater\Models\Payment;
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

                Address::where('user_id', $user->id)->update([
                    'customer_id' => $newCustomer->id,
                    'user_id' => null
                ]);

                Expense::where('user_id', $user->id)->update([
                    'customer_id' => $newCustomer->id,
                    'user_id' => null
                ]);

                Estimate::where('user_id', $user->id)->update([
                    'customer_id' => $newCustomer->id,
                    'user_id' => null
                ]);

                Invoice::where('user_id', $user->id)->update([
                    'customer_id' => $newCustomer->id,
                    'user_id' => null
                ]);

                Payment::where('user_id', $user->id)->update([
                    'customer_id' => $newCustomer->id,
                    'user_id' => null
                ]);

                CustomFieldValue::where('custom_field_valuable_id', $user->id)
                    ->where('custom_field_valuable_type', 'Crater\Models\User')
                    ->update([
                        'custom_field_valuable_type' => 'Crater\Models\Customer',
                        'custom_field_valuable_id' => $newCustomer->id
                    ]);
            }

            $customFields = CustomField::where('model_type', 'User')->get();

            if ($customFields) {
                foreach ($customFields as $customField) {
                    $customField->model_type = "Customer";
                    $customField->slug = Str::upper('CUSTOM_'.$customField->model_type.'_'.Str::slug($customField->label, '_'));
                    $customField->save();
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
