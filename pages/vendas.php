<?php
$action = $_GET['action'] ?? 'list';

if ($action == 'create' && $_POST) {
    $carro_id = $_POST['carro_id'];
    $cliente_id = $_POST['cliente_id'];
    $data_venda = $_POST['data_venda'];
    $valor = $_POST['valor'];
    $forma_pagamento = $_POST['forma_pagamento'];
    
    try {
        // Inserir venda
        query("INSERT INTO vendas (carro_id, cliente_id, data_venda, valor, forma_pagamento) VALUES (?, ?, ?, ?, ?)", 
              [$carro_id, $cliente_id, $data_venda, $valor, $forma_pagamento]);
        
        // Marcar carro como indisponível
        query("UPDATE carros SET disponivel = 0 WHERE id = ?", [$carro_id]);
        
        $_SESSION['success'] = 'Venda registrada com sucesso!';
        header('Location: ?page=vendas');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro ao registrar venda: ' . $e->getMessage();
    }
}

if ($action == 'delete' && isset($_GET['id'])) {
    try {
        // Buscar dados da venda
        $venda = fetchOne("SELECT * FROM vendas WHERE id = ?", [$_GET['id']]);
        
        // Marcar carro como disponível novamente
        query("UPDATE carros SET disponivel = 1 WHERE id = ?", [$venda['carro_id']]);
        
        // Excluir venda
        query("DELETE FROM vendas WHERE id = ?", [$_GET['id']]);
        
        $_SESSION['success'] = 'Venda cancelada com sucesso!';
        header('Location: ?page=vendas');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro ao cancelar venda: ' . $e->getMessage();
    }
}

// Buscar vendas com dados relacionados
$vendas = fetchAll("
    SELECT v.*, c.modelo, c.ano, c.cor, m.nome as marca_nome, cl.nome as cliente_nome 
    FROM vendas v 
    LEFT JOIN carros c ON v.carro_id = c.id 
    LEFT JOIN marcas m ON c.marca_id = m.id 
    LEFT JOIN clientes cl ON v.cliente_id = cl.id 
    ORDER BY v.data_venda DESC
");

// Buscar carros disponíveis e clientes para o formulário
$carrosDisponiveis = fetchAll("SELECT * FROM carros WHERE disponivel = 1 ORDER BY modelo");
$clientes = fetchAll("SELECT * FROM clientes ORDER BY nome");
?>

<?php if ($action == 'create'): ?>
<!-- Formulário de Criação -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">🛒 Registrar Nova Venda</h2>
        <p class="text-muted mb-0">Registre uma venda de veículo</p>
    </div>
    <a href="?page=vendas" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Carro *</label>
                            <select class="form-select" name="carro_id" required>
                                <option value="">Selecione um carro</option>
                                <?php foreach($carrosDisponiveis as $carro): ?>
                                    <option value="<?= $carro['id'] ?>"><?= htmlspecialchars($carro['modelo']) ?> - <?= htmlspecialchars($carro['ano']) ?> - R$ <?= number_format($carro['preco'], 2, ',', '.') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cliente *</label>
                            <select class="form-select" name="cliente_id" required>
                                <option value="">Selecione um cliente</option>
                                <?php foreach($clientes as $cliente): ?>
                                    <option value="<?= $cliente['id'] ?>"><?= htmlspecialchars($cliente['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Data da Venda *</label>
                            <input type="date" class="form-control" name="data_venda" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Valor (R$) *</label>
                            <input type="number" step="0.01" class="form-control" name="valor" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Forma de Pagamento *</label>
                            <select class="form-select" name="forma_pagamento" required>
                                <option value="">Selecione</option>
                                <option value="À vista">À vista</option>
                                <option value="Parcelado">Parcelado</option>
                                <option value="Financiamento">Financiamento</option>
                                <option value="Consórcio">Consórcio</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Registrar Venda
                        </button>
                        <a href="?page=vendas" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Lista de Vendas -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">🛒 Vendas</h2>
        <p class="text-muted mb-0">Gerencie as vendas realizadas</p>
    </div>
    <a href="?page=vendas&action=create" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nova Venda
    </a>
</div>

<div class="row">
    <?php foreach($vendas as $venda): ?>
    <div class="col-lg-6 col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="fw-bold mb-0"><?= htmlspecialchars($venda['marca_nome']) ?> <?= htmlspecialchars($venda['modelo']) ?></h5>
                    <span class="badge bg-success">Vendido</span>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Cliente</small>
                        <strong><?= htmlspecialchars($venda['cliente_nome']) ?></strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Data</small>
                        <strong><?= date('d/m/Y', strtotime($venda['data_venda'])) ?></strong>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Valor</small>
                        <strong class="text-success">R$ <?= number_format($venda['valor'], 2, ',', '.') ?></strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Pagamento</small>
                        <strong><?= htmlspecialchars($venda['forma_pagamento']) ?></strong>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="?page=vendas&action=view&id=<?= $venda['id'] ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>Ver
                    </a>
                    <a href="?page=vendas&action=delete&id=<?= $venda['id'] ?>" class="btn btn-outline-danger btn-sm" 
                       onclick="return confirm('Tem certeza que deseja cancelar esta venda?')">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>



