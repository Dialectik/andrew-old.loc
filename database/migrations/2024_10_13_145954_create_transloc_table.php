<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transloc', function (Blueprint $table) {
            $table->id();
            $table->string('locale'); // Язык
            $table->string('key');    // Ключ перевода (фраза на базовом языке)
            $table->text('value');    // Переведенный текст
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transloc');
    }
}
