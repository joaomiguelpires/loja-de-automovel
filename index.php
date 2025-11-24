<?php
// Iniciar output buffering para permitir redirects após output
ob_start();
session_start();

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'loja_carros';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Se não conseguir conectar, redirecionar para instalação
    if ($e->getCode() == 1049) { // Database doesn't exist
        header('Location: install.php');
        exit;
    }
    die("Erro na conexão: " . $e->getMessage());
}

// Função para executar queries
function query($sql, $params = []) {
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

// Função para buscar dados
function fetchAll($sql, $params = []) {
    return query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
}

// Função para buscar um registro
function fetchOne($sql, $params = []) {
    return query($sql, $params)->fetch(PDO::FETCH_ASSOC);
}

// Incluir funções de autenticação
require_once 'includes/auth.php';

// Verificar e corrigir estrutura do banco automaticamente
function verificarEstruturaBanco() {
    global $pdo;
    try {
        $colunas_existentes = $pdo->query("SHOW COLUMNS FROM carros")->fetchAll(PDO::FETCH_COLUMN);
        
        // Verificar e adicionar preco
        if (!in_array('preco', $colunas_existentes)) {
            $pdo->exec("ALTER TABLE carros ADD COLUMN preco DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER ano");
        }
        
        // Verificar e adicionar disponivel
        if (!in_array('disponivel', $colunas_existentes)) {
            $pdo->exec("ALTER TABLE carros ADD COLUMN disponivel BOOLEAN DEFAULT TRUE AFTER descricao");
        }
        
        // Verificar created_at
        if (!in_array('created_at', $colunas_existentes)) {
            $pdo->exec("ALTER TABLE carros ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        }
        
        // Verificar updated_at
        if (!in_array('updated_at', $colunas_existentes)) {
            $pdo->exec("ALTER TABLE carros ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        }
        
        // Atualizar valores NULL
        try {
            $pdo->exec("UPDATE carros SET disponivel = 1 WHERE disponivel IS NULL");
            $pdo->exec("UPDATE carros SET preco = 0 WHERE preco IS NULL OR preco = 0");
        } catch(PDOException $e) {
            // Ignorar
        }
    } catch(PDOException $e) {
        // Ignorar erros
    }
}

// Executar verificação
verificarEstruturaBanco();

// Verificar autenticação (exceto para login e install)
$currentPage = basename($_SERVER['PHP_SELF']);
$allowedPages = ['login.php', 'install.php'];

if (!isLoggedIn() && !in_array($currentPage, $allowedPages)) {
    header('Location: pages/login.php');
    exit;
}

// Roteamento simples
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'list';

// Verificar permissão de acesso à página (apenas se estiver logado)
if (isLoggedIn()) {
    if (!canAccessPage($page)) {
        redirectToAccessDenied();
    }
    
    // Verificar permissão de ação
    if (!canPerformAction($action, $page)) {
        redirectToAccessDenied();
    }
}

// Processar POST antes de incluir header (para permitir redirects)
if ($page == 'carros') {
    // Processar criação de carro
    if ($action == 'create' && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modelo'])) {
        // Verificar se pode criar
        if (!canPerformAction('create', 'carros')) {
            redirectToAccessDenied();
        }
        
        $modelo = trim($_POST['modelo'] ?? '');
        $marca_id = intval($_POST['marca_id'] ?? 0);
        $categoria_id = intval($_POST['categoria_id'] ?? 0);
        $ano = intval($_POST['ano'] ?? 0);
        $preco = floatval($_POST['preco'] ?? 0);
        $cor = trim($_POST['cor'] ?? '');
        $quilometragem = intval($_POST['quilometragem'] ?? 0);
        $descricao = trim($_POST['descricao'] ?? '');
        $disponivel = isset($_POST['disponivel']) && $_POST['disponivel'] == '1' ? 1 : 0;
        
        if (empty($modelo) || $marca_id <= 0 || $categoria_id <= 0 || $ano <= 0 || $preco <= 0 || empty($cor)) {
            $_SESSION['error'] = 'Preencha todos os campos obrigatórios!';
        } else {
            try {
                query("INSERT INTO carros (modelo, marca_id, categoria_id, ano, preco, cor, quilometragem, descricao, disponivel) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                      [$modelo, $marca_id, $categoria_id, $ano, $preco, $cor, $quilometragem, $descricao, $disponivel]);
                $_SESSION['success'] = 'Carro cadastrado com sucesso!';
                header('Location: index.php?page=carros');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Erro ao cadastrar carro: ' . $e->getMessage();
            }
        }
    }
    
    // Processar atualização de carro
    if ($action == 'update' && $_POST && isset($_GET['id'])) {
        // Verificar se pode editar
        if (!canPerformAction('edit', 'carros')) {
            redirectToAccessDenied();
        }
        
        $id = $_GET['id'];
        $modelo = $_POST['modelo'];
        $marca_id = $_POST['marca_id'];
        $categoria_id = $_POST['categoria_id'];
        $ano = $_POST['ano'];
        $preco = $_POST['preco'];
        $cor = $_POST['cor'];
        $quilometragem = $_POST['quilometragem'];
        $descricao = $_POST['descricao'] ?? '';
        $disponivel = isset($_POST['disponivel']) ? 1 : 0;
        
        try {
            query("UPDATE carros SET modelo = ?, marca_id = ?, categoria_id = ?, ano = ?, preco = ?, cor = ?, quilometragem = ?, descricao = ?, disponivel = ? WHERE id = ?",
                  [$modelo, $marca_id, $categoria_id, $ano, $preco, $cor, $quilometragem, $descricao, $disponivel, $id]);
            $_SESSION['success'] = 'Carro atualizado com sucesso!';
            header('Location: index.php?page=carros');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao atualizar carro: ' . $e->getMessage();
        }
    }
    
    // Processar exclusão de carro
    if ($action == 'delete' && isset($_GET['id'])) {
        // Apenas admin pode excluir
        if (!hasRole('admin')) {
            redirectToAccessDenied();
        }
        
        try {
            query("DELETE FROM carros WHERE id = ?", [$_GET['id']]);
            $_SESSION['success'] = 'Carro excluído com sucesso!';
            header('Location: index.php?page=carros');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erro ao excluir carro: ' . $e->getMessage();
        }
    }
}

// Incluir header
include 'includes/header.php';

// Processar solicitações
if ($page == 'solicitacoes') {
    // Processar criação de solicitação
    if ($action == 'create' && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['carro_id'])) {
        if (!canPerformAction('create', 'solicitacoes')) {
            redirectToAccessDenied();
        }
        
        $carro_id = intval($_POST['carro_id'] ?? 0);
        $tipo = trim($_POST['tipo'] ?? 'interesse');
        $mensagem = trim($_POST['mensagem'] ?? '');
        $cliente_id = $_SESSION['user_id'];
        
        if ($carro_id > 0 && $mensagem) {
            try {
                query("INSERT INTO solicitacoes (cliente_id, carro_id, tipo, mensagem, status) VALUES (?, ?, ?, ?, 'pendente')",
                      [$cliente_id, $carro_id, $tipo, $mensagem]);
                $_SESSION['success'] = 'Solicitação enviada com sucesso!';
                header('Location: index.php?page=solicitacoes');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Erro ao enviar solicitação: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'Preencha todos os campos obrigatórios!';
        }
    }
    
    // Processar resposta de solicitação (vendedor/admin)
    if ($action == 'responder' && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['solicitacao_id'])) {
        if (!canPerformAction('edit', 'solicitacoes')) {
            redirectToAccessDenied();
        }
        
        $solicitacao_id = intval($_POST['solicitacao_id'] ?? 0);
        $resposta = trim($_POST['resposta'] ?? '');
        $status = trim($_POST['status'] ?? 'resolvida');
        $atendido_por = $_SESSION['user_id'];
        
        if ($solicitacao_id > 0 && $resposta) {
            try {
                query("UPDATE solicitacoes SET resposta = ?, status = ?, atendido_por = ?, data_resposta = NOW() WHERE id = ?",
                      [$resposta, $status, $atendido_por, $solicitacao_id]);
                $_SESSION['success'] = 'Resposta enviada com sucesso!';
                header('Location: index.php?page=solicitacoes');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Erro ao responder solicitação: ' . $e->getMessage();
            }
        }
    }
}

// Incluir página baseada na rota
switch($page) {
    case 'carros':
        // Verificar se é view, edit ou create antes de incluir
        $action = $_GET['action'] ?? 'list';
        if ($action == 'view' || $action == 'edit' || $action == 'create' || $action == 'list') {
            include 'pages/carros.php';
        } else {
            include 'pages/carros.php';
        }
        break;
    case 'marcas':
        include 'pages/marcas.php';
        break;
    case 'categorias':
        include 'pages/categorias.php';
        break;
    case 'clientes':
        include 'pages/clientes.php';
        break;
    case 'vendas':
        include 'pages/vendas.php';
        break;
    case 'movimentacoes':
        include 'pages/movimentacoes.php';
        break;
    case 'relatorios':
        include 'pages/relatorios.php';
        break;
    case 'solicitacoes':
        include 'pages/solicitacoes.php';
        break;
    default:
        include 'pages/home.php';
        break;
}

// Incluir footer
include 'includes/footer.php';
?>



