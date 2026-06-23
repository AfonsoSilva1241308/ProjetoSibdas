<?php
// 1. Carrega as configurações PRIMEIRO (Isto resolve o erro do MYSQL_HOST!)
require_once __DIR__ . '/../config/config.php';

// 2. Inclui o ficheiro de funções e valida se o utilizador tem sessão ativa
require_once __DIR__ . '/includes/funcoes.php'; 
redirect_if_not_logged(); 

// =======================================================
// MAGIA DO DASHBOARD: QUERIES E ESTATÍSTICAS REAIS
// =======================================================
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=10464;dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Contagens para os 4 Cartões Principais
    $total_equipamentos = $ligacao->query("SELECT COUNT(*) FROM equipamento")->fetchColumn();
    $total_ativos       = $ligacao->query("SELECT COUNT(*) FROM equipamento WHERE estado = 'Ativo'")->fetchColumn();
    $total_manutencao   = $ligacao->query("SELECT COUNT(*) FROM equipamento WHERE estado LIKE '%Manutenção%' OR estado LIKE '%Calibração%'")->fetchColumn();
    $total_inativos     = $ligacao->query("SELECT COUNT(*) FROM equipamento WHERE estado IN ('Inativo', 'Abatido')")->fetchColumn();

    // 2. Alerta de Segurança
    $alerta_sv = $ligacao->query("SELECT COUNT(*) FROM equipamento WHERE criticidade = 'Suporte de Vida' AND estado != 'Ativo'")->fetchColumn();

    // 3. Gráfico Criticidade
    $dados_criticidade = $ligacao->query("SELECT criticidade, COUNT(*) as total FROM equipamento GROUP BY criticidade")->fetchAll(PDO::FETCH_ASSOC);

    // 4. Gráfico Serviços
    $dados_servicos = $ligacao->query("
        SELECT 
            l.servico, 
            COUNT(e.id) as total,
            SUM(CASE WHEN e.categoria = 'Suporte de Vida' THEN 1 ELSE 0 END) as total_sv
        FROM equipamento e 
        JOIN localizacao l ON e.localizacao_id = l.id 
        GROUP BY l.servico
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // 5. Gráfico Fornecedores
    $dados_fornecedores = $ligacao->query("
        SELECT marca as nome, COUNT(id) as total 
        FROM equipamento 
        WHERE marca IS NOT NULL AND marca != ''
        GROUP BY marca 
        ORDER BY total DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    // 6. Gráfico Idade
    $dados_idade = $ligacao->query("
        SELECT 
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, data_aquisicao, CURDATE()) < 2 THEN 1 ELSE 0 END) as age_0_2,
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, data_aquisicao, CURDATE()) BETWEEN 2 AND 5 THEN 1 ELSE 0 END) as age_2_5,
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, data_aquisicao, CURDATE()) BETWEEN 6 AND 10 THEN 1 ELSE 0 END) as age_5_10,
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, data_aquisicao, CURDATE()) > 10 THEN 1 ELSE 0 END) as age_10_plus
        FROM equipamento
        WHERE data_aquisicao IS NOT NULL
    ")->fetch(PDO::FETCH_ASSOC);

