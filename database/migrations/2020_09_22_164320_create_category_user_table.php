<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_user', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();
            $table->primary(['category_id', 'user_id']);
            $table->foreign('category_id', 'fk_category_user_categories')
                ->on('categories')
                ->references('id');
            $table->foreign('user_id', 'fk_category_user_users')
                ->on('users')
                ->references('id');
            $table->index('category_id', 'ix_fk_category_user_categories');
            $table->index('user_id', 'ix_fk_category_user_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_user');
    }
}
