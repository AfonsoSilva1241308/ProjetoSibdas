<?php
// 1. Segurança e sessão
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

// --- INÍCIO: LIGAÇÃO E QUERY ---
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=10464;dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query com os nomes EXATOS das colunas que tens agora na base de dados
    $sql = "SELECT 
                m.id AS id_manutencao,
                m.data_planeada,
                m.data_realizacao,
                m.tipo,
                m.estado,
                e.codigo_interno AS equipamento_codigo,
                e.designacao AS equipamento_nome,
                f.nome AS entidade_nome
            FROM manutencao m
            LEFT JOIN equipamento e ON m.equipamento_id = e.id
            LEFT JOIN fornecedor f ON m.fornecedor_id = f.id
            ORDER BY m.id DESC"; 
            
    $resultados = $ligacao->query($sql)->fetchAll(PDO::FETCH_OBJ);
    $erro = '';
} catch (PDOException $err) {
    $erro = "Erro técnico: " . $err->getMessage();
    $resultados = [];
}
$ligacao = null;
// --- FIM: LIGAÇÃO E QUERY ---

// 2. Definir os dados para a navbar
$titulo_pagina = "Registo de Intervenções"; 
$icone_pagina = "fa-solid fa-screwdriver-wrench"; 
$subtitulo_pagina = "Consulte o histórico de manutenções, reparações e calibrações de equipamentos.";
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex vh-100">
    
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 bg-light overflow-auto w-100">
        
        <?php include '../includes/navbar.php'; ?>

        <div class="card w-100 shadow-sm rounded border-0" style="max-width: 1200px;">
            <div class="card-body p-4 p-md-5">
                
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <h3 class="m-0 fw-bold text-dark">Histórico de Manutenções</h3>
                    
                    <div class="d-flex gap-3 align-items-center">
                        <form action="lista.php" method="GET" class="m-0 d-flex gap-2">
                            <input type="text" name="pesquisa_manutencao" class="form-control" placeholder="Pesquisar intervenção ou equipamento..." style="min-width: 280px;">
                            <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>
                </div>

                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger text-center fw-bold mt-4"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= $erro ?></div>
                <?php elseif (count($resultados) == 0): ?>
                    <div class="alert alert-info text-center mt-4"><i class="fa-solid fa-circle-info me-2"></i>Não existem manutenções registadas.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table id="tabela-manutencoes" class="table align-middle mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th class="py-3 text-uppercase fw-bold border-0 bg-light">Data</th>
                                    <th class="py-3 text-uppercase fw-bold border-0 bg-light">Equipamento</th>
                                    <th class="py-3 text-uppercase fw-bold border-0 bg-light">Tipo / Entidade</th>
                                    <th class="py-3 text-uppercase fw-bold border-0 bg-light text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php foreach ($resultados as $man): ?>
                                    <tr>
                                        <td class="py-3">
                                            <?php 
                                                // Verifica qual data mostrar (Realizada ganha à Planeada)
                                                $data_mostrar = !empty($man->data_realizacao) ? $man->data_realizacao : $man->data_planeada;
                                                $data_formatada = !empty($data_mostrar) ? date('d/m/Y', strtotime($data_mostrar)) : 'Sem data';
                                                
                                                // Preenche o ID com zeros para ficar com aspeto técnico (ex: #INT-0004)
                                                $id_formatado = str_pad($man->id_manutencao, 4, '0', STR_PAD_LEFT);
                                            ?>
                                            <span class="d-block fw-bold text-dark"><?= $data_formatada ?></span>
                                            <small class="text-muted">ID: #INT-<?= $id_formatado ?></small>
                                        </td>
                                        <td>
                                            <span class="d-block fw-bold text-dark"><?= htmlspecialchars($man->equipamento_codigo ?? 'N/A') ?></span>
                                            <small class="text-muted"><?= htmlspecialchars($man->equipamento_nome ?? 'Equipamento Removido') ?></small>
                                        </td>
                                        <td>
                                            <?php 
                                                if (str_contains(strtolower($man->tipo), 'corretiva') || str_contains(strtolower($man->tipo), 'avaria')) {
                                                    $icone = '<i class="fa-solid fa-triangle-exclamation text-warning me-2"></i><span class="text-warning fw-medium">';
                                                } else {
                                                    $icone = '<i class="fa-solid fa-shield-halved text-primary me-2"></i><span class="text-primary fw-medium">';
                                                }
                                            ?>
                                            <span class="d-block mb-1"><?= $icone . htmlspecialchars($man->tipo ?? 'Não definido') ?></span></span>
                                            <small class="text-muted">Entidade: <span class="text-dark fw-medium"><?= htmlspecialchars($man->entidade_nome ?? 'Dep. Engenharia Clínica') ?></span></small>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                                $estado = $man->estado ?? 'Pendente';
                                                $cor_badge = 'bg-secondary bg-opacity-10 text-secondary'; 
                                                if (strtolower($estado) == 'concluída' || strtolower($estado) == 'concluida') {
                                                    $cor_badge = 'bg-success bg-opacity-10 text-success';
                                                } elseif (strtolower($estado) == 'em curso') {
                                                    $cor_badge = 'bg-info bg-opacity-10 text-info';
                                                } elseif (strtolower($estado) == 'aguarda peças') {
                                                    $cor_badge = 'bg-warning bg-opacity-10 text-warning text-dark';
                                                }
                                            ?>
                                            <span class="badge <?= $cor_badge ?> rounded-pill px-3 py-1">
                                                <?= htmlspecialchars($estado) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top text-muted small">
                        <span>Total de registos: <strong id="total-registos-man">0</strong></span>
                        <nav>
                            <ul class="pagination pagination-sm m-0" id="paginacao-man">
                                </ul>
                        </nav>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAlterarPassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 mt-3 px-4">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-key text-primary me-2"></i> Alterar Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="#" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Password Atual</label>
                        <div class="input-group shadow-sm">
                            <input type="password" class="form-control bg-light border-end-0" id="passAtual" placeholder="Introduza a password atual" required>
                            <button class="btn bg-light border border-start-0 text-muted px-3" type="button" onclick="togglePassword('passAtual', 'iconAtual')"><i class="fa-solid fa-eye" id="iconAtual"></i></button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Nova Password</label>
                        <div class="input-group shadow-sm">
                            <input type="password" class="form-control border-end-0" id="passNova" placeholder="Mínimo 8 caracteres" required>
                            <button class="btn bg-white border border-start-0 text-muted px-3" type="button" onclick="togglePassword('passNova', 'iconNova')"><i class="fa-solid fa-eye" id="iconNova"></i></button>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Confirmar Nova Password</label>
                        <div class="input-group shadow-sm">
                            <input type="password" class="form-control border-end-0" id="passConfirma" placeholder="Repita a nova password" required>
                            <button class="btn bg-white border border-start-0 text-muted px-3" type="button" onclick="togglePassword('passConfirma', 'iconConfirma')"><i class="fa-solid fa-eye" id="iconConfirma"></i></button>
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

<?php include '../includes/footer.php'; ?>