<?php
// Buscar estatísticas (com tratamento de erros)
$totalCarros = 0;
$carrosDisponiveis = 0;
$valorTotal = 0;

try {
    $result = fetchOne("SELECT COUNT(*) as total FROM carros");
    $totalCarros = $result['total'] ?? 0;
} catch(Exception $e) {
    $totalCarros = 0;
}

try {
    $result = fetchOne("SELECT COUNT(*) as total FROM carros WHERE disponivel = 1");
    $carrosDisponiveis = $result['total'] ?? 0;
} catch(Exception $e) {
    // Se coluna não existe, considerar todos como disponíveis
    $carrosDisponiveis = $totalCarros;
}

try {
    $result = fetchOne("SELECT SUM(preco) as total FROM carros WHERE disponivel = 1");
    $valorTotal = $result['total'] ?? 0;
} catch(Exception $e) {
    try {
        $result = fetchOne("SELECT SUM(preco) as total FROM carros");
        $valorTotal = $result['total'] ?? 0;
    } catch(Exception $e2) {
        $valorTotal = 0;
    }
}

$carrosVendidos = $totalCarros - $carrosDisponiveis;
$totalMarcas = fetchOne("SELECT COUNT(*) as total FROM marcas")['total'] ?? 0;
$totalClientes = fetchOne("SELECT COUNT(*) as total FROM clientes")['total'] ?? 0;
$totalVendas = fetchOne("SELECT COUNT(*) as total FROM vendas")['total'] ?? 0;
$valorVendas = fetchOne("SELECT SUM(valor) as total FROM vendas")['total'] ?? 0;

// Vendas do mês atual
try {
    $result = fetchOne("SELECT COUNT(*) as total, SUM(valor) as valor_total FROM vendas WHERE MONTH(data_venda) = MONTH(CURRENT_DATE()) AND YEAR(data_venda) = YEAR(CURRENT_DATE())");
    $vendasMes = $result ?? ['total' => 0, 'valor_total' => 0];
} catch(Exception $e) {
    $vendasMes = ['total' => 0, 'valor_total' => 0];
}

// Movimentações pendentes
try {
    $movimentacoesPendentes = fetchOne("SELECT COUNT(*) as total FROM movimentacoes WHERE status = 'pendente'")['total'] ?? 0;
} catch(Exception $e) {
    $movimentacoesPendentes = 0;
}

// Buscar carros recentes
try {
    $carrosRecentes = fetchAll("SELECT c.*, m.nome as marca_nome, cat.nome as categoria_nome FROM carros c LEFT JOIN marcas m ON c.marca_id = m.id LEFT JOIN categorias cat ON c.categoria_id = cat.id ORDER BY c.id DESC LIMIT 6");
} catch(Exception $e) {
    $carrosRecentes = [];
}

// Vendas recentes
try {
    $vendasRecentes = fetchAll("SELECT v.*, c.modelo, c.ano, m.nome as marca_nome, cl.nome as cliente_nome FROM vendas v LEFT JOIN carros c ON v.carro_id = c.id LEFT JOIN marcas m ON c.marca_id = m.id LEFT JOIN clientes cl ON v.cliente_id = cl.id ORDER BY v.data_venda DESC LIMIT 5");
    if (!$vendasRecentes) $vendasRecentes = [];
} catch(Exception $e) {
    $vendasRecentes = [];
}

// Top marcas vendidas
try {
    $topMarcas = fetchAll("SELECT m.nome as marca, COUNT(v.id) as total_vendas, SUM(v.valor) as valor_total FROM vendas v LEFT JOIN carros c ON v.carro_id = c.id LEFT JOIN marcas m ON c.marca_id = m.id GROUP BY m.id, m.nome ORDER BY total_vendas DESC LIMIT 5");
    if (!$topMarcas) $topMarcas = [];
} catch(Exception $e) {
    $topMarcas = [];
}
?>

<!-- Hero Section -->
<div class="hero-section text-white py-5 mb-5 rounded-3 shadow-lg position-relative overflow-hidden">
    <div class="position-absolute top-0 end-0" style="opacity: 0.1;">
        <i class="fas fa-car-side" style="font-size: 20rem;"></i>
    </div>
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3 animate-fade-in">
                    <i class="fas fa-car me-3"></i>Loja de Carros
                </h1>
                <?php 
                $userRole = getUserRole();
                if ($userRole === 'cliente'): 
                ?>
                <p class="lead mb-4 fs-5">Bem-vindo! Explore nossa seleção de veículos e faça sua solicitação de interesse.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="?page=carros" class="btn btn-light btn-lg px-4 shadow">
                        <i class="fas fa-car me-2"></i>Ver Carros Disponíveis
                    </a>
                    <a href="?page=solicitacoes" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-comments me-2"></i>Minhas Solicitações
                    </a>
                </div>
                <?php else: ?>
                <p class="lead mb-4 fs-5">Sistema completo para gerenciamento de veículos, clientes e vendas. Controle total do seu negócio automotivo.</p>
                <div class="d-flex flex-wrap gap-3">
                    <?php if (canPerformAction('create', 'carros')): ?>
                    <a href="?page=carros&action=create" class="btn btn-light btn-lg px-4 shadow">
                        <i class="fas fa-plus me-2"></i>Adicionar Carro
                    </a>
                    <?php endif; ?>
                    <a href="?page=carros" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-list me-2"></i>Ver Estoque
                    </a>
                    <?php if (canAccessPage('vendas')): ?>
                    <a href="?page=vendas&action=create" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-shopping-cart me-2"></i>Nova Venda
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-4 text-center">
                <div class="bg-white bg-opacity-10 rounded-circle p-5 d-inline-block">
                    <i class="fas fa-chart-line" style="font-size: 5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas Principais -->
<div class="row mb-4">
    <div class="col-12 mb-3">
        <h3 class="fw-bold">
            <i class="fas fa-chart-bar text-primary me-2"></i>Dashboard - Visão Geral
        </h3>
    </div>
</div>

<div class="row mb-5">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card border-0 shadow-lg h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body text-center p-4">
                <div class="stats-icon bg-white bg-opacity-20 text-white rounded-circle mx-auto mb-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-car fa-2x"></i>
                </div>
                <h2 class="fw-bold mb-2"><?= $totalCarros ?></h2>
                <p class="mb-0 opacity-75">Carros Cadastrados</p>
                <small class="opacity-50"><?= $carrosDisponiveis ?> disponíveis</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card border-0 shadow-lg h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;">
            <div class="card-body text-center p-4">
                <div class="stats-icon bg-white bg-opacity-20 text-white rounded-circle mx-auto mb-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-shopping-cart fa-2x"></i>
                </div>
                <h2 class="fw-bold mb-2"><?= $totalVendas ?></h2>
                <p class="mb-0 opacity-75">Vendas Realizadas</p>
                <small class="opacity-50">R$ <?= number_format($valorVendas, 2, ',', '.') ?></small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card border-0 shadow-lg h-100" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: white;">
            <div class="card-body text-center p-4">
                <div class="stats-icon bg-white bg-opacity-20 text-white rounded-circle mx-auto mb-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h2 class="fw-bold mb-2"><?= $totalClientes ?></h2>
                <p class="mb-0 opacity-75">Clientes Cadastrados</p>
                <small class="opacity-50"><?= $totalMarcas ?> marcas</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card border-0 shadow-lg h-100" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%); color: white;">
            <div class="card-body text-center p-4">
                <div class="stats-icon bg-white bg-opacity-20 text-white rounded-circle mx-auto mb-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-dollar-sign fa-2x"></i>
                </div>
                <h2 class="fw-bold mb-2">R$ <?= number_format($valorTotal / 1000, 0, ',', '.') ?>k</h2>
                <p class="mb-0 opacity-75">Valor em Estoque</p>
                <small class="opacity-50"><?= $movimentacoesPendentes ?> mov. pendentes</small>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas Secundárias -->
