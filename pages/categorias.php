<?php
$action = $_GET['action'] ?? 'list';

if ($action == 'create' && $_POST) {
    $nome = $_POST['nome'];
    
    try {
        query("INSERT INTO categorias (nome) VALUES (?)", [$nome]);
        $_SESSION['success'] = 'Categoria cadastrada com sucesso!';
        header('Location: ?page=categorias');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro ao cadastrar categoria: ' . $e->getMessage();
    }
}

if ($action == 'delete' && isset($_GET['id'])) {
    try {
        query("DELETE FROM categorias WHERE id = ?", [$_GET['id']]);
        $_SESSION['success'] = 'Categoria excluída com sucesso!';
        header('Location: ?page=categorias');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Erro ao excluir categoria: ' . $e->getMessage();
    }
}

$categorias = fetchAll("SELECT * FROM categorias ORDER BY nome");
?>

<?php if ($action == 'create'): ?>
<!-- Formulário de Criação -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">📋 Adicionar Nova Categoria</h2>
        <p class="text-muted mb-0">Cadastre uma nova categoria de veículo</p>
    </div>
    <a href="?page=categorias" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Voltar
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome da Categoria *</label>
                        <input type="text" class="form-control" name="nome" required>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar Categoria
                        </button>
                        <a href="?page=categorias" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Lista de Categorias -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">📋 Categorias</h2>
        <p class="text-muted mb-0">Gerencie as categorias de veículos</p>
    </div>
    <a href="?page=categorias&action=create" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Adicionar Categoria
    </a>
</div>

<div class="row">
    <?php foreach($categorias as $categoria): ?>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-2"><?= htmlspecialchars($categoria['nome']) ?></h5>
                <div class="d-flex gap-2">
                    <a href="?page=categorias&action=edit&id=<?= $categoria['id'] ?>" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                    <a href="?page=categorias&action=delete&id=<?= $categoria['id'] ?>" class="btn btn-outline-danger btn-sm" 
                       onclick="return confirm('Tem certeza que deseja excluir esta categoria?')">
                        <i class="fas fa-trash me-1"></i>Excluir
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>



