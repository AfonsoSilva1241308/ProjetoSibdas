<?php
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../../config/config.php';
redirect_if_not_logged();

$link_voltar = "lista.php";

// ===============================
// Funções auxiliares para a página
// ===============================
function h($valor) {
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function valorRaw($obj, array $campos, $default = null) {
    foreach ($campos as $campo) {
        if (is_object($obj) && property_exists($obj, $campo) && $obj->$campo !== null && $obj->$campo !== '') {
            return $obj->$campo;
        }
    }
    return $default;
}

function campo($valor, $default = '—') {
    if ($valor === null || $valor === '') {
        return h($default);
    }
    return h($valor);
}

function formatarData($data) {
    if ($data === null || $data === '' || $data === '0000-00-00') {
        return '—';
    }

    try {
        return (new DateTime($data))->format('d/m/Y');
    } catch (Exception $e) {
        return h($data);
    }
}

function formatarMoeda($valor) {
    if ($valor === null || $valor === '') {
        return '—';
    }

    return number_format((float)$valor, 2, ',', '.') . ' €';
}

function classeEstado($estado) {
    $estadoLower = strtolower((string)$estado);

    if (strpos($estadoLower, 'ativo') !== false) {
        return 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
    }

    if (strpos($estadoLower, 'manuten') !== false || strpos($estadoLower, 'calibra') !== false) {
        return 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
    }

    if (strpos($estadoLower, 'inativo') !== false || strpos($estadoLower, 'avaria') !== false || strpos($estadoLower, 'abatido') !== false) {
        return 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
    }

    return 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25';
}

function garantiaEstaValida($fimGarantia) {
    if ($fimGarantia === null || $fimGarantia === '' || $fimGarantia === '0000-00-00') {
        return false;
    }

    try {
        $fim = new DateTime($fimGarantia);
        $hoje = new DateTime('today');
        return $fim >= $hoje;
    } catch (Exception $e) {
        return false;
    }
}

function caminhoDocumento($ficheiro) {
    if ($ficheiro === null || $ficheiro === '') {
        return '#';
    }

    $ficheiro = (string)$ficheiro;

    if (preg_match('/^https?:\/\//i', $ficheiro)) {
        return h($ficheiro);
    }

    if (strpos($ficheiro, '../') === 0 || strpos($ficheiro, '/') === 0 || strpos($ficheiro, 'uploads/') !== false) {
        return h($ficheiro);
    }

    return '../../uploads/documentos/' . h($ficheiro);
}

// ===============================
// Obter ID vindo da lista.php
// Aceita id_equipamento encriptado e também id normal, para compatibilidade.
// ===============================
$idParam = $_GET['id_equipamento'] ?? $_GET['id'] ?? null;

if (!$idParam) {
    header('Location: lista.php');
    exit;
}

if (is_numeric($idParam)) {
    $idEquipamento = (int)$idParam;
} else {
    $idEquipamento = aes_decrypt(urldecode($idParam));
}

if (!$idEquipamento || !is_numeric($idEquipamento)) {
    header('Location: lista.php');
    exit;
}

$idEquipamento = (int)$idEquipamento;

try {
    $porta = defined('MYSQL_PORT') ? MYSQL_PORT : 3306;

    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . $porta . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Equipamento principal + localização
    $stmt = $ligacao->prepare("\n        SELECT \n            e.*,\n            l.edificio AS localizacao_edificio,\n            l.piso AS localizacao_piso,\n            l.servico AS localizacao_servico,\n            l.sala AS localizacao_sala\n        FROM equipamento e\n        LEFT JOIN localizacao l ON e.localizacao_id = l.id\n        WHERE e.id = :id\n        LIMIT 1\n    ");
    $stmt->execute([':id' => $idEquipamento]);
    $equipamento = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$equipamento) {
        header('Location: lista.php');
        exit;
    }

    // Componentes associados: na tua BD, os componentes parecem estar na própria tabela equipamento.
    $stmt = $ligacao->prepare("\n        SELECT *\n        FROM equipamento\n        WHERE equipamento_pai_id = :id\n        ORDER BY designacao ASC\n    ");
    $stmt->execute([':id' => $idEquipamento]);
    $componentes = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Garantia/contrato: se tiveres tabela garantia_contrato, tenta ir buscar; senão usa os campos da tabela equipamento.
    $garantiaContrato = null;
    try {
        $stmt = $ligacao->prepare("SELECT * FROM garantia_contrato WHERE equipamento_id = :id LIMIT 1");
        $stmt->execute([':id' => $idEquipamento]);
        $garantiaContrato = $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    } catch (PDOException $e) {
        try {
            $stmt = $ligacao->prepare("SELECT * FROM garantia_contrato WHERE id_equipamento = :id LIMIT 1");
            $stmt->execute([':id' => $idEquipamento]);
            $garantiaContrato = $stmt->fetch(PDO::FETCH_OBJ) ?: null;
        } catch (PDOException $e2) {
            $garantiaContrato = null;
        }
    }

    // Documentos associados: tenta encontrar documentos ligados ao equipamento.
    $documentos = [];
    try {
        $stmt = $ligacao->prepare("SELECT * FROM documento WHERE equipamento_id = :id ORDER BY id DESC");
        $stmt->execute([':id' => $idEquipamento]);
        $documentos = $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        try {
            $stmt = $ligacao->prepare("SELECT * FROM documento WHERE id_equipamento = :id ORDER BY id DESC");
            $stmt->execute([':id' => $idEquipamento]);
            $documentos = $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e2) {
            $documentos = [];
        }
    }

    // Consumíveis: isto só mostra resultados se a tua BD tiver ligação por equipamento_id/id_equipamento.
    $consumiveis = [];
    try {
        $stmt = $ligacao->prepare("SELECT * FROM consumivel WHERE equipamento_id = :id ORDER BY designacao ASC");
        $stmt->execute([':id' => $idEquipamento]);
        $consumiveis = $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        try {
            $stmt = $ligacao->prepare("SELECT * FROM consumivel WHERE id_equipamento = :id ORDER BY designacao ASC");
            $stmt->execute([':id' => $idEquipamento]);
            $consumiveis = $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e2) {
            $consumiveis = [];
        }
    }

} catch (PDOException $err) {
    die('Erro ao ligar à base de dados ou ao carregar o equipamento: ' . h($err->getMessage()));
}

