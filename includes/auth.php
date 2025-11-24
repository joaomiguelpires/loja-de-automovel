<?php
/**
 * Funções de autenticação e controle de acesso
 */

/**
 * Verifica se o usuário está logado
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Obtém o role do usuário atual
 */
function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Verifica se o usuário tem um role específico
 */
function hasRole($role) {
    return getUserRole() === $role;
}

/**
 * Verifica se o usuário tem acesso a uma página
 * Clientes: apenas visualização de carros, home e solicitações
 * Vendedores: acesso completo a todas as informações (exceto configurações administrativas)
 * Admin: acesso total
 */
function canAccessPage($page) {
    $role = getUserRole();
    
    // Páginas permitidas para clientes
    $clientePages = ['home', 'carros', 'solicitacoes'];
    
    // Páginas permitidas para vendedores (todas exceto admin)
    $vendedorPages = ['home', 'carros', 'marcas', 'categorias', 'clientes', 'vendas', 'movimentacoes', 'relatorios', 'solicitacoes'];
    
    // Admin tem acesso a tudo
    if ($role === 'admin') {
        return true;
    }
    
    // Vendedor tem acesso às páginas listadas
    if ($role === 'vendedor') {
        return in_array($page, $vendedorPages);
    }
    
    // Cliente tem acesso apenas às páginas listadas
    if ($role === 'cliente') {
        return in_array($page, $clientePages);
    }
    
    // Se não tiver role definido, negar acesso
    return false;
}

/**
 * Verifica se o usuário pode realizar uma ação específica
 */
function canPerformAction($action, $page = null) {
    $role = getUserRole();
    
    // Admin pode tudo
    if ($role === 'admin') {
        return true;
    }
    
    // Clientes podem apenas visualizar e criar solicitações
    if ($role === 'cliente') {
        // Clientes podem criar solicitações
        if ($page === 'solicitacoes' && $action === 'create') {
            return true;
        }
        // Clientes podem visualizar carros e suas próprias solicitações
        return in_array($action, ['view', 'list']);
    }
    
    // Vendedores podem criar, editar, visualizar, mas não excluir (apenas admin)
    if ($role === 'vendedor') {
        return in_array($action, ['create', 'edit', 'view', 'list', 'update']);
    }
    
    return false;
}

/**
 * Redireciona para página de acesso negado
 */
function redirectToAccessDenied() {
    $_SESSION['error'] = 'Você não tem permissão para acessar esta página.';
    header('Location: index.php?page=home');
    exit;
}

/**
 * Verifica e redireciona se não tiver permissão
 */
function requirePermission($page, $action = 'list') {
    if (!isLoggedIn()) {
        header('Location: pages/login.php');
        exit;
    }
    
    if (!canAccessPage($page)) {
        redirectToAccessDenied();
    }
    
    if (!canPerformAction($action, $page)) {
        redirectToAccessDenied();
    }
}

