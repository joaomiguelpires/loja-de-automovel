<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('carros', function (Blueprint $table) {
            $table->id();
            $table->string('modelo');
            $table->foreignId('marca_id')->constrained();
            $table->foreignId('categoria_id')->constrained();
            $table->integer('ano');
            $table->decimal('preco', 10, 2);
            $table->string('cor');
            $table->integer('quilometragem');
            $table->text('descricao')->nullable();
            $table->boolean('disponivel')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('carros');
    }
};