<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_user', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('category_id');
            $table->float('amount');
            $table->boolean('is_owner');
            $table->char('type', 1)->default('E');
            $table->char('currency', 3)->default('USD');
            $table->float('currency_exchange')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->primary(['user_id', 'transaction_id']);
            $table->foreign('user_id', 'fk_transaction_user_users')
                ->on('users')->references('id');
            $table->foreign('transaction_id', 'fk_transaction_user_transactions')
                ->on('transactions')->references('id');
            $table->foreign('category_id', 'fk_transaction_user_categories')
                ->on('categories')->references('id');
            $table->index('user_id', 'ix_fk_transaction_user_users');
            $table->index('transaction_id', 'ix_fk_transaction_user_transactions');
            $table->index('category_id', 'ix_fk_transaction_user_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_user');
    }
}
