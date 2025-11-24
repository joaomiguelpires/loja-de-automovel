@extends('layouts.app')

@section('title', 'Adicionar Carro')
@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">🚗 Adicionar Novo Carro</h2>
        <p class="text-muted mb-0">Preencha os dados do veículo para adicioná-lo ao estoque</p>
    </div>
    <a href="{{ route('carros.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </a>
</div>

<!-- Formulário -->
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('carros.store') }}" method="POST" id="carroForm">
                    @csrf
                    
                    <!-- Informações Básicas -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Informações Básicas
                            </h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="modelo" class="form-label fw-semibold">Modelo *</label>
                            <input type="text" class="form-control @error('modelo') is-invalid @enderror" 
                                   id="modelo" name="modelo" value="{{ old('modelo') }}" 
                                   placeholder="Ex: Civic, Corolla, Gol" required>
                            @error('modelo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cor" class="form-label fw-semibold">Cor *</label>
                            <input type="text" class="form-control @error('cor') is-invalid @enderror" 
                                   id="cor" name="cor" value="{{ old('cor') }}" 
                                   placeholder="Ex: Branco, Prata, Preto" required>
                            @error('cor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Marca e Categoria -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="fas fa-tags me-2"></i>Classificação
                            </h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="marca_id" class="form-label fw-semibold">Marca *</label>
                            <select class="form-select @error('marca_id') is-invalid @enderror" 
                                    id="marca_id" name="marca_id" required>
                                <option value="">Selecione uma marca</option>
                                @foreach($marcas as $marca)
                                    <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
                                        {{ $marca->nome }} ({{ $marca->pais_origem }})
                                    </option>
                                @endforeach
                            </select>
                            @error('marca_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="categoria_id" class="form-label fw-semibold">Categoria *</label>
                            <select class="form-select @error('categoria_id') is-invalid @enderror" 
                                    id="categoria_id" name="categoria_id" required>
                                <option value="">Selecione uma categoria</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Detalhes Técnicos -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="fas fa-cogs me-2"></i>Detalhes Técnicos
                            </h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ano" class="form-label fw-semibold">Ano *</label>
                            <input type="number" class="form-control @error('ano') is-invalid @enderror" 
                                   id="ano" name="ano" value="{{ old('ano') }}" 
                                   min="1900" max="{{ date('Y') + 1 }}" 
                                   placeholder="Ex: 2020" required>
                            @error('ano')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quilometragem" class="form-label fw-semibold">Quilometragem *</label>
                            <input type="number" class="form-control @error('quilometragem') is-invalid @enderror" 
                                   id="quilometragem" name="quilometragem" value="{{ old('quilometragem') }}" 
                                   min="0" placeholder="Ex: 50000" required>
                            @error('quilometragem')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="preco" class="form-label fw-semibold">Preço (R$) *</label>
                            <input type="number" step="0.01" class="form-control @error('preco') is-invalid @enderror" 
                                   id="preco" name="preco" value="{{ old('preco') }}" 
                                   min="0" placeholder="Ex: 45000.00" required>
                            @error('preco')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Descrição e Status -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="fas fa-file-alt me-2"></i>Informações Adicionais
                            </h5>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="descricao" class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                      id="descricao" name="descricao" rows="4" 
                                      placeholder="Descreva características especiais, histórico, etc.">{{ old('descricao') }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="disponivel" 
                                       name="disponivel" value="1" {{ old('disponivel', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="disponivel">
                                    <i class="fas fa-check-circle me-1"></i>Disponível para venda
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Salvar Carro
                        </button>
                        <a href="{{ route('carros.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar com Dicas -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h6 class="fw-bold mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Dicas
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="fw-semibold text-primary">📝 Modelo</h6>
                    <p class="small text-muted mb-0">Use apenas o nome do modelo, sem a marca.</p>
                </div>
                <div class="mb-3">
                    <h6 class="fw-semibold text-primary">💰 Preço</h6>
                    <p class="small text-muted mb-0">Digite o valor em reais, use ponto para decimais.</p>
                </div>
                <div class="mb-3">
                    <h6 class="fw-semibold text-primary">📊 Quilometragem</h6>
                    <p class="small text-muted mb-0">Informe a quilometragem atual do veículo.</p>
                </div>
                <div class="mb-3">
                    <h6 class="fw-semibold text-primary">📋 Descrição</h6>
                    <p class="small text-muted mb-0">Mencione características especiais, histórico, etc.</p>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-success text-white">
                <h6 class="fw-bold mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Estatísticas
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h5 class="fw-bold text-primary">{{ \App\Models\Carro::count() }}</h5>
                        <small class="text-muted">Total</small>
                    </div>
                    <div class="col-6">
                        <h5 class="fw-bold text-success">{{ \App\Models\Carro::where('disponivel', true)->count() }}</h5>
                        <small class="text-muted">Disponíveis</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Formatação do preço
    const precoInput = document.getElementById('preco');
    precoInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            value = (parseInt(value) / 100).toFixed(2);
            this.value = value;
        }
    });
    
    // Validação do ano
    const anoInput = document.getElementById('ano');
    anoInput.addEventListener('input', function() {
        const currentYear = new Date().getFullYear();
        if (this.value > currentYear + 1) {
            this.setCustomValidity('Ano não pode ser maior que ' + (currentYear + 1));
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>
@endpush