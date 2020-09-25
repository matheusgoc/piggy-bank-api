<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('sub_id');
            $table->primary(['parent_id', 'sub_id']);
            $table->foreign('parent_id', 'fk_categories_groups_parent')
                ->on('categories')->references('id');
            $table->foreign('sub_id', 'fk_categories_groups_sub')
                ->on('categories')->references('id');
            $table->index('parent_id', 'ix_fk_categories_groups_parent');
            $table->index('sub_id', 'ix_fk_categories_groups_sub');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories_groups');
    }
}
