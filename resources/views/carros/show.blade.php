@extends('layouts.app')

@section('title', 'Detalhes do Carro')
@section('content')
<h1>Detalhes do Carro</h1>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ $carro->modelo }}</h5>
        <p class="card-text"><strong>Marca:</strong> {{ $carro->marca->nome }}</p>
        <p class="card-text"><strong>Categoria:</strong> {{ $carro->categoria->nome }}</p>
        <p class="card-text"><strong>Ano:</strong> {{ $carro->ano }}</p>
        <p class="card-text"><strong>Preço:</strong> R$ {{ number_format($carro->preco, 2, ',', '.') }}</p>
        <p class="card-text"><strong>Cor:</strong> {{ $carro->cor }}</p>
        <p class="card-text"><strong>Quilometragem:</strong> {{ number_format($carro->quilometragem, 0, ',', '.') }} km</p>
        <p class="card-text"><strong>Disponível:</strong> {{ $carro->disponivel ? 'Sim' : 'Não' }}</p>
        <p class="card-text"><strong>Descrição:</strong> {{ $carro->descricao }}</p>
        
        <a href="{{ route('carros.edit', $carro->id) }}" class="btn btn-warning">Editar</a>
        <a href="{{ route('carros.index') }}" class="btn btn-secondary">Voltar</a>
    </div>
</div>
@endsection