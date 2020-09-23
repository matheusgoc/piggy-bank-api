<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('firstname', 45);
            $table->string('lastname', 45);
            $table->char('gender', 1)->nullable();
            $table->date('birthday')->nullable();
            $table->char('state', 2)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postalcode', 10)->nullable();
            $table->float('balance')->default(0)->nullable();
            $table->float('target_total_savings')->default(0)->nullable();
            $table->float('target_monthly_savings')->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id', 'fk_profiles_users')->on('users')->references('id');
            $table->index('user_id', 'ix_fk_profiles_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
