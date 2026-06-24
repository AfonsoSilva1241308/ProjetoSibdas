<?php
// 1. Segurança e sessão
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

// 2. Definir os dados para a navbar
$titulo_pagina = "Registo de Intervenções";
$icone_pagina = "fa-solid fa-screwdriver-wrench";
$subtitulo_pagina = "Consulte o histórico de manutenções, reparações e calibrações de equipamentos.";

// ===============================
// Funções auxiliares
// ===============================
function h($valor) {
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function formatarData($data) {
    if (empty($data) || $data === '0000-00-00') {
        return 'Sem data';
    }

    try {
        return (new DateTime($data))->format('d/m/Y');
    } catch (Exception $e) {
        return h($data);
    }
}

function classeEstadoManutencao($estado) {
    $estadoLower = mb_strtolower((string)$estado, 'UTF-8');

    if ($estadoLower === 'concluída' || $estadoLower === 'concluida') {
        return 'bg-success bg-opacity-10 text-success';
    }

    if ($estadoLower === 'em curso') {
        return 'bg-info bg-opacity-10 text-info';
    }

    if ($estadoLower === 'aguarda peças' || $estadoLower === 'aguarda pecas') {
        return 'bg-warning bg-opacity-10 text-warning text-dark';
    }

    if ($estadoLower === 'cancelada' || $estadoLower === 'cancelado') {
        return 'bg-danger bg-opacity-10 text-danger';
    }

    return 'bg-secondary bg-opacity-10 text-secondary';
}

function htmlTipoManutencao($tipo) {
    $tipoLower = mb_strtolower((string)$tipo, 'UTF-8');
    $tipoSeguro = h($tipo ?: 'Não definido');

    if (strpos($tipoLower, 'corretiva') !== false || strpos($tipoLower, 'avaria') !== false) {
        return '<i class="fa-solid fa-triangle-exclamation text-warning me-2"></i><span class="text-warning fw-medium">' . $tipoSeguro . '</span>';
    }

    if (strpos($tipoLower, 'calibra') !== false) {
        return '<i class="fa-solid fa-gauge-high text-info me-2"></i><span class="text-info fw-medium">' . $tipoSeguro . '</span>';
    }

    return '<i class="fa-solid fa-shield-halved text-primary me-2"></i><span class="text-primary fw-medium">' . $tipoSeguro . '</span>';
}

// ===============================
// Filtros
// ===============================
$pesquisa = trim($_GET['pesquisa_manutencao'] ?? '');

// --- INÍCIO: LIGAÇÃO E QUERY ---
try {
    $porta = defined('MYSQL_PORT') ? MYSQL_PORT : '10464';

    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . $porta . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT
            m.id AS id_manutencao,
            m.data_planeada,
            m.data_realizacao,
            m.tipo,
            m.estado,
            e.id AS equipamento_id,
            e.codigo_interno AS equipamento_codigo,
            e.designacao AS equipamento_nome,
            f.id AS fornecedor_id,
            f.nome AS entidade_nome
        FROM manutencao m
        LEFT JOIN equipamento e ON m.equipamento_id = e.id
        LEFT JOIN fornecedor f ON m.fornecedor_id = f.id
    ";

    $params = [];

    if ($pesquisa !== '') {
        $sql .= "
            WHERE
                m.tipo LIKE :pesquisa
                OR m.estado LIKE :pesquisa
                OR DATE_FORMAT(m.data_planeada, '%d/%m/%Y') LIKE :pesquisa
                OR DATE_FORMAT(m.data_realizacao, '%d/%m/%Y') LIKE :pesquisa
                OR e.codigo_interno LIKE :pesquisa
                OR e.designacao LIKE :pesquisa
                OR f.nome LIKE :pesquisa
        ";
        $params[':pesquisa'] = '%' . $pesquisa . '%';
    }

    $sql .= " ORDER BY COALESCE(m.data_realizacao, m.data_planeada) DESC, m.id DESC";

    $stmt = $ligacao->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_OBJ);
    $erro = '';
} catch (PDOException $err) {
    $erro = "Erro técnico: " . $err->getMessage();
    $resultados = [];
}
$ligacao = null;
// --- FIM: LIGAÇÃO E QUERY ---
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="d-flex vh-100">
    
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 bg-light overflow-auto w-100">
        
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="card w-100 shadow-sm rounded border-0" style="max-width: 1200px;">
            <div class="card-body p-4 p-md-5">
                
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <h3 class="m-0 fw-bold text-dark">Histórico de Manutenções</h3>
                    
                    <div class="d-flex gap-3 align-items-center">
                        <form action="lista.php" method="GET" class="m-0 d-flex gap-2">
                            <input
                                type="text"
                                name="pesquisa_manutencao"
                                class="form-control"
                                placeholder="Pesquisar intervenção ou equipamento..."
                                value="<?= h($pesquisa) ?>"
                                style="min-width: 280px;"
                            >
                            <button type="submit" class="btn btn-outline-primary" title="Pesquisar">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>

                            <?php if ($pesquisa !== ''): ?>
                                <a href="lista.php" class="btn btn-outline-secondary" title="Limpar pesquisa">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger text-center fw-bold mt-4">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i><?= h($erro) ?>
                    </div>
                <?php else: ?>
                    <?php if ($pesquisa !== ''): ?>
                        <div class="alert alert-light border d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <span>
                                <i class="fa-solid fa-filter me-2 text-primary"></i>
                                Pesquisa ativa por: <strong><?= h($pesquisa) ?></strong>
                            </span>
                            <span class="text-muted small">
                                <?= count($resultados) ?> resultado(s)
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if (count($resultados) === 0): ?>
                        <div class="alert alert-info text-center mt-4">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            <?php if ($pesquisa !== ''): ?>
                                Não foram encontradas manutenções para a pesquisa feita.
                            <?php else: ?>
                                Não existem manutenções registadas.
                            <?php endif; ?>
                        </div>
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
                                        <?php
                                            $dataMostrar = !empty($man->data_realizacao) ? $man->data_realizacao : $man->data_planeada;
                                            $dataFormatada = formatarData($dataMostrar);
                                            $idFormatado = str_pad((string)$man->id_manutencao, 4, '0', STR_PAD_LEFT);
                                            $estado = $man->estado ?? 'Pendente';
                                        ?>
                                        <tr>
                                            <td class="py-3">
                                                <span class="d-block fw-bold text-dark"><?= h($dataFormatada) ?></span>
                                                <small class="text-muted">ID: #INT-<?= h($idFormatado) ?></small>
                                            </td>

                                            <td>
                                                <?php if (!empty($man->equipamento_id)): ?>
                                                    <a href="../equipamentos/detalhes.php?id_equipamento=<?= urlencode(aes_encrypt($man->equipamento_id)) ?>" class="text-dark text-decoration-none">
                                                        <span class="d-block fw-bold"><?= h($man->equipamento_codigo ?? 'N/A') ?></span>
                                                        <small class="text-muted"><?= h($man->equipamento_nome ?? 'Equipamento Removido') ?></small>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="d-block fw-bold text-dark">N/A</span>
                                                    <small class="text-muted">Equipamento Removido</small>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <span class="d-block mb-1"><?= htmlTipoManutencao($man->tipo ?? '') ?></span>
                                                <small class="text-muted">
                                                    Entidade:
                                                    <span class="text-dark fw-medium"><?= h($man->entidade_nome ?? 'Dep. Engenharia Clínica') ?></span>
                                                </small>
                                            </td>

                                            <td class="text-center">
                                                <span class="badge <?= h(classeEstadoManutencao($estado)) ?> rounded-pill px-3 py-1">
                                                    <?= h($estado) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top text-muted small">
                            <span>Total de registos: <strong id="total-registos-man"><?= count($resultados) ?></strong></span>
                            <nav>
                                <ul class="pagination pagination-sm m-0" id="paginacao-man"></ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

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

<?php include __DIR__ . '/../includes/footer.php'; ?>