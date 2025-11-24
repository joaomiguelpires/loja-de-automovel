@extends('layouts.app')

@section('title', 'Editar Carro')
@section('content')
<h1>Editar Carro</h1>

<form action="{{ route('carros.update', $carro->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="modelo" class="form-label">Modelo</label>
        <input type="text" class="form-control" id="modelo" name="modelo" value="{{ $carro->modelo }}" required>
    </div>
    <div class="mb-3">
        <label for="marca_id" class="form-label">Marca</label>
        <select class="form-control" id="marca_id" name="marca_id" required>
            <option value="">Selecione uma marca</option>
            @foreach($marcas as $marca)
                <option value="{{ $marca->id }}" {{ $carro->marca_id == $marca->id ? 'selected' : '' }}>{{ $marca->nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="categoria_id" class="form-label">Categoria</label>
        <select class="form-control" id="categoria_id" name="categoria_id" required>
            <option value="">Selecione uma categoria</option>
            @foreach($categorias as $categoria)
                <option value="{{ $categoria->id }}" {{ $carro->categoria_id == $categoria->id ? 'selected' : '' }}>{{ $categoria->nome }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="ano" class="form-label">Ano</label>
        <input type="number" class="form-control" id="ano" name="ano" value="{{ $carro->ano }}" required>
    </div>
    <div class="mb-3">
        <label for="preco" class="form-label">Preço</label>
        <input type="number" step="0.01" class="form-control" id="preco" name="preco" value="{{ $carro->preco }}" required>
    </div>
    <div class="mb-3">
        <label for="cor" class="form-label">Cor</label>
        <input type="text" class="form-control" id="cor" name="cor" value="{{ $carro->cor }}" required>
    </div>
    <div class="mb-3">
        <label for="quilometragem" class="form-label">Quilometragem</label>
        <input type="number" class="form-control" id="quilometragem" name="quilometragem" value="{{ $carro->quilometragem }}" required>
    </div>
    <div class="mb-3">
        <label for="descricao" class="form-label">Descrição</label>
        <textarea class="form-control" id="descricao" name="descricao" rows="3">{{ $carro->descricao }}</textarea>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="disponivel" name="disponivel" value="1" {{ $carro->disponivel ? 'checked' : '' }}>
        <label class="form-check-label" for="disponivel">Disponível para venda</label>
    </div>
    <button type="submit" class="btn btn-primary">Atualizar</button>
    <a href="{{ route('carros.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection