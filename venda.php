<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = ['carro_id', 'cliente_id', 'data_venda', 'valor', 'forma_pagamento'];

    public function carro()
    {
        return $this->belongsTo(Carro::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}