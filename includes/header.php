<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja de Carros - Sistema de Gerenciamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- Font Awesome - usando CDN mais confiável -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.0/css/all.css" integrity="sha384-HzLeBuhoNPvSl5KYnjx0BT+WB0QEEqLprO+NBkkk5gbc67FTaL7XIGa2w1L0Xbgc" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8f9fa; 
        }
        .hero-section { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
        }
        .stats-card { 
            transition: all 0.3s ease; 
        }
        .stats-card:hover { 
            transform: translateY(-8px) scale(1.02); 
            box-shadow: 0 15px 35px rgba(0,0,0,0.2) !important;
        }
        .stats-icon { 
            width: 60px; 
            height: 60px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.5rem; 
        }
        .action-card { 
            transition: all 0.3s ease; 
        }
        .action-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important; 
        }
        .action-icon { 
            width: 50px; 
            height: 50px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.2rem; 
        }
        .car-card { 
            transition: all 0.3s ease; 
        }
        .car-card:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 6px 20px rgba(0,0,0,0.12); 
        }
        .bg-gradient { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
        }
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        .nav-link.active {
            background-color: rgba(255,255,255,0.1) !important;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-gradient shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="?page=home">
                <i class="fas fa-car me-2"></i>Loja de Carros
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($page == 'carros') ? 'active' : '' ?>" href="?page=carros">
                            <i class="fas fa-car me-1"></i>Carros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page == 'solicitacoes') ? 'active' : '' ?>" href="?page=solicitacoes">
                            <i class="fas fa-comments me-1"></i>Solicitações
                        </a>
                    </li>
                    <?php 
                    $userRole = $_SESSION['user_role'] ?? '';
                    // Apenas vendedores e admin veem estes menus
                    if ($userRole === 'vendedor' || $userRole === 'admin'): 
                    ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page == 'marcas') ? 'active' : '' ?>" href="?page=marcas">
                            <i class="fas fa-tags me-1"></i>Marcas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page == 'categorias') ? 'active' : '' ?>" href="?page=categorias">
                            <i class="fas fa-list me-1"></i>Categorias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page == 'clientes') ? 'active' : '' ?>" href="?page=clientes">
                            <i class="fas fa-users me-1"></i>Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page == 'vendas') ? 'active' : '' ?>" href="?page=vendas">
                            <i class="fas fa-shopping-cart me-1"></i>Vendas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page == 'movimentacoes') ? 'active' : '' ?>" href="?page=movimentacoes">
                            <i class="fas fa-exchange-alt me-1"></i>Movimentações
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page == 'relatorios') ? 'active' : '' ?>" href="?page=relatorios">
                            <i class="fas fa-chart-bar me-1"></i>Relatórios
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <div class="d-flex align-items-center">
                    <span class="text-white me-3">
                        <i class="fas fa-user me-1"></i><?= htmlspecialchars($_SESSION['user_nome'] ?? 'Usuário') ?>
                        <small class="d-block text-white-50" style="font-size: 0.75rem;">
                            <?php 
                            $roleLabel = [
                                'admin' => 'Administrador',
                                'vendedor' => 'Vendedor',
                                'cliente' => 'Cliente'
                            ];
                            echo $roleLabel[$_SESSION['user_role'] ?? ''] ?? 'Usuário';
                            ?>
                        </small>
                    </span>
                    <?php if ($userRole === 'vendedor' || $userRole === 'admin'): ?>
                    <a href="?page=carros&action=create" class="btn btn-light btn-sm me-2">
                        <i class="fas fa-plus me-1"></i>Novo Carro
                    </a>
                    <?php endif; ?>
                    <a href="pages/logout.php" class="btn btn-outline-light btn-sm" onclick="return confirm('Deseja realmente sair?')">
                        <i class="fas fa-sign-out-alt me-1"></i>Sair
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>






