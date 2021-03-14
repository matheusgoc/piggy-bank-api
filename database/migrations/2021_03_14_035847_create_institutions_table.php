<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstitutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedInteger('user_id');
            $table->string('name', 100);
            $table->string('access_token', 100);
            $table->text('logo');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id', 'fk_institutions_users')
                ->on('users')->references('id');
            $table->index('user_id', 'ix_fk_institutions_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('institutions');
    }
}