<div class="row mb-5">
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Carros Disponíveis</h6>
                        <h3 class="fw-bold text-success mb-0"><?= $carrosDisponiveis ?></h3>
                        <small class="text-muted">de <?= $totalCarros ?> total</small>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Vendas Este Mês</h6>
                        <h3 class="fw-bold text-primary mb-0"><?= $vendasMes['total'] ?? 0 ?></h3>
                        <small class="text-muted">R$ <?= number_format($vendasMes['valor_total'] ?? 0, 2, ',', '.') ?></small>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Taxa de Vendas</h6>
                        <h3 class="fw-bold text-info mb-0"><?= $totalCarros > 0 ? number_format(($carrosVendidos / $totalCarros) * 100, 1) : 0 ?>%</h3>
                        <small class="text-muted"><?= $carrosVendidos ?> vendidos</small>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-percentage fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row mb-5">
    <div class="col-12 mb-4">
        <h3 class="fw-bold">
            <i class="fas fa-bolt text-warning me-2"></i>Ações Rápidas
        </h3>
    </div>
    <?php if ($userRole === 'cliente'): ?>
    <!-- Ações para Cliente -->
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card action-card border-0 shadow-sm h-100 hover-lift">
            <div class="card-body text-center p-4">
                <div class="action-icon bg-primary text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fas fa-car fa-lg"></i>
                </div>
                <h5 class="fw-bold mb-2">Ver Carros</h5>
                <p class="text-muted small mb-3">Explore nossa seleção de veículos</p>
                <a href="?page=carros" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-arrow-right me-1"></i>Ver Estoque
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card action-card border-0 shadow-sm h-100 hover-lift">
            <div class="card-body text-center p-4">
                <div class="action-icon bg-success text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fas fa-comments fa-lg"></i>
                </div>
                <h5 class="fw-bold mb-2">Minhas Solicitações</h5>
                <p class="text-muted small mb-3">Acompanhe suas solicitações</p>
                <a href="?page=solicitacoes" class="btn btn-success btn-sm w-100">
                    <i class="fas fa-arrow-right me-1"></i>Ver Solicitações
                </a>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Ações para Vendedor/Admin -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card action-card border-0 shadow-sm h-100 hover-lift">
            <div class="card-body text-center p-4">
                <div class="action-icon bg-primary text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fas fa-plus fa-lg"></i>
                </div>
                <h5 class="fw-bold mb-2">Adicionar Carro</h5>
                <p class="text-muted small mb-3">Cadastre um novo veículo no estoque</p>
                <?php if (canPerformAction('create', 'carros')): ?>
                <a href="?page=carros&action=create" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-arrow-right me-1"></i>Começar
                </a>
                <?php else: ?>
                <button class="btn btn-secondary btn-sm w-100" disabled>Sem permissão</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card action-card border-0 shadow-sm h-100 hover-lift">
            <div class="card-body text-center p-4">
                <div class="action-icon bg-success text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fas fa-user-plus fa-lg"></i>
                </div>
                <h5 class="fw-bold mb-2">Novo Cliente</h5>
                <p class="text-muted small mb-3">Cadastre um novo cliente</p>
                <?php if (canAccessPage('clientes')): ?>
                <a href="?page=clientes&action=create" class="btn btn-success btn-sm w-100">
                    <i class="fas fa-arrow-right me-1"></i>Começar
                </a>
                <?php else: ?>
                <button class="btn btn-secondary btn-sm w-100" disabled>Sem permissão</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card action-card border-0 shadow-sm h-100 hover-lift">
            <div class="card-body text-center p-4">
                <div class="action-icon bg-warning text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                </div>
                <h5 class="fw-bold mb-2">Nova Venda</h5>
                <p class="text-muted small mb-3">Registre uma venda</p>
                <?php if (canAccessPage('vendas')): ?>
                <a href="?page=vendas&action=create" class="btn btn-warning btn-sm w-100">
                    <i class="fas fa-arrow-right me-1"></i>Começar
                </a>
                <?php else: ?>
                <button class="btn btn-secondary btn-sm w-100" disabled>Sem permissão</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card action-card border-0 shadow-sm h-100 hover-lift">
            <div class="card-body text-center p-4">
                <div class="action-icon bg-info text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fas fa-comments fa-lg"></i>
                </div>
                <h5 class="fw-bold mb-2">Solicitações</h5>
                <p class="text-muted small mb-3">Gerencie solicitações de clientes</p>
                <a href="?page=solicitacoes" class="btn btn-info btn-sm w-100">
                    <i class="fas fa-arrow-right me-1"></i>Ver Solicitações
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Carros Recentes e Vendas -->
<div class="row mb-5">
<!-- Carros Recentes -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-car text-primary me-2"></i>Carros Recentes
                </h5>
                <a href="?page=carros" class="btn btn-sm btn-outline-primary">Ver Todos</a>
            </div>
            <div class="card-body">
                <?php if(count($carrosRecentes) > 0): ?>
                <div class="row">
                    <?php foreach($carrosRecentes as $carro): ?>
                    <div class="col-lg-6 col-md-6 mb-3">
                        <div class="card car-card border h-100 hover-lift">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($carro['modelo']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($carro['marca_nome']) ?> • <?= htmlspecialchars($carro['categoria_nome']) ?></small>
                                    </div>
                                    <span class="badge bg-<?= $carro['disponivel'] ? 'success' : 'danger' ?>">
                                        <?= $carro['disponivel'] ? 'Disponível' : 'Vendido' ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <p class="fw-bold text-primary mb-0">R$ <?= number_format($carro['preco'], 2, ',', '.') ?></p>
                                        <small class="text-muted">Ano <?= $carro['ano'] ?></small>
                                    </div>
                                    <a href="?page=carros&action=view&id=<?= $carro['id'] ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>Ver
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-car fa-3x mb-3 opacity-25"></i>
                    <p>Nenhum carro cadastrado ainda</p>
                    <a href="?page=carros&action=create" class="btn btn-primary">Adicionar Primeiro Carro</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Vendas Recentes e Top Marcas -->
    <div class="col-lg-4">
        <!-- Vendas Recentes -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0">
                    <i class="fas fa-shopping-cart text-success me-2"></i>Vendas Recentes
                </h6>
            </div>
            <div class="card-body p-0">
                <?php if(count($vendasRecentes) > 0): ?>
                <div class="list-group list-group-flush">
                    <?php foreach($vendasRecentes as $venda): ?>
                    <div class="list-group-item border-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold"><?= htmlspecialchars($venda['marca_nome']) ?> <?= htmlspecialchars($venda['modelo']) ?></h6>
                                <small class="text-muted"><?= htmlspecialchars($venda['cliente_nome']) ?></small>
                            </div>
                            <div class="text-end">
                                <strong class="text-success">R$ <?= number_format($venda['valor'], 2, ',', '.') ?></strong>
                                <br><small class="text-muted"><?= date('d/m', strtotime($venda['data_venda'])) ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="?page=vendas" class="btn btn-sm btn-outline-success w-100">Ver Todas as Vendas</a>
                </div>
                <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-shopping-cart fa-2x mb-2 opacity-25"></i>
                    <p class="mb-0 small">Nenhuma venda ainda</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Top Marcas -->
        <?php if(count($topMarcas) > 0): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0">
                    <i class="fas fa-trophy text-warning me-2"></i>Top Marcas
                </h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php foreach($topMarcas as $index => $marca): ?>
                    <div class="list-group-item border-0 px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">#<?= $index + 1 ?></span>
                                <strong><?= htmlspecialchars($marca['marca']) ?></strong>
                            </div>
                            <div class="text-end">
                                <small class="text-muted"><?= $marca['total_vendas'] ?> vendas</small>
                </div>
            </div>
                    </div>
                    <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>
    </div>
</div>










