<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('key')->unique('ix_uq_transactions_key');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('place', 45)->nullable();
            $table->string('description', 244)->nullable();
            $table->string('receipt', 100)->nullable();
            $table->timestamp('ordered_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parent_id', 'fk_parent_transactions')
                ->on('transactions')->references('id');
            $table->index('parent_id', 'ix_fk_parent_transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