$codigoInterno = valorRaw($equipamento, ['codigo_interno'], '—');
$designacao = valorRaw($equipamento, ['designacao'], 'Equipamento');
$marca = valorRaw($equipamento, ['marca'], '—');
$modelo = valorRaw($equipamento, ['modelo'], '—');
$numSerie = valorRaw($equipamento, ['num_serie', 'numero_serie'], '—');
$categoria = valorRaw($equipamento, ['categoria'], '—');
$fabricante = valorRaw($equipamento, ['fabricante'], $marca);
$estado = valorRaw($equipamento, ['estado'], '—');
$criticidade = valorRaw($equipamento, ['criticidade'], '—');
$servico = valorRaw($equipamento, ['localizacao_servico', 'servico'], '—');
$sala = valorRaw($equipamento, ['localizacao_sala', 'sala'], '—');
$piso = valorRaw($equipamento, ['localizacao_piso'], null);
$edificio = valorRaw($equipamento, ['localizacao_edificio'], null);
$observacoes = valorRaw($equipamento, ['observacoes'], 'Sem observações registadas.');

$garantiaBase = $garantiaContrato ?: $equipamento;
$inicioGarantia = valorRaw($garantiaBase, ['inicio_garantia', 'data_inicio', 'inicio'], null);
$fimGarantia = valorRaw($garantiaBase, ['fim_garantia', 'data_fim', 'fim'], null);
$contratoManutencao = valorRaw($garantiaBase, ['contrato_manutencao', 'contrato', 'tem_contrato'], null);
$entidadeResponsavel = valorRaw($garantiaBase, ['entidade_responsavel', 'entidade', 'fornecedor'], $fabricante);
$periodicidade = valorRaw($garantiaBase, ['periodicidade', 'periodicidade_revisao'], '—');
$observacoesContrato = valorRaw($garantiaBase, ['observacoes', 'observacoes_contrato', 'notas'], 'Sem observações de contrato registadas.');
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="d-flex vh-100">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100 bg-light">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="d-flex justify-content-center align-items-start mt-4 mb-5">
            <div class="card w-100 shadow-sm rounded border-top border-secondary border-4 h-auto" style="max-width: 1200px;">
                <div class="card-body p-4 p-md-5">

                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                        <div>
                            <h2 class="text-dark mb-1">
                                <strong><i class="fa-solid fa-file-medical text-secondary me-2"></i> Ficha Técnica do Equipamento</strong>
                            </h2>
                            <p class="text-muted m-0">
                                Código de Inventário Hospitalar:
                                <span class="badge bg-dark fs-6 font-monospace"><?= campo($codigoInterno) ?></span>
                            </p>
                        </div>

                        <a href="editar.php?id_equipamento=<?= urlencode(aes_encrypt($equipamento->id)) ?>" class="btn btn-outline-warning fw-medium">
                            <i class="fa-regular fa-pen-to-square me-2"></i>Editar
                        </a>
                    </div>

                    <ul class="nav nav-tabs mb-4" id="equipamentoTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-dark fw-bold border-bottom-0" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab" aria-selected="true">
                                <i class="fa-solid fa-list-ul text-primary me-2"></i>Dados Técnicos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-secondary fw-medium" id="documentacao-tab" data-bs-toggle="tab" data-bs-target="#documentacao" type="button" role="tab" aria-selected="false">
                                <i class="fa-solid fa-file-contract text-success me-2"></i>Documentação e Garantias
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="equipamentoTabsContent">

                        <div class="tab-pane fade show active" id="dados" role="tabpanel" aria-labelledby="dados-tab">

                            <div class="mb-5 mt-2">
                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fa-solid fa-microchip text-primary me-2"></i>1. Identificação Técnica
                                </h5>

                                <div class="row g-4">
                                    <div class="col-md-3">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Código Interno</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo($codigoInterno) ?></p>
                                    </div>

                                    <div class="col-md-5">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Designação do Equipamento</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo($designacao) ?></p>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Categoria / Grupo</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo($categoria) ?></p>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Marca</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo($marca) ?></p>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Modelo</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo($modelo) ?></p>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Número de Série</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo($numSerie) ?></p>
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

                            <div class="mb-5">
                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fa-solid fa-cart-shopping text-secondary me-2"></i>2. Dados de Aquisição e Estado
                                </h5>

                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <span class="d-block text-muted small fw-bold text-uppercase mb-1">Fabricante</span>
                                        <span class="text-dark fw-medium fs-6"><?= campo($fabricante) ?></span>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small fw-bold text-uppercase mb-1">Data de Aquisição</span>
                                        <span class="text-dark fw-medium fs-6"><?= formatarData(valorRaw($equipamento, ['data_aquisicao'], null)) ?></span>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small fw-bold text-uppercase mb-1">Tipo de Entrada</span>
                                        <span class="text-dark fw-medium fs-6"><?= campo(valorRaw($equipamento, ['tipo_entrada'], '—')) ?></span>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small fw-bold text-uppercase mb-1">Ano de Fabrico</span>
                                        <span class="text-dark fw-medium fs-6"><?= campo(valorRaw($equipamento, ['ano_fabrico'], '—')) ?></span>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small fw-bold text-uppercase mb-1">Custo de Aquisição</span>
                                        <span class="text-dark fw-bold fs-6"><?= formatarMoeda(valorRaw($equipamento, ['custo_aquisicao'], null)) ?></span>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small fw-bold text-uppercase mb-1">Estado Atual</span>
                                        <span class="badge <?= classeEstado($estado) ?> px-3 py-2">
                                            <i class="fa-solid fa-circle-check me-1"></i><?= campo($estado) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-5">
                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fa-solid fa-location-dot text-secondary me-2"></i>3. Classificação Clínica e Localização
                                </h5>

                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <span class="d-block text-muted small fw-bold text-uppercase mb-1">Criticidade Clínica</span>
                                        <span class="text-dark fw-medium fs-6"><?= campo($criticidade) ?></span>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small fw-bold text-uppercase mb-1">Serviço / Departamento</span>
                                        <span class="text-dark fw-medium fs-6"><?= campo($servico) ?></span>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small fw-bold text-uppercase mb-1">Sala / Gabinete / Box</span>
                                        <span class="text-dark fw-medium fs-6"><?= campo($sala) ?></span>
                                    </div>

                                    <?php if ($edificio || $piso): ?>
                                        <div class="col-md-6">
                                            <span class="d-block text-muted small fw-bold text-uppercase mb-1">Edifício</span>
                                            <span class="text-dark fw-medium fs-6"><?= campo($edificio) ?></span>
                                        </div>

                                        <div class="col-md-6">
                                            <span class="d-block text-muted small fw-bold text-uppercase mb-1">Piso</span>
                                            <span class="text-dark fw-medium fs-6"><?= campo($piso) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="col-12">
                                        <span class="d-block text-muted small fw-bold text-uppercase mb-1">Observações / Notas Técnicas</span>
                                        <p class="text-dark bg-light p-3 rounded shadow-sm border mb-0"><?= nl2br(campo($observacoes)) ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-5">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold text-dark m-0">
                                        <i class="fa-solid fa-sitemap text-info me-2"></i>4. Componentes e Acessórios Associados
                                    </h5>

                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1">
                                        <?= (valorRaw($equipamento, ['is_componente'], 'nao') === 'sim') ? 'Componente' : 'Equipamento Principal' ?>
                                    </span>
                                </div>

                                <div class="table-responsive border rounded bg-white shadow-sm">
                                    <table class="table align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr class="text-muted small">
                                                <th class="py-2 px-4 border-0">Cód. Componente</th>
                                                <th class="py-2 border-0">Designação</th>
                                                <th class="py-2 border-0 text-center">Estado Atual</th>
                                                <th class="py-2 px-4 border-0 text-end">Ação</th>
                                            </tr>
                                        </thead>

                                        <tbody class="border-top-0">
                                            <?php if (empty($componentes)): ?>
                                                <tr>
                                                    <td colspan="4" class="py-4 px-4 text-center text-muted fst-italic">
                                                        Nenhum componente associado a este equipamento.
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($componentes as $componente): ?>
                                                    <?php $estadoComponente = valorRaw($componente, ['estado'], '—'); ?>
                                                    <tr>
                                                        <td class="py-3 px-4 font-monospace text-muted small"><?= campo(valorRaw($componente, ['codigo_interno'], '—')) ?></td>
                                                        <td class="py-3 fw-medium text-dark"><?= campo(valorRaw($componente, ['designacao'], '—')) ?></td>
                                                        <td class="py-3 text-center">
                                                            <span class="badge <?= classeEstado($estadoComponente) ?> rounded-pill px-2"><?= campo($estadoComponente) ?></span>
                                                        </td>
                                                        <td class="py-3 px-4 text-end">
                                                            <a href="detalhes.php?id_equipamento=<?= urlencode(aes_encrypt($componente->id)) ?>" class="btn btn-sm btn-light border px-2 text-secondary" title="Ver ficha do componente">
                                                                <i class="fa-solid fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fa-solid fa-box-open text-warning me-2"></i>5. Consumíveis e Material Compatível
                                </h5>

                                <div class="table-responsive border rounded bg-white shadow-sm mb-3">
                                    <table class="table align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr class="text-muted small">
                                                <th class="py-2 px-4 border-0" style="width: 50%;">Designação do Consumível</th>
                                                <th class="py-2 border-0" style="width: 25%;">Categoria</th>
                                                <th class="py-2 px-4 border-0 text-center" style="width: 25%;">Frequência de Troca</th>
                                            </tr>
                                        </thead>

                                        <tbody class="border-top-0">
                                            <?php if (empty($consumiveis)): ?>
                                                <tr>
                                                    <td colspan="3" class="py-4 px-4 text-center text-muted fst-italic">
                                                        Nenhum consumível associado a este equipamento.
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($consumiveis as $consumivel): ?>
                                                    <tr>
                                                        <td class="py-3 px-4 fw-medium text-dark">
                                                            <i class="fa-solid fa-box text-secondary me-2 opacity-50"></i><?= campo(valorRaw($consumivel, ['designacao', 'nome', 'descricao'], '—')) ?>
                                                        </td>
                                                        <td class="py-3 text-muted small"><?= campo(valorRaw($consumivel, ['categoria', 'tipo'], '—')) ?></td>
                                                        <td class="py-3 px-4 text-center">
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-3 py-1">
                                                                <?= campo(valorRaw($consumivel, ['frequencia_troca', 'periodicidade_troca', 'periodicidade'], '—')) ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <small class="text-muted fst-italic d-block">
                                    <i class="fa-solid fa-circle-info me-1"></i>O material de desgaste rápido não é inventariado individualmente. O seu fornecimento deve ser gerido através do economato/armazém.
                                </small>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="documentacao" role="tabpanel" aria-labelledby="documentacao-tab">

                            <div class="card border-0 shadow-sm p-4 bg-white mb-4">
                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fa-solid fa-shield-halved text-success me-2"></i>6. Garantias e Contratos
                                </h5>

                                <div class="row g-4">
                                    <div class="col-md-4 border-end">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Período de Garantia</span>
                                        <p class="m-0 fw-medium text-dark">
                                            <i class="fa-solid fa-calendar-days text-secondary me-1"></i>
                                            <?= formatarData($inicioGarantia) ?> a <?= formatarData($fimGarantia) ?>
                                        </p>

                                        <?php if (garantiaEstaValida($fimGarantia)): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill mt-2 px-3 py-1">Garantia Válida</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill mt-2 px-3 py-1">Sem garantia válida registada</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-md-4 border-end">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Contrato de Manutenção</span>
                                        <p class="m-0 fw-bold text-dark">
                                            <i class="fa-solid fa-file-signature text-primary me-1"></i>
                                            <?= campo($contratoManutencao ?: '—') ?>
                                        </p>
                                        <small class="text-muted d-block mt-1">Periodicidade de Revisão: <strong><?= campo($periodicidade) ?></strong></small>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Entidade Responsável</span>
                                        <p class="m-0 fw-medium text-dark">
                                            <i class="fa-solid fa-building text-secondary me-1"></i><?= campo($entidadeResponsavel) ?>
                                        </p>
                                    </div>

                                    <div class="col-12 mt-3 pt-3 border-top">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Observações do Contrato</span>
                                        <div class="p-3 bg-light rounded text-secondary small fst-italic">
                                            <i class="fa-solid fa-circle-info me-1 text-primary"></i><?= nl2br(campo($observacoesContrato)) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm p-4 bg-white mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                    <h5 class="fw-bold text-dark m-0">
                                        <i class="fa-solid fa-folder-open text-primary me-2"></i>7. Documentação Associada
                                    </h5>
                                    <span class="badge bg-dark rounded-pill px-3 py-2 font-monospace"><?= count($documentos) ?> documento(s)</span>
                                </div>

                                <div class="table-responsive border rounded bg-white shadow-sm">
                                    <table class="table align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr class="text-muted small">
                                                <th class="py-3 px-4 text-uppercase fw-bold border-0" style="width: 35%;">Tipo de Documento</th>
                                                <th class="py-3 px-4 text-uppercase fw-bold border-0" style="width: 35%;">Detalhes do Ficheiro</th>
                                                <th class="py-3 px-4 text-uppercase fw-bold border-0 text-center" style="width: 15%;">Estado</th>
                                                <th class="py-3 px-4 text-uppercase fw-bold border-0 text-end" style="width: 15%;">Ações</th>
                                            </tr>
                                        </thead>

                                        <tbody class="border-top-0">
                                            <?php if (empty($documentos)): ?>
                                                <tr>
                                                    <td colspan="4" class="py-4 px-4 text-center text-muted fst-italic">
                                                        Nenhum documento associado a este equipamento.
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($documentos as $documento): ?>
                                                    <?php
                                                        $tipoDocumento = valorRaw($documento, ['tipo_documento', 'tipo', 'categoria', 'titulo', 'designacao'], 'Documento');
                                                        $ficheiro = valorRaw($documento, ['nome_ficheiro', 'ficheiro', 'filename', 'caminho', 'path'], '');
                                                        $estadoDocumento = valorRaw($documento, ['estado', 'status'], 'Disponível');
                                                        $dataDocumento = valorRaw($documento, ['data_documento', 'data_submissao', 'criado_em', 'created_at'], null);
                                                        $submetidoPor = valorRaw($documento, ['submetido_por', 'autor', 'entidade', 'fornecedor'], '—');
                                                    ?>
                                                    <tr>
                                                        <td class="py-3 px-4">
                                                            <span class="d-block fw-bold text-dark">
                                                                <i class="fa-solid fa-file-lines text-primary me-2"></i><?= campo($tipoDocumento) ?>
                                                            </span>
                                                        </td>

                                                        <td class="px-4">
                                                            <span class="d-block fw-medium text-dark"><?= campo($ficheiro ?: 'Sem ficheiro') ?></span>
                                                            <small class="text-secondary d-block">Submetido em: <?= formatarData($dataDocumento) ?> | Por: <?= campo($submetidoPor) ?></small>
                                                        </td>

                                                        <td class="text-center px-4">
                                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1"><?= campo($estadoDocumento) ?></span>
                                                        </td>

                                                        <td class="text-end px-4">
                                                            <?php if ($ficheiro): ?>
                                                                <a href="<?= caminhoDocumento($ficheiro) ?>" download class="btn btn-sm btn-light border px-2 rounded" title="Transferir Ficheiro">
                                                                    <i class="fa-solid fa-download text-secondary"></i>
                                                                </a>
                                                            <?php else: ?>
                                                                <button class="btn btn-sm btn-light border px-2 rounded disabled" disabled>
                                                                    <i class="fa-solid fa-download text-muted"></i>
                                                                </button>
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
                </div>

<<<<<<< HEAD
                </div>
            </div>
=======
            <?php endif; ?>
>>>>>>> f01820d50daa5c9ffec404e8b2dfde321f1467c8
        </div>
    </div>
</div>

<<<<<<< HEAD
<?php include __DIR__ . '/../includes/footer.php'; ?>
=======
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
>>>>>>> f01820d50daa5c9ffec404e8b2dfde321f1467c8
