<?php
$action = $_GET['action'] ?? 'list';

if ($action == 'create' && $_POST) {
    $nome = $_POST['nome'];
    $pais_origem = $_POST['pais_origem'];
    
    try {
        query("INSERT INTO marcas (nome, pais_origem) VALUES (?, ?)", [$nome, $pais_origem]);
        $_SESSION['success'] = 'Marca cadastrada com sucesso!';
        header('Location: ?page=marcas');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro ao cadastrar marca: ' . $e->getMessage();
    }
}

if ($action == 'delete' && isset($_GET['id'])) {
    try {
        query("DELETE FROM marcas WHERE id = ?", [$_GET['id']]);
        $_SESSION['success'] = 'Marca excluída com sucesso!';
        header('Location: ?page=marcas');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro ao excluir marca: ' . $e->getMessage();
    }
}

$marcas = fetchAll("SELECT * FROM marcas ORDER BY nome");
?>

<?php if ($action == 'create'): ?>
<!-- Formulário de Criação -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">🏷️ Adicionar Nova Marca</h2>
        <p class="text-muted mb-0">Cadastre uma nova marca de veículo</p>
    </div>
    <a href="?page=marcas" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome da Marca *</label>
                        <input type="text" class="form-control" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">País de Origem *</label>
                        <input type="text" class="form-control" name="pais_origem" required>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar Marca
                        </button>
                        <a href="?page=marcas" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Lista de Marcas -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">🏷️ Marcas</h2>
        <p class="text-muted mb-0">Gerencie as marcas de veículos</p>
    </div>
    <a href="?page=marcas&action=create" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Adicionar Marca
    </a>
</div>

<div class="row">
    <?php foreach($marcas as $marca): ?>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-2"><?= htmlspecialchars($marca['nome']) ?></h5>
                <p class="text-muted mb-3"><?= htmlspecialchars($marca['pais_origem']) ?></p>
                <div class="d-flex gap-2">
                    <a href="?page=marcas&action=edit&id=<?= $marca['id'] ?>" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                    <a href="?page=marcas&action=delete&id=<?= $marca['id'] ?>" class="btn btn-outline-danger btn-sm" 
                       onclick="return confirm('Tem certeza que deseja excluir esta marca?')">
                        <i class="fas fa-trash me-1"></i>Excluir
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>



