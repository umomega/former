<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('form_id')->unsigned()->nullable();

            $table->string('uuid', 64);
            $table->string('ip', 64);
            $table->integer('status')->default(30);
            $table->text('notes')->nullable();
            $table->json('form_data');

            $table->timestamps();

            $table->foreign('form_id')
                ->references('id')
                ->on('forms')
                ->onDelete('cascade');
        });
    }

	/**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers');
    }

}