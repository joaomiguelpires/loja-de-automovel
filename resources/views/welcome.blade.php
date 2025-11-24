@extends('layouts.app')

@section('title', 'Dashboard - Loja de Carros')

@section('content')
<!-- Hero Section -->
<div class="hero-section bg-gradient-primary text-white py-5 mb-5 rounded-3 shadow">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">🚗 Loja de Carros</h1>
                <p class="lead mb-4">Sistema completo para gerenciamento de veículos, clientes e vendas. Controle total do seu negócio automotivo.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('carros.create') }}" class="btn btn-light btn-lg px-4">
                        <i class="fas fa-plus me-2"></i>Adicionar Carro
                    </a>
                    <a href="{{ route('carros.index') }}" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-list me-2"></i>Ver Estoque
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <i class="fas fa-car-side" style="font-size: 8rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-5">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="stats-icon bg-primary text-white rounded-circle mx-auto mb-3">
                    <i class="fas fa-car"></i>
                </div>
                <h3 class="fw-bold text-primary mb-1">{{ \App\Models\Carro::count() }}</h3>
                <p class="text-muted mb-0">Carros Cadastrados</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="stats-icon bg-success text-white rounded-circle mx-auto mb-3">
                    <i class="fas fa-tags"></i>
                </div>
                <h3 class="fw-bold text-success mb-1">{{ \App\Models\Marca::count() }}</h3>
                <p class="text-muted mb-0">Marcas</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="stats-icon bg-warning text-white rounded-circle mx-auto mb-3">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="fw-bold text-warning mb-1">{{ \App\Models\Cliente::count() }}</h3>
                <p class="text-muted mb-0">Clientes</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="stats-icon bg-info text-white rounded-circle mx-auto mb-3">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="fw-bold text-info mb-1">{{ \App\Models\Venda::count() }}</h3>
                <p class="text-muted mb-0">Vendas Realizadas</p>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row mb-5">
    <div class="col-12">
        <h3 class="fw-bold mb-4">⚡ Ações Rápidas</h3>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card action-card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="action-icon bg-primary text-white rounded-circle mx-auto mb-3">
                    <i class="fas fa-plus"></i>
                </div>
                <h5 class="fw-bold mb-2">Adicionar Carro</h5>
                <p class="text-muted small mb-3">Cadastre um novo veículo no estoque</p>
                <a href="{{ route('carros.create') }}" class="btn btn-primary btn-sm">Começar</a>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card action-card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="action-icon bg-success text-white rounded-circle mx-auto mb-3">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h5 class="fw-bold mb-2">Novo Cliente</h5>
                <p class="text-muted small mb-3">Cadastre um novo cliente</p>
                <a href="{{ route('clientes.create') }}" class="btn btn-success btn-sm">Começar</a>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card action-card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="action-icon bg-warning text-white rounded-circle mx-auto mb-3">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h5 class="fw-bold mb-2">Nova Venda</h5>
                <p class="text-muted small mb-3">Registre uma venda</p>
                <a href="{{ route('vendas.create') }}" class="btn btn-warning btn-sm">Começar</a>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card action-card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="action-icon bg-info text-white rounded-circle mx-auto mb-3">
                    <i class="fas fa-cog"></i>
                </div>
                <h5 class="fw-bold mb-2">Configurações</h5>
                <p class="text-muted small mb-3">Gerencie marcas e categorias</p>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('marcas.index') }}" class="btn btn-outline-info">Marcas</a>
                    <a href="{{ route('categorias.index') }}" class="btn btn-outline-info">Categorias</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Carros Recentes -->
@if(\App\Models\Carro::count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0">🚗 Carros Recentes</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach(\App\Models\Carro::with(['marca', 'categoria'])->latest()->take(4)->get() as $carro)
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card car-card border-0 h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0">{{ $carro->modelo }}</h6>
                                    <span class="badge bg-{{ $carro->disponivel ? 'success' : 'danger' }}">
                                        {{ $carro->disponivel ? 'Disponível' : 'Vendido' }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-2">{{ $carro->marca->nome }} • {{ $carro->categoria->nome }}</p>
                                <p class="fw-bold text-primary mb-2">R$ {{ number_format($carro->preco, 2, ',', '.') }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $carro->ano }}</small>
                                    <a href="{{ route('carros.show', $carro->id) }}" class="btn btn-outline-primary btn-sm">Ver</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('carros.index') }}" class="btn btn-outline-primary">Ver Todos os Carros</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stats-card {
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.action-card {
    transition: all 0.3s ease;
}

.action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.action-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.car-card {
    transition: all 0.3s ease;
}

.car-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endpush
