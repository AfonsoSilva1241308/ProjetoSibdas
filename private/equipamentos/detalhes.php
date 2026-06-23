<?php
// 1. Segurança e Configuração (sempre no topo)
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../../config/config.php';
redirect_if_not_logged();

// 2. Obter e desencriptar o ID do equipamento
$idParam = $_GET['id_equipamento'] ?? $_GET['id'] ?? '';
$idEquipamento = is_numeric($idParam) ? (int)$idParam : aes_decrypt($idParam);

if (!$idEquipamento || !is_numeric($idEquipamento)) {
    header('Location: lista.php');
    exit;
}

$erro = '';
$equipamento = null;
$componentes = [];
$consumiveis = [];
$documentos  = [];

// 3. Obter dados da Base de Dados
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3.1 Equipamento principal (com localização, se existir)
    $stmt = $ligacao->prepare("
        SELECT e.*, l.edificio, l.piso, l.servico AS localizacao_servico, l.sala AS localizacao_sala
        FROM equipamento e
        LEFT JOIN localizacao l ON e.localizacao_id = l.id
        WHERE e.id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $idEquipamento]);
    $equipamento = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$equipamento) {
        header('Location: lista.php');
        exit;
    }

    // 3.2 Componentes/acessórios (equipamentos-filho)
    $stmtComponentes = $ligacao->prepare("
        SELECT id, codigo_interno, designacao, estado
        FROM equipamento
        WHERE equipamento_pai_id = :id
        ORDER BY codigo_interno
    ");
    $stmtComponentes->execute([':id' => $idEquipamento]);
    $componentes = $stmtComponentes->fetchAll(PDO::FETCH_OBJ);

    // 3.3 Consumíveis associados
    $stmtConsumiveis = $ligacao->prepare("
        SELECT designacao, categoria, frequencia
        FROM consumivel
        WHERE equipamento_id = :id
        ORDER BY designacao
    ");
    $stmtConsumiveis->execute([':id' => $idEquipamento]);
    $consumiveis = $stmtConsumiveis->fetchAll(PDO::FETCH_OBJ);

    // 3.4 Documentos associados
    $stmtDocumentos = $ligacao->prepare("
        SELECT *
        FROM documento
        WHERE equipamento_id = :id
        ORDER BY categoria, titulo
    ");
    $stmtDocumentos->execute([':id' => $idEquipamento]);
    $documentos = $stmtDocumentos->fetchAll(PDO::FETCH_OBJ);

} catch (PDOException $err) {
    $erro = "Erro ao carregar o equipamento: " . $err->getMessage();
    $equipamento = null;
    $componentes = $consumiveis = $documentos = [];
}

// 4. Navbar: apenas botão Voltar (SEM botão Editar)
$link_voltar  = "lista.php";
$titulo_pagina = "Ficha Técnica do Equipamento";
$icone_pagina  = "fa-solid fa-file-medical";
?>
<?php include '/../includes/header.php'; ?>

<div class="d-flex vh-100">
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100 bg-light">
        <?php include '../includes/navbar.php'; ?>

        <div class="d-flex justify-content-center mt-4">

            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger fw-bold shadow-sm w-100" style="max-width: 1200px;">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($erro) ?>
                </div>
            <?php elseif ($equipamento): ?>

                <!-- Card com a MESMA moldura azul do editar -->
                <div class="card w-100 shadow-sm rounded border-top border-primary border-4" style="max-width: 1200px;">
                    <div class="card-body p-4 p-md-5">

                        <!-- Cabeçalho (igual ao editar, mas sem botão Editar) -->
                        <div class="mb-1">
                            <h2 class="mb-1 text-primary">
                                <strong><i class="fa-solid fa-file-medical me-2"></i> Ficha Técnica do Equipamento</strong>
                            </h2>
                            <p class="text-muted m-0">
                                Código de Inventário Hospitalar:
                                <span class="badge bg-dark fs-6 font-monospace ms-1"><?= htmlspecialchars($equipamento->codigo_interno ?? 'N/A') ?></span>
                            </p>
                        </div>

                        <!-- ABAS: exatamente as mesmas do editar -->
                        <ul class="nav nav-tabs mb-4 mt-4" id="detalhesTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active text-dark fw-bold border-bottom-0" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab" aria-controls="dados" aria-selected="true">
                                    <i class="fa-solid fa-list-ul text-primary me-2"></i>Dados Técnicos
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-secondary fw-medium" id="documentacao-tab" data-bs-toggle="tab" data-bs-target="#documentacao" type="button" role="tab" aria-controls="documentacao" aria-selected="false">
                                    <i class="fa-solid fa-file-contract text-success me-2"></i>Documentação e Garantias
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="detalhesTabsContent">

                            <!-- ====================== ABA 1: DADOS TÉCNICOS ====================== -->
                            <div class="tab-pane fade show active" id="dados" role="tabpanel" aria-labelledby="dados-tab">

                                <!-- 1. Identificação Técnica -->
                                <div class="mb-5">
                                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-2">
                                        <i class="fa-solid fa-microchip text-secondary me-2"></i>1. Identificação Técnica
                                    </h5>
                                    <div class="row g-4">
                                        <div class="col-md-3">
                                            <label class="form-label fw-medium text-muted">Código Interno</label>
                                            <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($equipamento->codigo_interno ?? 'N/A') ?></p>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label fw-medium text-muted">Designação do Equipamento</label>
                                            <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($equipamento->designacao ?? 'N/A') ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-muted">Categoria / Grupo</label>
                                            <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($equipamento->categoria ?: 'N/A') ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-muted">Marca</label>
                                            <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($equipamento->marca ?: 'N/A') ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-muted">Modelo</label>
                                            <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($equipamento->modelo ?: 'N/A') ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-muted">Número de Série</label>
                                            <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($equipamento->num_serie ?: 'N/A') ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. Dados de Aquisição e Estado -->
                                <div class="mb-5">
                                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                        <i class="fa-solid fa-cart-shopping text-secondary me-2"></i>2. Dados de Aquisição e Estado
                                    </h5>
                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-muted">Fabricante</label>
                                            <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($equipamento->fabricante ?: 'N/A') ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-muted">Data de Aquisição</label>
                                            <p class="fw-medium text-dark mb-0"><?= !empty($equipamento->data_aquisicao) ? date('d-m-Y', strtotime($equipamento->data_aquisicao)) : 'N/A' ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-muted">Tipo de Entrada</label>
                                            <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($equipamento->tipo_entrada ?: 'N/A') ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-muted">Ano de Fabrico</label>
                                            <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($equipamento->ano_fabrico ?: 'N/A') ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-muted">Custo de Aquisição (€)</label>
                                            <p class="fw-medium text-dark mb-0"><?= ($equipamento->custo_aquisicao !== null && $equipamento->custo_aquisicao !== '') ? number_format((float)$equipamento->custo_aquisicao, 2, ',', ' ') . ' €' : 'N/A' ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-muted">Estado Atual</label>
                                            <p class="mb-0"><span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2"><?= htmlspecialchars($equipamento->estado ?? 'Indefinido') ?></span></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. Classificação Clínica e Localização -->
                                <div class="mb-5">
                                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                        <i class="fa-solid fa-location-dot text-secondary me-2"></i>3. Classificação Clínica e Localização
                                    </h5>
                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-muted">Criticidade Clínica</label>
                                            <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($equipamento->criticidade ?? 'N/A') ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-muted">Serviço / Departamento</label>
                                            <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($equipamento->localizacao_servico ?: ($equipamento->servico ?? 'N/A')) ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-muted">Sala / Gabinete / Box</label>
                                            <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($equipamento->localizacao_sala ?: ($equipamento->sala ?? 'N/A')) ?></p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-medium text-muted">Observações Iniciais</label>
                                            <div class="p-3 bg-light rounded border text-dark"><?= !empty($equipamento->observacoes) ? nl2br(htmlspecialchars($equipamento->observacoes)) : '<span class="text-muted">Sem observações.</span>' ?></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 4. Componentes e Acessórios Associados -->
                                <div class="mb-5">
                                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-5">
                                        <i class="fa-solid fa-sitemap text-info me-2"></i>4. Componentes e Acessórios Associados
                                    </h5>
                                    <div class="table-responsive bg-white border rounded shadow-sm">
                                        <table class="table align-middle mb-0">
                                            <thead>
                                                <tr class="text-muted small">
                                                    <th class="py-2 px-3 border-0 bg-light">Código Interno</th>
                                                    <th class="py-2 border-0 bg-light">Designação</th>
                                                    <th class="py-2 pe-3 border-0 bg-light">Estado Atual</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($componentes)): ?>
                                                    <tr><td colspan="3" class="text-center text-muted py-4">Nenhum componente vinculado a esta unidade.</td></tr>
                                                <?php else: ?>
                                                    <?php foreach ($componentes as $comp): ?>
                                                        <tr>
                                                            <td class="px-3 font-monospace fw-bold text-dark"><?= htmlspecialchars($comp->codigo_interno) ?></td>
                                                            <td class="fw-medium text-dark"><?= htmlspecialchars($comp->designacao) ?></td>
                                                            <td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1"><?= htmlspecialchars($comp->estado) ?></span></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- 5. Consumíveis e Material Compatível -->
                                <div class="mb-4">
                                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                        <i class="fa-solid fa-box-open text-warning me-2"></i>5. Consumíveis e Material Compatível
                                    </h5>
                                    <div class="table-responsive bg-white border rounded shadow-sm">
                                        <table class="table align-middle mb-0">
                                            <thead>
                                                <tr class="text-muted small">
                                                    <th class="py-2 px-3 border-0 bg-light">Designação do Material</th>
                                                    <th class="py-2 border-0 bg-light">Categoria</th>
                                                    <th class="py-2 pe-3 border-0 bg-light">Frequência</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($consumiveis)): ?>
                                                    <tr><td colspan="3" class="text-center text-muted py-4">Nenhum consumível associado.</td></tr>
                                                <?php else: ?>
                                                    <?php foreach ($consumiveis as $cons): ?>
                                                        <tr>
                                                            <td class="px-3 fw-medium text-dark"><?= htmlspecialchars($cons->designacao) ?></td>
                                                            <td class="text-muted"><?= htmlspecialchars($cons->categoria) ?></td>
                                                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($cons->frequencia) ?></span></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                            <!-- ============ ABA 2: DOCUMENTAÇÃO E GARANTIAS ============ -->
                            <div class="tab-pane fade" id="documentacao" role="tabpanel" aria-labelledby="documentacao-tab">

                                <!-- 6. Garantias e Contratos -->
                                <div class="mb-5">
                                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-2">
                                        <i class="fa-solid fa-shield-halved text-success me-2"></i>6. Garantias e Contratos
                                    </h5>
                                    <div class="row g-4">
                                        <div class="col-md-3">
                                            <label class="form-label fw-medium text-muted">Início da Garantia</label>
                                            <p class="fw-medium text-dark mb-0"><?= !empty($equipamento->inicio_garantia) ? date('d-m-Y', strtotime($equipamento->inicio_garantia)) : 'N/A' ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-medium text-muted">Fim da Garantia</label>
                                            <p class="fw-medium text-dark mb-0"><?= !empty($equipamento->fim_garantia) ? date('d-m-Y', strtotime($equipamento->fim_garantia)) : 'N/A' ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-medium text-muted">Contrato de Manutenção</label>
                                            <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($equipamento->contrato_manutencao ?: 'N/A') ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-medium text-muted">Periodicidade</label>
                                            <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($equipamento->periodicidade ?: 'N/A') ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium text-muted">Entidade Responsável</label>
                                            <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($equipamento->entidade_responsavel ?? 'N/A') ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- 7. Documentação Associada -->
                                <div class="mb-4">
                                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-5">
                                        <i class="fa-solid fa-folder-open text-primary me-2"></i>7. Documentação Associada
                                    </h5>
                                    <div class="table-responsive bg-white border rounded shadow-sm">
                                        <table class="table align-middle mb-0">
                                            <thead>
                                                <tr class="text-muted small">
                                                    <th class="py-2 px-3 border-0 bg-light">Categoria</th>
                                                    <th class="py-2 border-0 bg-light">Título / Ficheiro</th>
                                                    <th class="py-2 pe-3 border-0 bg-light">Validade</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($documentos)): ?>
                                                    <tr><td colspan="3" class="text-center text-muted py-4">Nenhum documento anexado a este equipamento.</td></tr>
                                                <?php else: ?>
                                                    <?php foreach ($documentos as $doc): ?>
                                                        <tr>
                                                            <td class="px-3 text-muted"><?= htmlspecialchars($doc->categoria ?? 'N/A') ?></td>
                                                            <td>
                                                                <span class="d-block fw-medium text-dark"><?= htmlspecialchars($doc->titulo ?? 'N/A') ?></span>
                                                                <?php if (!empty($doc->nome_ficheiro)): ?>
                                                                    <span class="badge bg-secondary bg-opacity-10 text-dark border px-2 py-1 mt-1"><i class="fa-solid fa-file-pdf text-danger me-1"></i> <?= htmlspecialchars($doc->nome_ficheiro) ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-muted"><?= !empty($doc->data_validade) ? date('d-m-Y', strtotime($doc->data_validade)) : 'N/A' ?></td>
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
                </div>

            <?php endif; ?>
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

<?php include '../includes/footer.php'; ?>