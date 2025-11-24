<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carro_id')->constrained();
            $table->foreignId('cliente_id')->constrained();
            $table->date('data_venda');
            $table->decimal('valor', 10, 2);
            $table->string('forma_pagamento');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendas');
    }
};