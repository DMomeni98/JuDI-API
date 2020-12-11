<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 20);
            $table->Integer('user_id')->unsigned();
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamp('due')->nullable();
            $table->boolean('with_star')->defualt(0);
            $table->string('label_name', 20);
            $table->timestamps();
            $table->integer('category_id');
            //$table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->boolean('is_done')->defualt(0);
            $table->integer('repetitive_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
}
