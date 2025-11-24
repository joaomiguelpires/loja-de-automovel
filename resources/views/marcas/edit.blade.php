@extends('layouts.app')

@section('title', 'Editar Marca')
@section('content')
<h1>Editar Marca</h1>

<form action="{{ route('marcas.update', $marca->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="nome" class="form-label">Nome da Marca</label>
        <input type="text" class="form-control" id="nome" name="nome" value="{{ $marca->nome }}" required>
    </div>
    <div class="mb-3">
        <label for="pais_origem" class="form-label">País de Origem</label>
        <input type="text" class="form-control" id="pais_origem" name="pais_origem" value="{{ $marca->pais_origem }}" required>
    </div>
    <button type="submit" class="btn btn-primary">Atualizar</button>
    <a href="{{ route('marcas.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection

