<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvmediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avmedia', function (Blueprint $table) {
            $table->increments('id'); // Используем increments
            $table->unsignedInteger('page_id'); // Внешний ключ должен быть unsigned
            $table->string('type'); // audio или video
            $table->string('file'); // путь к файлу
            $table->integer('resolution')->nullable(); // разрешение видео (только для видео)
            $table->timestamps();

            // Добавляем внешний ключ
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('avmedia');
    }
}
