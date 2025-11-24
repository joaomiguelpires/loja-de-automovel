<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Carro;
use App\Models\Cliente;
use Illuminate\Http\Request;

class VendaController extends Controller
{
    public function index()
    {
        $vendas = Venda::with(['carro', 'cliente'])->get();
        return view('vendas.index', compact('vendas'));
    }

    public function create()
    {
        $carros = Carro::where('disponivel', true)->get();
        $clientes = Cliente::all();
        return view('vendas.create', compact('carros', 'clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'carro_id' => 'required|exists:carros,id',
            'cliente_id' => 'required|exists:clientes,id',
            'data_venda' => 'required|date',
            'valor' => 'required|numeric|min:0',
            'forma_pagamento' => 'required'
        ]);

        // Atualiza o carro para indisponível
        $carro = Carro::find($request->carro_id);
        $carro->disponivel = false;
        $carro->save();

        Venda::create($request->all());

        return redirect()->route('vendas.index')
            ->with('success', 'Venda registrada com sucesso!');
    }

    public function show(Venda $venda)
    {
        return view('vendas.show', compact('venda'));
    }

    public function destroy(Venda $venda)
    {
        // Libera o carro para venda novamente
        $carro = Carro::find($venda->carro_id);
        $carro->disponivel = true;
        $carro->save();

        $venda->delete();
        return redirect()->route('vendas.index')
            ->with('success', 'Venda excluída com sucesso!');
    }
}