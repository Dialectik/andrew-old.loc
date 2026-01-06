<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubtitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subtitles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('page_id'); // Связь с таблицей pages
            $table->string('label');       // Язык субтитров (например, English)
            $table->string('srclang');     // Код языка (например, en, fr, etc.)
            $table->string('src');         // Путь к файлу субтитров (.vtt)
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
        Schema::dropIfExists('subtitles');
    }
}
