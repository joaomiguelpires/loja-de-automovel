<?php
// Incluir funções de autenticação
if (file_exists('includes/auth.php')) {
    require_once 'includes/auth.php';
} elseif (file_exists('../includes/auth.php')) {
    require_once '../includes/auth.php';
}

$action = $_GET['action'] ?? 'list';

// O processamento de POST foi movido para index.php para evitar problemas com headers
// Este arquivo agora apenas exibe os formulários e listas

// Buscar carros
$search = $_GET['search'] ?? '';
$marca_filter = $_GET['marca_id'] ?? '';
$categoria_filter = $_GET['categoria_id'] ?? '';
$disponivel_filter = $_GET['disponivel'] ?? '';

$sql = "SELECT c.*, m.nome as marca_nome, cat.nome as categoria_nome 
        FROM carros c 
        LEFT JOIN marcas m ON c.marca_id = m.id 
        LEFT JOIN categorias cat ON c.categoria_id = cat.id 
        WHERE 1=1";

$params = [];

if ($search && trim($search) != '') {
    $search_term = "%" . trim($search) . "%";
    $sql .= " AND (c.modelo LIKE ? OR c.cor LIKE ? OR m.nome LIKE ? OR c.descricao LIKE ?)";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

if ($marca_filter) {
    $sql .= " AND c.marca_id = ?";
    $params[] = $marca_filter;
}

if ($categoria_filter) {
    $sql .= " AND c.categoria_id = ?";
    $params[] = $categoria_filter;
}

if ($disponivel_filter !== '') {
    $sql .= " AND c.disponivel = ?";
    $params[] = $disponivel_filter;
}

// Verificar se created_at existe, senão usar id
// Ordenar por id (mais seguro)
$sql .= " ORDER BY c.id DESC";

$carros = fetchAll($sql, $params);

// Buscar marcas e categorias para filtros
$marcas = fetchAll("SELECT * FROM marcas ORDER BY nome");
$categorias = fetchAll("SELECT * FROM categorias ORDER BY nome");

// Buscar carro para edição ou visualização
$carro_edit = null;
$carro_view = null;
if (($action == 'edit' || $action == 'view') && isset($_GET['id'])) {
    $carro_data = fetchOne("SELECT c.*, m.nome as marca_nome, cat.nome as categoria_nome FROM carros c LEFT JOIN marcas m ON c.marca_id = m.id LEFT JOIN categorias cat ON c.categoria_id = cat.id WHERE c.id = ?", [$_GET['id']]);
    if (!$carro_data) {
        $_SESSION['error'] = 'Carro não encontrado!';
        ob_clean();
        header('Location: index.php?page=carros');
        exit;
    }
    if ($action == 'edit') {
        $carro_edit = $carro_data;
    } else {
        $carro_view = $carro_data;
    }
}
?>

<?php if ($action == 'edit' && $carro_edit): ?>
<!-- Formulário de Edição -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">✏️ Editar Carro</h2>
        <p class="text-muted mb-0">Atualize os dados do veículo</p>
    </div>
    <a href="index.php?page=carros" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="index.php?page=carros&action=update&id=<?= $carro_edit['id'] ?>">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Modelo *</label>
                            <input type="text" class="form-control" name="modelo" value="<?= htmlspecialchars($carro_edit['modelo']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cor *</label>
                            <input type="text" class="form-control" name="cor" value="<?= htmlspecialchars($carro_edit['cor']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Marca *</label>
                            <select class="form-select" name="marca_id" required>
                                <option value="">Selecione uma marca</option>
                                <?php foreach($marcas as $marca): ?>
                                    <option value="<?= $marca['id'] ?>" <?= $carro_edit['marca_id'] == $marca['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($marca['nome']) ?> (<?= htmlspecialchars($marca['pais_origem']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoria *</label>
                            <select class="form-select" name="categoria_id" required>
                                <option value="">Selecione uma categoria</option>
                                <?php foreach($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>" <?= $carro_edit['categoria_id'] == $categoria['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($categoria['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ano *</label>
                            <input type="number" class="form-control" name="ano" value="<?= $carro_edit['ano'] ?>" min="1900" max="<?= date('Y') + 1 ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Quilometragem *</label>
                            <input type="number" class="form-control" name="quilometragem" value="<?= $carro_edit['quilometragem'] ?>" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Preço (R$) *</label>
                            <input type="number" step="0.01" class="form-control" name="preco" value="<?= $carro_edit['preco'] ?>" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descrição</label>
                        <textarea class="form-control" name="descricao" rows="3"><?= htmlspecialchars($carro_edit['descricao'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="disponivel" value="1" <?= $carro_edit['disponivel'] ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold">Disponível para venda</label>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Atualizar Carro
                        </button>
                        <a href="index.php?page=carros" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php elseif ($action == 'view' && $carro_view): ?>
<!-- Visualização de Carro -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">👁️ Detalhes do Carro</h2>
        <p class="text-muted mb-0">Informações completas do veículo</p>
    </div>
    <div>
        <a href="index.php?page=carros&action=edit&id=<?= $carro_view['id'] ?>" class="btn btn-warning me-2">
            <i class="fas fa-edit me-2"></i>Editar
        </a>
        <a href="index.php?page=carros" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-car me-2"></i><?= htmlspecialchars($carro_view['modelo']) ?>
                    <span class="badge bg-<?= $carro_view['disponivel'] ? 'success' : 'danger' ?> ms-2">
                        <?= $carro_view['disponivel'] ? 'Disponível' : 'Vendido' ?>
                    </span>
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-2">Marca</h6>
                        <h5 class="fw-bold"><?= htmlspecialchars($carro_view['marca_nome']) ?></h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-2">Categoria</h6>
                        <h5 class="fw-bold"><?= htmlspecialchars($carro_view['categoria_nome']) ?></h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-2">Ano</h6>
                        <h5 class="fw-bold"><?= $carro_view['ano'] ?></h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-2">Cor</h6>
                        <h5 class="fw-bold"><?= htmlspecialchars($carro_view['cor']) ?></h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-2">Quilometragem</h6>
                        <h5 class="fw-bold"><?= number_format($carro_view['quilometragem'], 0, ',', '.') ?> km</h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-2">Preço</h6>
                        <h4 class="fw-bold text-primary">R$ <?= number_format($carro_view['preco'], 2, ',', '.') ?></h4>
                    </div>
                </div>
                
                <?php if($carro_view['descricao']): ?>
                <div class="mb-3">
                    <h6 class="text-muted mb-2">Descrição</h6>
                    <p class="lead"><?= nl2br(htmlspecialchars($carro_view['descricao'])) ?></p>
                </div>
                <?php endif; ?>
                
                <div class="d-flex gap-2 mt-4">
                    <?php if (hasRole('cliente') && $carro_view['disponivel']): ?>
                    <a href="index.php?page=solicitacoes&action=create&carro_id=<?= $carro_view['id'] ?>" class="btn btn-primary">
                        <i class="fas fa-comments me-2"></i>Fazer Solicitação
                    </a>
                    <?php endif; ?>
                    <?php if (canPerformAction('edit', 'carros')): ?>
                    <a href="index.php?page=carros&action=edit&id=<?= $carro_view['id'] ?>" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Editar Carro
                    </a>
                    <?php endif; ?>
                    <?php if (hasRole('admin')): ?>
                    <a href="index.php?page=carros&action=delete&id=<?= $carro_view['id'] ?>" 
                       class="btn btn-danger"
                       onclick="return confirm('Tem certeza que deseja excluir este carro?')">
                        <i class="fas fa-trash me-2"></i>Excluir
                    </a>
                    <?php endif; ?>
                    <a href="index.php?page=carros" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar para Lista
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">Informações Adicionais</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">ID do Veículo</small>
                    <strong>#<?= $carro_view['id'] ?></strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-<?= $carro_view['disponivel'] ? 'success' : 'danger' ?> fs-6">
                        <?= $carro_view['disponivel'] ? 'Disponível para Venda' : 'Vendido' ?>
                    </span>
                </div>
                <?php if(isset($carro_view['created_at'])): ?>
                <div class="mb-3">
                    <small class="text-muted d-block">Cadastrado em</small>
                    <strong><?= date('d/m/Y H:i', strtotime($carro_view['created_at'])) ?></strong>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php elseif ($action == 'create'): ?>
<!-- Formulário de Criação -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">🚗 Adicionar Novo Carro</h2>
        <p class="text-muted mb-0">Preencha os dados do veículo para adicioná-lo ao estoque</p>
    </div>
    <a href="index.php?page=carros" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="index.php?page=carros&action=create">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Modelo *</label>
                            <input type="text" class="form-control" name="modelo" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cor *</label>
                            <input type="text" class="form-control" name="cor" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Marca *</label>
                            <select class="form-select" name="marca_id" required>
                                <option value="">Selecione uma marca</option>
                                <?php foreach($marcas as $marca): ?>
                                    <option value="<?= $marca['id'] ?>"><?= htmlspecialchars($marca['nome']) ?> (<?= htmlspecialchars($marca['pais_origem']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoria *</label>
                            <select class="form-select" name="categoria_id" required>
                                <option value="">Selecione uma categoria</option>
                                <?php foreach($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ano *</label>
                            <input type="number" class="form-control" name="ano" min="1900" max="<?= date('Y') + 1 ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Quilometragem *</label>
                            <input type="number" class="form-control" name="quilometragem" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Preço (R$) *</label>
                            <input type="number" step="0.01" class="form-control" name="preco" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descrição</label>
                        <textarea class="form-control" name="descricao" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="disponivel" value="1" checked>
                            <label class="form-check-label fw-semibold">Disponível para venda</label>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Salvar Carro
                        </button>
                        <a href="index.php?page=carros" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Lista de Carros -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">🚗 Estoque de Carros</h2>
        <p class="text-muted mb-0">Gerencie seu inventário de veículos</p>
    </div>
    <?php if (canPerformAction('create', 'carros')): ?>
    <a href="index.php?page=carros&action=create" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Adicionar Carro
    </a>
    <?php endif; ?>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="page" value="carros">
            <div class="col-md-4">
                <label class="form-label">
                    <i class="fas fa-search me-1"></i>Buscar
                </label>
                <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Modelo, marca, cor, descrição...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Marca</label>
                <select class="form-select" name="marca_id">
                    <option value="">Todas as marcas</option>
                    <?php foreach($marcas as $marca): ?>
                        <option value="<?= $marca['id'] ?>" <?= $marca_filter == $marca['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($marca['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Categoria</label>
                <select class="form-select" name="categoria_id">
                    <option value="">Todas as categorias</option>
                    <?php foreach($categorias as $categoria): ?>
                        <option value="<?= $categoria['id'] ?>" <?= $categoria_filter == $categoria['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($categoria['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="disponivel">
                    <option value="">Todos</option>
                    <option value="1" <?= $disponivel_filter === '1' ? 'selected' : '' ?>>Disponível</option>
                    <option value="0" <?= $disponivel_filter === '0' ? 'selected' : '' ?>>Vendido</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i>Filtrar
                </button>
                <a href="index.php?page=carros" class="btn btn-outline-secondary">
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
                <h4 class="fw-bold"><?= count($carros) ?></h4>
                <small>Total de Carros</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-success text-white">
            <div class="card-body text-center">
                <h4 class="fw-bold"><?= count(array_filter($carros, fn($c) => $c['disponivel'])) ?></h4>
                <small>Disponíveis</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-warning text-white">
            <div class="card-body text-center">
                <h4 class="fw-bold"><?= count(array_filter($carros, fn($c) => !$c['disponivel'])) ?></h4>
                <small>Vendidos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-info text-white">
            <div class="card-body text-center">
                <h4 class="fw-bold">R$ <?= number_format(array_sum(array_column(array_filter($carros, fn($c) => $c['disponivel']), 'preco')), 2, ',', '.') ?></h4>
                <small>Valor Total</small>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Carros -->
<?php if (count($carros) > 0): ?>
    <div class="row">
        <?php foreach($carros as $carro): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card car-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="fw-bold mb-0"><?= htmlspecialchars($carro['modelo']) ?></h5>
                        <span class="badge bg-<?= $carro['disponivel'] ? 'success' : 'danger' ?> fs-6">
                            <?= $carro['disponivel'] ? 'Disponível' : 'Vendido' ?>
                        </span>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Marca</small>
                            <strong><?= htmlspecialchars($carro['marca_nome']) ?></strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Categoria</small>
                            <strong><?= htmlspecialchars($carro['categoria_nome']) ?></strong>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Ano</small>
                            <strong><?= $carro['ano'] ?></strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Cor</small>
                            <strong><?= htmlspecialchars($carro['cor']) ?></strong>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Preço</small>
                        <h4 class="fw-bold text-primary mb-0">R$ <?= number_format($carro['preco'], 2, ',', '.') ?></h4>
                    </div>
                    
                    <?php if($carro['descricao']): ?>
                    <p class="text-muted small mb-3"><?= htmlspecialchars(substr($carro['descricao'], 0, 100)) ?><?= strlen($carro['descricao']) > 100 ? '...' : '' ?></p>
                    <?php endif; ?>
                    
                    <div class="d-flex gap-2">
                        <a href="index.php?page=carros&action=view&id=<?= $carro['id'] ?>" class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="fas fa-eye me-1"></i>Ver
                        </a>
                        <?php if (hasRole('cliente') && $carro['disponivel']): ?>
                        <a href="index.php?page=solicitacoes&action=create&carro_id=<?= $carro['id'] ?>" class="btn btn-primary btn-sm flex-fill">
                            <i class="fas fa-comments me-1"></i>Solicitar
                        </a>
                        <?php endif; ?>
                        <?php if (canPerformAction('edit', 'carros')): ?>
                        <a href="index.php?page=carros&action=edit&id=<?= $carro['id'] ?>" class="btn btn-outline-warning btn-sm flex-fill">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                        <?php endif; ?>
                        <?php if (hasRole('admin')): ?>
                        <a href="index.php?page=carros&action=delete&id=<?= $carro['id'] ?>" class="btn btn-outline-danger btn-sm" 
                           onclick="return confirm('Tem certeza que deseja excluir este carro?')">
                            <i class="fas fa-trash me-1"></i>Excluir
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-car" style="font-size: 4rem; color: #dee2e6;"></i>
        <h4 class="text-muted mt-3">Nenhum carro encontrado</h4>
        <p class="text-muted">Comece adicionando seu primeiro veículo ao estoque.</p>
        <a href="index.php?page=carros&action=create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Adicionar Primeiro Carro
        </a>
    </div>
<?php endif; ?>

<?php endif; ?>








