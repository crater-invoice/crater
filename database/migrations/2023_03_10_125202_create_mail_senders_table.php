<?php

use Crater\Models\Company;
use Crater\Models\MailSender;
use Crater\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Silber\Bouncer\BouncerFacade;

class CreateMailSendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_senders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('driver');
            $table->boolean('is_default')->default(false);
            $table->string('bcc')->nullable();
            $table->string('cc')->nullable();
            $table->string('from_address')->nullable();
            $table->string('from_name')->nullable();
            $table->json('settings')->nullable();
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->timestamps();
        });

        $users = User::where('role', 'super admin')->get();

        foreach ($users as $user) {
            BouncerFacade::allow($user)->toManage(MailSender::class);
        }

        $companies = Company::all();

        $companies->map(function ($company) {
            if (env('MAIL_DRIVER') == 'smtp') {
                $settings = [
                    'MAIL_HOST' => env('MAIL_HOST'),
                    'MAIL_PORT' => env('MAIL_PORT'),
                    'MAIL_USERNAME' => env('MAIL_USERNAME'),
                    'MAIL_PASSWORD' => env('MAIL_PASSWORD'),
                    'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION')
                ];
                $this->createSender($settings, $company->id);
            }

            if (env('MAIL_DRIVER') == 'mail' || env('MAIL_DRIVER') == 'sendmail') {
                $this->createSender(null, $company->id);
            }

            if (env('MAIL_DRIVER') == 'mailgun') {
                $settings = [
                    'MAILGUN_DOMAIN' => env('MAILGUN_DOMAIN'),
                    'MAILGUN_SECRET' => env('MAILGUN_SECRET'),
                    'MAILGUN_ENDPOINT' => env('MAILGUN_ENDPOINT'),
                ];
                $this->createSender($settings, $company->id);
            }

            if (env('MAIL_DRIVER') == 'ses') {
                $settings = [
                    'MAIL_HOST' => env('MAIL_HOST'),
                    'MAIL_PORT' => env('MAIL_PORT'),
                    'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION'),
                    'MAILGUN_DOMAIN' => env('MAILGUN_DOMAIN'),
                    'SES_KEY' => env('SES_KEY'),
                    'SES_SECRET' => env('SES_SECRET'),
                ];
                $this->createSender($settings, $company->id);
            }
        });
    }

    public function createSender($settings, $company_id)
    {
        $data = [
            'name' => env('MAIL_DRIVER'),
            'driver' => env('MAIL_DRIVER'),
            'is_default' => true,
            'from_address' => env('MAIL_FROM_ADDRESS'),
            'from_name' => env('MAIL_FROM_NAME'),
            'settings' => $settings ?? null,
            'company_id' => $company_id
        ];

        MailSender::create($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_senders');
    }
}
