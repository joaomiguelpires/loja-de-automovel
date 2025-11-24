<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index()
    {
        $marcas = Marca::all();
        return view('marcas.index', compact('marcas'));
    }

    public function create()
    {
        return view('marcas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|unique:marcas',
            'pais_origem' => 'required'
        ]);

        Marca::create($request->all());

        return redirect()->route('marcas.index')
            ->with('success', 'Marca cadastrada com sucesso!');
    }

    public function edit(Marca $marca)
    {
        return view('marcas.edit', compact('marca'));
    }

    public function update(Request $request, Marca $marca)
    {
        $request->validate([
            'nome' => 'required|unique:marcas,nome,' . $marca->id,
            'pais_origem' => 'required'
        ]);

        $marca->update($request->all());

        return redirect()->route('marcas.index')
            ->with('success', 'Marca atualizada com sucesso!');
    }

    public function destroy(Marca $marca)
    {
        $marca->delete();
        return redirect()->route('marcas.index')
            ->with('success', 'Marca excluída com sucesso!');
    }
}