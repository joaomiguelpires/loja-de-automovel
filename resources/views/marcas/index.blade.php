@extends('layouts.app')

@section('title', 'Marcas')
@section('content')
<h1>Marcas</h1>
<a href="{{ route('marcas.create') }}" class="btn btn-primary mb-3">Adicionar Marca</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Nome</th>
            <th>País de Origem</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($marcas as $marca)
        <tr>
            <td>{{ $marca->nome }}</td>
            <td>{{ $marca->pais_origem }}</td>
            <td>
                <a href="{{ route('marcas.edit', $marca->id) }}" class="btn btn-warning btn-sm">Editar</a>
                <form action="{{ route('marcas.destroy', $marca->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')">Excluir</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

