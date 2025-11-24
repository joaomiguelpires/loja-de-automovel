<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Models\Marca;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CarroController extends Controller
{
    public function index(Request $request)
    {
        $query = Carro::with(['marca', 'categoria']);

        // Busca por texto
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('modelo', 'like', "%{$search}%")
                  ->orWhere('cor', 'like', "%{$search}%")
                  ->orWhereHas('marca', function($q) use ($search) {
                      $q->where('nome', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por marca
        if ($request->filled('marca_id')) {
            $query->where('marca_id', $request->marca_id);
        }

        // Filtro por categoria
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // Filtro por disponibilidade
        if ($request->filled('disponivel')) {
            $query->where('disponivel', $request->disponivel);
        }

        $carros = $query->get();
        return view('carros.index', compact('carros'));
    }

    public function create()
    {
        $marcas = Marca::all();
        $categorias = Categoria::all();
        return view('carros.create', compact('marcas', 'categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'modelo' => 'required',
            'marca_id' => 'required|exists:marcas,id',
            'categoria_id' => 'required|exists:categorias,id',
            'ano' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'preco' => 'required|numeric|min:0',
            'cor' => 'required',
            'quilometragem' => 'required|integer|min:0',
            'descricao' => 'nullable|string',
            'disponivel' => 'boolean'
        ]);

        Carro::create($request->all());

        return redirect()->route('carros.index')
            ->with('success', 'Carro cadastrado com sucesso!');
    }

    public function show(Carro $carro)
    {
        return view('carros.show', compact('carro'));
    }

    public function edit(Carro $carro)
    {
        $marcas = Marca::all();
        $categorias = Categoria::all();
        return view('carros.edit', compact('carro', 'marcas', 'categorias'));
    }

    public function update(Request $request, Carro $carro)
    {
        $request->validate([
            'modelo' => 'required',
            'marca_id' => 'required|exists:marcas,id',
            'categoria_id' => 'required|exists:categorias,id',
            'ano' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'preco' => 'required|numeric|min:0',
            'cor' => 'required',
            'quilometragem' => 'required|integer|min:0',
            'descricao' => 'nullable|string',
            'disponivel' => 'boolean'
        ]);

        $carro->update($request->all());

        return redirect()->route('carros.index')
            ->with('success', 'Carro atualizado com sucesso!');
    }

    public function destroy(Carro $carro)
    {
        $carro->delete();
        return redirect()->route('carros.index')
            ->with('success', 'Carro excluído com sucesso!');
    }
}
