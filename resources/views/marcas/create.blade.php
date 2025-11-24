@extends('layouts.app')

@section('title', 'Adicionar Marca')
@section('content')
<h1>Adicionar Nova Marca</h1>

<form action="{{ route('marcas.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="nome" class="form-label">Nome da Marca</label>
        <input type="text" class="form-control" id="nome" name="nome" required>
    </div>
    <div class="mb-3">
        <label for="pais_origem" class="form-label">País de Origem</label>
        <input type="text" class="form-control" id="pais_origem" name="pais_origem" required>
    </div>
    <button type="submit" class="btn btn-primary">Salvar</button>
    <a href="{{ route('marcas.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection

