@extends('layouts.app')

@section('title', 'Lista de Carros')
@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">🚗 Estoque de Carros</h2>
        <p class="text-muted mb-0">Gerencie seu inventário de veículos</p>
    </div>
    <a href="{{ route('carros.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Adicionar Carro
    </a>
</div>

<!-- Filtros e Busca -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('carros.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Modelo, marca, cor...">
            </div>
            <div class="col-md-3">
                <label for="marca_id" class="form-label">Marca</label>
                <select class="form-select" id="marca_id" name="marca_id">
                    <option value="">Todas as marcas</option>
                    @foreach(\App\Models\Marca::all() as $marca)
                        <option value="{{ $marca->id }}" {{ request('marca_id') == $marca->id ? 'selected' : '' }}>
                            {{ $marca->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="categoria_id" class="form-label">Categoria</label>
                <select class="form-select" id="categoria_id" name="categoria_id">
                    <option value="">Todas as categorias</option>
                    @foreach(\App\Models\Categoria::all() as $categoria)
                        <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="disponivel" class="form-label">Status</label>
                <select class="form-select" id="disponivel" name="disponivel">
                    <option value="">Todos</option>
                    <option value="1" {{ request('disponivel') == '1' ? 'selected' : '' }}>Disponível</option>
                    <option value="0" {{ request('disponivel') == '0' ? 'selected' : '' }}>Vendido</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i>Filtrar
                </button>
                <a href="{{ route('carros.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body text-center">
                <h4 class="fw-bold">{{ $carros->count() }}</h4>
                <small>Total de Carros</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-success text-white">
            <div class="card-body text-center">
                <h4 class="fw-bold">{{ $carros->where('disponivel', true)->count() }}</h4>
                <small>Disponíveis</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-warning text-white">
            <div class="card-body text-center">
                <h4 class="fw-bold">{{ $carros->where('disponivel', false)->count() }}</h4>
                <small>Vendidos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-info text-white">
            <div class="card-body text-center">
                <h4 class="fw-bold">R$ {{ number_format($carros->where('disponivel', true)->sum('preco'), 2, ',', '.') }}</h4>
                <small>Valor Total</small>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Carros -->
@if($carros->count() > 0)
    <div class="row">
        @foreach($carros as $carro)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card car-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="fw-bold mb-0">{{ $carro->modelo }}</h5>
                        <span class="badge bg-{{ $carro->disponivel ? 'success' : 'danger' }} fs-6">
                            {{ $carro->disponivel ? 'Disponível' : 'Vendido' }}
                        </span>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Marca</small>
                            <strong>{{ $carro->marca->nome }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Categoria</small>
                            <strong>{{ $carro->categoria->nome }}</strong>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Ano</small>
                            <strong>{{ $carro->ano }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Cor</small>
                            <strong>{{ $carro->cor }}</strong>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Preço</small>
                        <h4 class="fw-bold text-primary mb-0">R$ {{ number_format($carro->preco, 2, ',', '.') }}</h4>
                    </div>
                    
                    @if($carro->descricao)
                    <p class="text-muted small mb-3">{{ Str::limit($carro->descricao, 100) }}</p>
                    @endif
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('carros.show', $carro->id) }}" class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="fas fa-eye me-1"></i>Ver
                        </a>
                        <a href="{{ route('carros.edit', $carro->id) }}" class="btn btn-outline-warning btn-sm flex-fill">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                        <form action="{{ route('carros.destroy', $carro->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" 
                                    onclick="return confirm('Tem certeza que deseja excluir este carro?')">
                                <i class="fas fa-trash me-1"></i>Excluir
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@else
    <div class="text-center py-5">
        <i class="fas fa-car" style="font-size: 4rem; color: #dee2e6;"></i>
        <h4 class="text-muted mt-3">Nenhum carro encontrado</h4>
        <p class="text-muted">Comece adicionando seu primeiro veículo ao estoque.</p>
        <a href="{{ route('carros.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Adicionar Primeiro Carro
        </a>
    </div>
@endif
@endsection

@push('styles')
<style>
.car-card {
    transition: all 0.3s ease;
}

.car-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endpush