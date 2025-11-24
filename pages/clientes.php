<?php
$action = $_GET['action'] ?? 'list';

if ($action == 'create' && $_POST) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    
    try {
        query("INSERT INTO clientes (nome, email, telefone, endereco) VALUES (?, ?, ?, ?)", 
              [$nome, $email, $telefone, $endereco]);
        $_SESSION['success'] = 'Cliente cadastrado com sucesso!';
        header('Location: ?page=clientes');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro ao cadastrar cliente: ' . $e->getMessage();
    }
}

if ($action == 'delete' && isset($_GET['id'])) {
    try {
        query("DELETE FROM clientes WHERE id = ?", [$_GET['id']]);
        $_SESSION['success'] = 'Cliente excluído com sucesso!';
        header('Location: ?page=clientes');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro ao excluir cliente: ' . $e->getMessage();
    }
}

$clientes = fetchAll("SELECT * FROM clientes ORDER BY nome");
?>

<?php if ($action == 'create'): ?>
<!-- Formulário de Criação -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">👥 Adicionar Novo Cliente</h2>
        <p class="text-muted mb-0">Cadastre um novo cliente</p>
    </div>
    <a href="?page=clientes" class="btn btn-outline-secondary">
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
                            <label class="form-label fw-semibold">Nome *</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email *</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Telefone *</label>
                            <input type="text" class="form-control" name="telefone" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Endereço *</label>
                            <input type="text" class="form-control" name="endereco" required>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar Cliente
                        </button>
                        <a href="?page=clientes" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Lista de Clientes -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">👥 Clientes</h2>
        <p class="text-muted mb-0">Gerencie os clientes</p>
    </div>
    <a href="?page=clientes&action=create" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Adicionar Cliente
    </a>
</div>

<div class="row">
    <?php foreach($clientes as $cliente): ?>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-2"><?= htmlspecialchars($cliente['nome']) ?></h5>
                <p class="text-muted mb-1"><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($cliente['email']) ?></p>
                <p class="text-muted mb-1"><i class="fas fa-phone me-2"></i><?= htmlspecialchars($cliente['telefone']) ?></p>
                <p class="text-muted mb-3"><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($cliente['endereco']) ?></p>
                <div class="d-flex gap-2">
                    <a href="?page=clientes&action=edit&id=<?= $cliente['id'] ?>" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                    <a href="?page=clientes&action=delete&id=<?= $cliente['id'] ?>" class="btn btn-outline-danger btn-sm" 
                       onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
                        <i class="fas fa-trash me-1"></i>Excluir
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>



