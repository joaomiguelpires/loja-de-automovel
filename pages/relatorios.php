<?php
// Consulta PIVOT - Vendas por Mês e Marca
// Esta consulta transforma dados de linhas em colunas (formato PIVOT)

// Buscar vendas agrupadas por mês e marca
$vendasPorMesMarca = fetchAll("
    SELECT 
        m.nome as marca,
        MONTH(v.data_venda) as mes,
        MONTHNAME(v.data_venda) as mes_nome,
        COUNT(v.id) as total_vendas,
        SUM(v.valor) as valor_total
    FROM vendas v
    LEFT JOIN carros c ON v.carro_id = c.id
    LEFT JOIN marcas m ON c.marca_id = m.id
    WHERE YEAR(v.data_venda) = YEAR(CURRENT_DATE())
    GROUP BY m.id, m.nome, MONTH(v.data_venda), MONTHNAME(v.data_venda)
    ORDER BY m.nome, MONTH(v.data_venda)
");

// Preparar dados para formato PIVOT
$pivotData = [];
$marcas = [];
$meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 
          'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

foreach ($vendasPorMesMarca as $row) {
    $marca = $row['marca'];
    $mes = $row['mes_nome'];
    
    if (!in_array($marca, $marcas)) {
        $marcas[] = $marca;
    }
    
    if (!isset($pivotData[$marca])) {
        $pivotData[$marca] = [];
    }
    
    $pivotData[$marca][$mes] = [
        'vendas' => $row['total_vendas'],
        'valor' => $row['valor_total']
    ];
}

// Outras consultas para relatórios
$totalVendas = fetchOne("SELECT COUNT(*) as total, SUM(valor) as valor_total FROM vendas")['total'] ?? 0;
$valorTotalVendas = fetchOne("SELECT SUM(valor) as valor_total FROM vendas")['valor_total'] ?? 0;

// Vendas por marca (agrupamento)
$vendasPorMarca = fetchAll("
    SELECT 
        m.nome as marca,
        COUNT(v.id) as total_vendas,
        SUM(v.valor) as valor_total
    FROM vendas v
    LEFT JOIN carros c ON v.carro_id = c.id
    LEFT JOIN marcas m ON c.marca_id = m.id
    GROUP BY m.id, m.nome
    ORDER BY total_vendas DESC
");

// Vendas por categoria (agrupamento)
$vendasPorCategoria = fetchAll("
    SELECT 
        cat.nome as categoria,
        COUNT(v.id) as total_vendas,
        SUM(v.valor) as valor_total
    FROM vendas v
    LEFT JOIN carros c ON v.carro_id = c.id
    LEFT JOIN categorias cat ON c.categoria_id = cat.id
    GROUP BY cat.id, cat.nome
    ORDER BY total_vendas DESC
");

// Vendas por mês (agrupamento)
$vendasPorMes = fetchAll("
    SELECT 
        MONTHNAME(v.data_venda) as mes,
        MONTH(v.data_venda) as mes_num,
        COUNT(v.id) as total_vendas,
        SUM(v.valor) as valor_total
    FROM vendas v
    WHERE YEAR(v.data_venda) = YEAR(CURRENT_DATE())
    GROUP BY MONTH(v.data_venda), MONTHNAME(v.data_venda)
    ORDER BY MONTH(v.data_venda)
");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">
            <i class="fas fa-chart-bar me-2"></i>Relatórios
        </h2>
        <p class="text-muted mb-0">Análise de vendas e estatísticas do sistema</p>
    </div>
</div>

<!-- Resumo Geral -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Resumo Geral</h5>
                <div class="row">
                    <div class="col-6">
                        <h3 class="fw-bold text-primary"><?= $totalVendas ?></h3>
                        <small class="text-muted">Total de Vendas</small>
                    </div>
                    <div class="col-6">
                        <h3 class="fw-bold text-success">R$ <?= number_format($valorTotalVendas ?? 0, 2, ',', '.') ?></h3>
                        <small class="text-muted">Valor Total</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Consulta PIVOT: Vendas por Mês e Marca -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-table me-2"></i>Consulta PIVOT - Vendas por Marca e Mês (<?= date('Y') ?>)
        </h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">
            <strong>Formato PIVOT:</strong> Dados transformados de linhas para colunas, mostrando vendas por marca em cada mês.
        </p>
        
        <?php if (!empty($pivotData)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Marca</th>
                        <?php foreach ($meses as $mes): ?>
                            <th class="text-center"><?= $mes ?></th>
                        <?php endforeach; ?>
                        <th class="text-center"><strong>Total</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($marcas as $marca): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($marca) ?></strong></td>
                        <?php 
                        $totalMarca = 0;
                        foreach ($meses as $mes): 
                            $vendas = $pivotData[$marca][$mes]['vendas'] ?? 0;
                            $totalMarca += $vendas;
                        ?>
                            <td class="text-center">
                                <?php if ($vendas > 0): ?>
                                    <span class="badge bg-info"><?= $vendas ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <td class="text-center">
                            <strong class="text-primary"><?= $totalMarca ?></strong>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td><strong>Total</strong></td>
                        <?php 
                        foreach ($meses as $mes): 
                            $totalMes = 0;
                            foreach ($marcas as $marca) {
                                $totalMes += $pivotData[$marca][$mes]['vendas'] ?? 0;
                            }
                        ?>
                            <td class="text-center">
                                <strong><?= $totalMes > 0 ? $totalMes : '-' ?></strong>
                            </td>
                        <?php endforeach; ?>
                        <td class="text-center">
                            <strong class="text-success"><?= $totalVendas ?></strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Nenhuma venda encontrada para o ano atual.
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Vendas por Marca (Agrupamento) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="fas fa-chart-pie me-2"></i>Consulta de Agrupamento - Vendas por Marca
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($vendasPorMarca)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Marca</th>
                        <th class="text-center">Total de Vendas</th>
                        <th class="text-end">Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vendasPorMarca as $item): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($item['marca']) ?></strong></td>
                        <td class="text-center">
                            <span class="badge bg-primary"><?= $item['total_vendas'] ?></span>
                        </td>
                        <td class="text-end">
                            <strong>R$ <?= number_format($item['valor_total'] ?? 0, 2, ',', '.') ?></strong>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Nenhuma venda encontrada.
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Vendas por Categoria (Agrupamento) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-warning text-white">
        <h5 class="mb-0">
            <i class="fas fa-chart-bar me-2"></i>Consulta de Agrupamento - Vendas por Categoria
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($vendasPorCategoria)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Categoria</th>
                        <th class="text-center">Total de Vendas</th>
                        <th class="text-end">Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vendasPorCategoria as $item): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($item['categoria']) ?></strong></td>
                        <td class="text-center">
                            <span class="badge bg-warning text-dark"><?= $item['total_vendas'] ?></span>
                        </td>
                        <td class="text-end">
                            <strong>R$ <?= number_format($item['valor_total'] ?? 0, 2, ',', '.') ?></strong>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Nenhuma venda encontrada.
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Vendas por Mês (Agrupamento) -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-calendar-alt me-2"></i>Consulta de Agrupamento - Vendas por Mês (<?= date('Y') ?>)
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($vendasPorMes)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Mês</th>
                        <th class="text-center">Total de Vendas</th>
                        <th class="text-end">Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vendasPorMes as $item): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($item['mes']) ?></strong></td>
                        <td class="text-center">
                            <span class="badge bg-info"><?= $item['total_vendas'] ?></span>
                        </td>
                        <td class="text-end">
                            <strong>R$ <?= number_format($item['valor_total'] ?? 0, 2, ',', '.') ?></strong>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Nenhuma venda encontrada para o ano atual.
        </div>
        <?php endif; ?>
    </div>
</div>

