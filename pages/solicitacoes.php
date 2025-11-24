<?php
$action = $_GET['action'] ?? 'list';
$userRole = getUserRole();
$userId = $_SESSION['user_id'] ?? 0;

// Buscar solicitações
if ($userRole === 'cliente') {
    // Cliente vê apenas suas próprias solicitações
    $solicitacoes = fetchAll("
        SELECT s.*, c.modelo, c.ano, c.preco, m.nome as marca_nome, 
               u.nome as atendente_nome
        FROM solicitacoes s
        LEFT JOIN carros c ON s.carro_id = c.id
        LEFT JOIN marcas m ON c.marca_id = m.id
        LEFT JOIN users u ON s.atendido_por = u.id
        WHERE s.cliente_id = ?
        ORDER BY s.data_solicitacao DESC
    ", [$userId]);
} else {
    // Vendedor e admin veem todas as solicitações
    $solicitacoes = fetchAll("
        SELECT s.*, c.modelo, c.ano, c.preco, m.nome as marca_nome,
               cl.nome as cliente_nome, cl.email as cliente_email,
               u.nome as atendente_nome
        FROM solicitacoes s
        LEFT JOIN carros c ON s.carro_id = c.id
        LEFT JOIN marcas m ON c.marca_id = m.id
        LEFT JOIN users cl ON s.cliente_id = cl.id
        LEFT JOIN users u ON s.atendido_por = u.id
        ORDER BY s.data_solicitacao DESC
    ");
}

// Buscar carro para criar solicitação
$carro_solicitacao = null;
if ($action == 'create' && isset($_GET['carro_id'])) {
    $carro_solicitacao = fetchOne("
        SELECT c.*, m.nome as marca_nome, cat.nome as categoria_nome 
        FROM carros c 
        LEFT JOIN marcas m ON c.marca_id = m.id 
        LEFT JOIN categorias cat ON c.categoria_id = cat.id 
        WHERE c.id = ?
    ", [$_GET['carro_id']]);
}

// Buscar solicitação para responder
$solicitacao_responder = null;
if ($action == 'responder' && isset($_GET['id']) && ($userRole === 'vendedor' || $userRole === 'admin')) {
    $solicitacao_responder = fetchOne("
        SELECT s.*, c.modelo, c.ano, c.preco, m.nome as marca_nome,
               cl.nome as cliente_nome, cl.email as cliente_email
        FROM solicitacoes s
        LEFT JOIN carros c ON s.carro_id = c.id
        LEFT JOIN marcas m ON c.marca_id = m.id
        LEFT JOIN users cl ON s.cliente_id = cl.id
        WHERE s.id = ?
    ", [$_GET['id']]);
}
?>

<?php if ($action == 'create' && $carro_solicitacao): ?>
<!-- Formulário de Nova Solicitação -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">📝 Nova Solicitação</h2>
        <p class="text-muted mb-0">Envie sua solicitação sobre o veículo</p>
    </div>
    <a href="index.php?page=carros&action=view&id=<?= $carro_solicitacao['id'] ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <!-- Informações do Carro -->
                <div class="alert alert-info mb-4">
                    <h5 class="fw-bold mb-2">
                        <i class="fas fa-car me-2"></i><?= htmlspecialchars($carro_solicitacao['modelo']) ?>
                    </h5>
                    <p class="mb-1"><strong>Marca:</strong> <?= htmlspecialchars($carro_solicitacao['marca_nome']) ?></p>
                    <p class="mb-1"><strong>Ano:</strong> <?= $carro_solicitacao['ano'] ?></p>
                    <p class="mb-0"><strong>Preço:</strong> R$ <?= number_format($carro_solicitacao['preco'], 2, ',', '.') ?></p>
                </div>

                <form method="POST" action="index.php?page=solicitacoes&action=create">
                    <input type="hidden" name="carro_id" value="<?= $carro_solicitacao['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo de Solicitação *</label>
                        <select class="form-select" name="tipo" required>
                            <option value="interesse">Tenho interesse no veículo</option>
                            <option value="teste_drive">Solicitar teste drive</option>
                            <option value="financiamento">Consultar financiamento</option>
                            <option value="duvida">Tirar dúvidas</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mensagem *</label>
                        <textarea class="form-control" name="mensagem" rows="5" required placeholder="Descreva sua solicitação, dúvida ou interesse..."></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Solicitação
                        </button>
                        <a href="index.php?page=carros&action=view&id=<?= $carro_solicitacao['id'] ?>" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php elseif ($action == 'responder' && $solicitacao_responder): ?>
<!-- Formulário de Resposta -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">💬 Responder Solicitação</h2>
        <p class="text-muted mb-0">Responda à solicitação do cliente</p>
    </div>
    <a href="index.php?page=solicitacoes" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Informações da Solicitação</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Cliente:</strong> <?= htmlspecialchars($solicitacao_responder['cliente_nome']) ?><br>
                        <strong>Email:</strong> <?= htmlspecialchars($solicitacao_responder['cliente_email']) ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Carro:</strong> <?= htmlspecialchars($solicitacao_responder['marca_nome']) ?> <?= htmlspecialchars($solicitacao_responder['modelo']) ?> (<?= $solicitacao_responder['ano'] ?>)<br>
                        <strong>Tipo:</strong> 
                        <?php
                        $tipos = [
                            'interesse' => 'Interesse',
                            'teste_drive' => 'Teste Drive',
                            'financiamento' => 'Financiamento',
                            'duvida' => 'Dúvida',
                            'outro' => 'Outro'
                        ];
                        echo $tipos[$solicitacao_responder['tipo']] ?? $solicitacao_responder['tipo'];
                        ?>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Mensagem do Cliente:</strong>
                    <div class="alert alert-light mt-2">
                        <?= nl2br(htmlspecialchars($solicitacao_responder['mensagem'])) ?>
                    </div>
                </div>
                <?php if ($solicitacao_responder['resposta']): ?>
                <div class="mb-3">
                    <strong>Resposta Anterior:</strong>
                    <div class="alert alert-info mt-2">
                        <?= nl2br(htmlspecialchars($solicitacao_responder['resposta'])) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="index.php?page=solicitacoes&action=responder">
                    <input type="hidden" name="solicitacao_id" value="<?= $solicitacao_responder['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Resposta *</label>
                        <textarea class="form-control" name="resposta" rows="5" required placeholder="Digite sua resposta ao cliente..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" name="status">
                            <option value="em_atendimento" <?= $solicitacao_responder['status'] == 'em_atendimento' ? 'selected' : '' ?>>Em Atendimento</option>
                            <option value="resolvida" <?= $solicitacao_responder['status'] == 'resolvida' ? 'selected' : '' ?>>Resolvida</option>
                            <option value="pendente" <?= $solicitacao_responder['status'] == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Resposta
                        </button>
                        <a href="index.php?page=solicitacoes" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Lista de Solicitações -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">
            <i class="fas fa-comments me-2"></i>Minhas Solicitações
        </h2>
        <p class="text-muted mb-0">
            <?php if ($userRole === 'cliente'): ?>
                Acompanhe suas solicitações sobre os veículos
            <?php else: ?>
                Gerencie todas as solicitações dos clientes
            <?php endif; ?>
        </p>
    </div>
    <?php if ($userRole === 'cliente'): ?>
    <a href="index.php?page=carros" class="btn btn-primary">
        <i class="fas fa-car me-2"></i>Ver Carros
    </a>
    <?php endif; ?>
</div>

<?php if (empty($solicitacoes)): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    <?php if ($userRole === 'cliente'): ?>
        Você ainda não fez nenhuma solicitação. <a href="index.php?page=carros">Veja nossos carros disponíveis</a> e faça sua primeira solicitação!
    <?php else: ?>
        Nenhuma solicitação encontrada.
    <?php endif; ?>
</div>
<?php else: ?>
<div class="row">
    <?php foreach ($solicitacoes as $solicitacao): ?>
    <div class="col-12 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="fas fa-car me-2 text-primary"></i>
                            <?= htmlspecialchars($solicitacao['marca_nome']) ?> <?= htmlspecialchars($solicitacao['modelo']) ?> (<?= $solicitacao['ano'] ?>)
                        </h5>
                        <p class="text-muted mb-0">
                            <strong>Preço:</strong> R$ <?= number_format($solicitacao['preco'], 2, ',', '.') ?>
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-<?php
                            $statusColors = [
                                'pendente' => 'warning',
                                'em_atendimento' => 'info',
                                'resolvida' => 'success',
                                'cancelada' => 'danger'
                            ];
                            echo $statusColors[$solicitacao['status']] ?? 'secondary';
                        ?> fs-6">
                            <?php
                            $statusLabels = [
                                'pendente' => 'Pendente',
                                'em_atendimento' => 'Em Atendimento',
                                'resolvida' => 'Resolvida',
                                'cancelada' => 'Cancelada'
                            ];
                            echo $statusLabels[$solicitacao['status']] ?? $solicitacao['status'];
                            ?>
                        </span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Tipo:</strong> 
                        <?php
                        $tipos = [
                            'interesse' => 'Interesse',
                            'teste_drive' => 'Teste Drive',
                            'financiamento' => 'Financiamento',
                            'duvida' => 'Dúvida',
                            'outro' => 'Outro'
                        ];
                        echo $tipos[$solicitacao['tipo']] ?? $solicitacao['tipo'];
                        ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($solicitacao['data_solicitacao'])) ?>
                    </div>
                    <?php if ($userRole !== 'cliente'): ?>
                    <div class="col-md-6 mt-2">
                        <strong>Cliente:</strong> <?= htmlspecialchars($solicitacao['cliente_nome'] ?? 'N/A') ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <strong>Mensagem:</strong>
                    <div class="alert alert-light mt-2 mb-0">
                        <?= nl2br(htmlspecialchars($solicitacao['mensagem'])) ?>
                    </div>
                </div>

                <?php if ($solicitacao['resposta']): ?>
                <div class="mb-3">
                    <strong>Resposta:</strong>
                    <div class="alert alert-info mt-2 mb-0">
                        <?= nl2br(htmlspecialchars($solicitacao['resposta'])) ?>
                    </div>
                    <?php if ($solicitacao['atendente_nome']): ?>
                    <small class="text-muted">
                        Respondido por: <?= htmlspecialchars($solicitacao['atendente_nome']) ?> 
                        em <?= $solicitacao['data_resposta'] ? date('d/m/Y H:i', strtotime($solicitacao['data_resposta'])) : 'N/A' ?>
                    </small>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="d-flex gap-2">
                    <?php if (($userRole === 'vendedor' || $userRole === 'admin') && !$solicitacao['resposta']): ?>
                    <a href="index.php?page=solicitacoes&action=responder&id=<?= $solicitacao['id'] ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-reply me-1"></i>Responder
                    </a>
                    <?php endif; ?>
                    <a href="index.php?page=carros&action=view&id=<?= $solicitacao['carro_id'] ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-eye me-1"></i>Ver Carro
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php endif; ?>

