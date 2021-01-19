<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormFieldsTable extends Migration
{

	/**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('form_id')->unsigned();

            $table->string('name');
            $table->string('label');
            $table->string('type');
            $table->text('description')->nullable();
            $table->integer('position')->unsigned();
            $table->boolean('is_visible');

            $table->text('rules')->nullable();
            $table->text('default_value')->nullable();
            $table->json('options')->nullable();
            
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
        Schema::dropIfExists('form_fields');
    }
}