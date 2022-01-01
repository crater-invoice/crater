<?php

use Crater\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRelationshipOfCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = User::all();

        if ($users) {
            foreach ($users as $user) {
                $user->companies()->attach($user->company_id);
                $user->company_id = null;
                $user->save();
            }
        }

        Schema::table('users', function (Blueprint $table) {
            if (config('database.default') !== 'sqlite') {
                $table->dropForeign(['company_id']);
            }
            $table->dropColumn('company_id');
        });
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