// 8. Lista: Garantias a Terminar (Expiradas ou que expiram nos próximos 30 dias)
    $lista_garantias = $ligacao->query("
        SELECT e.designacao, e.marca, l.servico, g.data_fim, DATEDIFF(g.data_fim, CURDATE()) as dias_restantes
        FROM garantia_contrato g
        JOIN equipamento e ON g.equipamento_id = e.id
        LEFT JOIN localizacao l ON e.localizacao_id = l.id
        WHERE g.data_fim <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        ORDER BY g.data_fim ASC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // 9. Lista: Equipamentos sem Documentação
    $lista_sem_docs = $ligacao->query("
        SELECT designacao, marca, fabricante
        FROM equipamento
        WHERE id NOT IN (SELECT equipamento_id FROM documento)
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    // 7. Garantias e Documentação
    $garantias_expiradas = $ligacao->query("SELECT COUNT(*) FROM garantia_contrato WHERE data_fim < CURDATE()")->fetchColumn();
    $sem_documentos      = $ligacao->query("SELECT COUNT(*) FROM equipamento WHERE id NOT IN (SELECT equipamento_id FROM documento)")->fetchColumn();

} catch (PDOException $err) {
    // Valores de segurança caso a BD falhe
    $total_equipamentos = $total_ativos = $total_manutencao = $total_inativos = $alerta_sv = 0;
    $dados_criticidade = $dados_servicos = $dados_fornecedores = [];
    $dados_idade = ['age_0_2'=>0, 'age_2_5'=>0, 'age_5_10'=>0, 'age_10_plus'=>0];
    $garantias_expiradas = $sem_documentos = 0;
    $lista_garantias = []; $lista_sem_docs = [];
}
$ligacao = null;

$titulo_pagina = "Visão Geral do Sistema"; 
?>
<?php include 'includes/header.php'; ?>

<div class="d-flex vh-100">
    <?php include 'includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 bg-light overflow-auto w-100">
        
        <?php include 'includes/navbar.php'; ?>

        <div class="d-flex justify-content-end align-items-center mb-4">
            <span id="tempo-atualizacao" class="badge bg-light text-secondary border px-3 py-2 fw-medium shadow-sm">
                <i class="fa-regular fa-clock me-1"></i> Atualizado hoje às 09:14
            </span>
        </div>
<?php if ($alerta_sv > 0): ?>
        <div class="alert border-danger bg-danger bg-opacity-10 text-danger border border-start border-4 d-flex align-items-center mb-4 shadow-sm" role="alert">
            <i class="fa-solid fa-triangle-exclamation fs-4 me-3"></i>
            <div>
                <h6 class="alert-heading fw-bold mb-1">Atenção: Equipamentos Críticos</h6>
                <p class="mb-0 small">Existem <strong><?= $alerta_sv ?> equipamento(s) de Suporte de Vida</strong> atualmente indisponíveis ou em manutenção. Verifique as prioridades de intervenção!</p>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
            <div class="row g-4 mb-5">
    
    <!-- Total Equipamentos -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
            <div class="card-body p-4">
                <p class="text-muted mb-1 fw-semibold text-uppercase small" style="letter-spacing: 1px;">Total Equipamentos</p>
                <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_equipamentos, 0, ',', ' ') ?></h3>
            </div>
        </div>
    </div>
    
    <!-- Ativos -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
            <div class="card-body p-4">
                <p class="text-muted mb-1 fw-semibold text-uppercase small" style="letter-spacing: 1px;">Ativos</p>
               <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_ativos, 0, ',', ' ') ?></h3>
            </div>
        </div>
    </div>
    
    <!-- Em Manutenção -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
            <div class="card-body p-4">
                <p class="text-muted mb-1 fw-semibold text-uppercase small" style="letter-spacing: 1px;">Em Manutenção</p>
                <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_manutencao, 0, ',', ' ') ?></h3>
            </div>
        </div>
    </div>
    
    <!-- Inativos -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
            <div class="card-body p-4">
                <p class="text-muted mb-1 fw-semibold text-uppercase small" style="letter-spacing: 1px;">Inativos</p>
                <h3 class="fw-bold mb-0 text-dark"><?= number_format($total_inativos, 0, ',', ' ') ?></h3>
            </div>
        </div>
    </div>
<div class="row g-4 mb-5">
    <div class="col-md-6">
        <div class="card border-0 border-start border-dark border-4 shadow-sm">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary text-uppercase fw-bold small mb-1">Garantia Expirada</h6>
                            <div class="h3 fw-bold text-dark mb-0"><?= $garantias_expiradas ?></div>
                </div>
                <i class="fa-solid fa-calendar-times text-danger fs-3 opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 border-start border-secondary border-4 shadow-sm">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-secondary text-uppercase fw-bold small mb-1">Sem Documentação Associada</h6>
                            <div class="h3 fw-bold text-dark mb-0"><?= $sem_documentos ?></div>
                </div>
                <i class="fa-solid fa-exclamation-triangle text-secondary fs-3 opacity-25"></i>
            </div>
        </div>
    </div>
</div>
</div>
<div class="row g-4 mb-5">
    
    <div class="col-12 col-lg-8">
    <div class="card shadow-sm border-0 h-100">
        <div class="card-body p-4 p-md-5">
            <h5 class="fw-bold text-dark mb-4">Equipamentos por Serviço</h5>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="graficoServicos"></canvas>
            </div>
        </div>
    </div>
</div>

   <div class="col-12 col-lg-4">
    <div class="card shadow-sm border-0 h-100">
        <div class="card-body p-4 p-md-5 d-flex flex-column">
            <h5 class="fw-bold text-dark mb-4">Nível de Criticidade</h5>
            <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="position: relative; height: 300px; width: 100%;">
                <canvas id="graficoCriticidade"></canvas>
            </div>
        </div>
    </div>
</div>
</div>
<div class="row g-4 mb-5">
    
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-4">Os Principais Forncedores</h5>
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="graficoFornecedores"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-4">Idade dos Equipamentos </h5>
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="graficoIdade"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row g-4 mb-4">
    
    <div class="col-12 col-xl-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2 px-4">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h5 class="fw-bold text-dark m-0">Garantias a Terminar</h5>
                </div>
                <a href="equipamentos/lista.html" class="text-decoration-none small fw-semibold">Ver tudo <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
            
            <div class="card-body px-4 pb-4 pt-2">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="text-muted small text-uppercase border-bottom">
                            <tr>
                                <th class="border-0 bg-light rounded-start py-2">Equipamento</th>
                                <th class="border-0 bg-light py-2">Serviço</th>
                                <th class="border-0 bg-light rounded-end text-end py-2">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                                    <?php if (empty($lista_garantias)): ?>
                                        <tr><td colspan="3" class="text-center py-4 text-muted">Não há garantias a expirar brevemente.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($lista_garantias as $garantia): ?>
                                        <tr>
                                            <td class="fw-medium text-dark py-3">
                                                <?= htmlspecialchars($garantia['designacao']) ?> 
                                                <small class="d-block text-muted fw-normal"><?= htmlspecialchars($garantia['marca'] ?: 'Sem marca') ?></small>
                                            </td>
                                            <td class="text-muted"><?= htmlspecialchars($garantia['servico'] ?: 'Não definido') ?></td>
                                            <td class="text-end">
                                                <?php if ($garantia['dias_restantes'] < 0): ?>
                                                    <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">Expirada</span>
                                                <?php elseif ($garantia['dias_restantes'] == 0): ?>
                                                    <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">Expira Hoje</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2 shadow-sm">Expira em <?= $garantia['dias_restantes'] ?> dias</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2 px-4">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-file-contract"></i>
                    </div>
                    <h5 class="fw-bold text-dark m-0">Sem Documentação</h5>
                </div>
                <a href="equipamentos/lista.html" class="text-decoration-none small fw-semibold">Ver tudo <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
            
            <div class="card-body px-4 pb-4 pt-2">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="text-muted small text-uppercase border-bottom">
                            <tr>
                                <th class="border-0 bg-light rounded-start py-2">Equipamento</th>
                                <th class="border-0 bg-light py-2">Fabricante</th>
                                <th class="border-0 bg-light rounded-end text-end py-2">Em Falta</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                                    <?php if (empty($lista_sem_docs)): ?>
                                        <tr><td colspan="3" class="text-center py-4 text-muted">Todos os equipamentos têm documentação.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($lista_sem_docs as $eq): ?>
                                        <tr>
                                            <td class="fw-medium text-dark py-3">
                                                <?= htmlspecialchars($eq['designacao']) ?>
                                            </td>
                                            <td class="text-muted"><?= htmlspecialchars($eq['marca'] ?: 'Não definido') ?></td>
                                            <td class="text-end"><span class="text-muted small fw-medium"><i class="fa-solid fa-file-pdf text-danger me-2"></i>Falta Documento</span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="modal fade" id="modalAlterarPassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 mt-3 px-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fa-solid fa-key text-primary me-2"></i> Alterar Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="alterar_password.php" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Password Atual</label>
                        <div class="input-group shadow-sm">
                            <input type="password" name="password_atual" class="form-control bg-light border-end-0" id="passAtual" placeholder="Introduza a password atual" required>
                            <button class="btn bg-light border border-start-0 text-muted px-3" type="button" onclick="togglePassword('passAtual', 'iconAtual')">
                                <i class="fa-solid fa-eye" id="iconAtual"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Nova Password</label>
                        <div class="input-group shadow-sm">
                            <input type="password" name="password_nova" class="form-control border-end-0" id="passNova" placeholder="Mínimo 8 caracteres" required>
                            <button class="btn bg-white border border-start-0 text-muted px-3" type="button" onclick="togglePassword('passNova', 'iconNova')">
                                <i class="fa-solid fa-eye" id="iconNova"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Confirmar Nova Password</label>
                        <div class="input-group shadow-sm">
                            <input type="password" name="password_confirma" class="form-control border-end-0" id="passConfirma" placeholder="Repita a nova password" required>
                            <button class="btn bg-white border border-start-0 text-muted px-3" type="button" onclick="togglePassword('passConfirma', 'iconConfirma')">
                                <i class="fa-solid fa-eye" id="iconConfirma"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top mt-4 pt-3">
                        <button type="button" class="btn btn-light border fw-medium px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-medium px-4">Guardar Nova Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    const DADOS_CRITICIDADE = <?= json_encode($dados_criticidade) ?>;
    const DADOS_SERVICOS = <?= json_encode($dados_servicos) ?>;
</script>
    <?php include 'includes/footer.php'; ?>