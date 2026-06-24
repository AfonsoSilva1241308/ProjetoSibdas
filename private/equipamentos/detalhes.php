<?php
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../../config/config.php';

// Ficha 14: todos os perfis podem consultar detalhes do equipamento.
// Só administrador e técnico podem editar.
bloquear_se_nao_tiver_perfil(['administrador', 'profissional_saude', 'tecnico']);

$link_voltar = 'lista.php';

if (!function_exists('valorRaw')) {
    function valorRaw($obj, array $campos, $default = null) {
        foreach ($campos as $campo) {
            if (is_object($obj) && property_exists($obj, $campo) && $obj->$campo !== null && $obj->$campo !== '') {
                return $obj->$campo;
            }
        }
        return $default;
    }
}

if (!function_exists('campo')) {
    function campo($valor, $default = '—') {
        if ($valor === null || $valor === '') {
            return h($default);
        }
        return h($valor);
    }
}

if (!function_exists('formatarData')) {
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
}

if (!function_exists('formatarMoeda')) {
    function formatarMoeda($valor) {
        if ($valor === null || $valor === '') {
            return '—';
        }

        return number_format((float)$valor, 2, ',', '.') . ' €';
    }
}

if (!function_exists('classeEstado')) {
    function classeEstado($estado) {
        $estadoLower = mb_strtolower((string)$estado, 'UTF-8');

        if (strpos($estadoLower, 'inativo') !== false || strpos($estadoLower, 'avaria') !== false || strpos($estadoLower, 'abatido') !== false) {
            return 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
        }

        if (strpos($estadoLower, 'manuten') !== false || strpos($estadoLower, 'calibra') !== false || strpos($estadoLower, 'aguarda') !== false) {
            return 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
        }

        if (strpos($estadoLower, 'ativo') !== false) {
            return 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
        }

        return 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25';
    }
}

if (!function_exists('garantiaEstaValida')) {
    function garantiaEstaValida($fimGarantia) {
        if ($fimGarantia === null || $fimGarantia === '' || $fimGarantia === '0000-00-00') {
            return false;
        }

        try {
            return new DateTime($fimGarantia) >= new DateTime('today');
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('caminhoDocumento')) {
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

        return BASE_URL . '/uploads/documentos/' . rawurlencode($ficheiro);
    }
}

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
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        SELECT
            e.*,
            l.edificio AS localizacao_edificio,
            l.piso AS localizacao_piso,
            l.servico AS localizacao_servico,
            l.sala AS localizacao_sala
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

    $stmt = $ligacao->prepare("
        SELECT id, codigo_interno, designacao, estado
        FROM equipamento
        WHERE equipamento_pai_id = :id
        ORDER BY designacao ASC
    ");
    $stmt->execute([':id' => $idEquipamento]);
    $componentes = $stmt->fetchAll(PDO::FETCH_OBJ);

    $stmt = $ligacao->prepare("
        SELECT id, titulo, categoria, nome_ficheiro, data_validade
        FROM documento
        WHERE equipamento_id = :id
        ORDER BY id DESC
    ");
    $stmt->execute([':id' => $idEquipamento]);
    $documentos = $stmt->fetchAll(PDO::FETCH_OBJ);

    $stmt = $ligacao->prepare("
        SELECT id, designacao, categoria, frequencia
        FROM consumivel
        WHERE equipamento_id = :id
        ORDER BY designacao ASC
    ");
    $stmt->execute([':id' => $idEquipamento]);
    $consumiveis = $stmt->fetchAll(PDO::FETCH_OBJ);

    $stmt = $ligacao->prepare("
        SELECT gc.*, f.nome AS fornecedor_nome
        FROM garantia_contrato gc
        LEFT JOIN fornecedor f ON gc.entidade_responsavel_id = f.id
        WHERE gc.equipamento_id = :id
        ORDER BY gc.data_fim DESC
        LIMIT 1
    ");
    $stmt->execute([':id' => $idEquipamento]);
    $garantiaContrato = $stmt->fetch(PDO::FETCH_OBJ) ?: null;

} catch (PDOException $err) {
    die('Erro ao carregar o equipamento: ' . h($err->getMessage()));
}

$codigoInterno = valorRaw($equipamento, ['codigo_interno'], '—');
$designacao = valorRaw($equipamento, ['designacao'], 'Equipamento');
$marca = valorRaw($equipamento, ['marca'], '—');
$modelo = valorRaw($equipamento, ['modelo'], '—');
$numSerie = valorRaw($equipamento, ['num_serie'], '—');
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
$inicioGarantia = valorRaw($garantiaBase, ['inicio_garantia', 'data_inicio'], null);
$fimGarantia = valorRaw($garantiaBase, ['fim_garantia', 'data_fim'], null);
$contratoManutencao = valorRaw($garantiaBase, ['contrato_manutencao'], '—');
$entidadeResponsavel = valorRaw($garantiaBase, ['fornecedor_nome', 'entidade_responsavel'], '—');
$periodicidade = valorRaw($garantiaBase, ['periodicidade'], '—');
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="d-flex min-vh-100">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="flex-grow-1 p-4 p-md-5 bg-light w-100">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="d-flex justify-content-center align-items-start mt-4 mb-5">
            <div class="card w-100 shadow-sm rounded border-top border-primary border-4" style="max-width: 1200px;">
                <div class="card-body p-4 p-md-5">

                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                        <div>
                            <h2 class="text-primary mb-1 fw-bold">
                                <i class="fa-solid fa-file-medical me-2"></i>Ficha Técnica do Equipamento
                            </h2>
                            <p class="text-muted mb-0">
                                Código de Inventário:
                                <span class="badge bg-dark fs-6 font-monospace"><?= campo($codigoInterno) ?></span>
                            </p>
                        </div>

                        <?php if (tem_perfil(['administrador', 'tecnico'])): ?>
                            <div class="d-flex gap-2">
                                <a href="editar.php?id_equipamento=<?= urlencode(aes_encrypt($equipamento->id)) ?>" class="btn btn-outline-warning fw-medium">
                                    <i class="fa-regular fa-pen-to-square me-2"></i>Editar
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <ul class="nav nav-tabs mb-4" id="equipamentoTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab" aria-selected="true">
                                <i class="fa-solid fa-list-ul me-2"></i>Dados Técnicos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-medium" id="documentacao-tab" data-bs-toggle="tab" data-bs-target="#documentacao" type="button" role="tab" aria-selected="false">
                                <i class="fa-solid fa-file-contract me-2"></i>Documentação e Garantias
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="equipamentoTabsContent">
                        <div class="tab-pane fade show active" id="dados" role="tabpanel" aria-labelledby="dados-tab">

                            <section class="mb-5">
                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fa-solid fa-microchip text-primary me-2"></i>1. Identificação Técnica
                                </h5>

                                <div class="row g-4">
                                    <div class="col-md-3">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Código Interno</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo($codigoInterno) ?></p>
                                    </div>

                                    <div class="col-md-5">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Designação</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo($designacao) ?></p>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Categoria</span>
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
                            </section>

                            <section class="mb-5">
                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fa-solid fa-cart-shopping text-secondary me-2"></i>2. Dados de Aquisição e Estado
                                </h5>

                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Fabricante</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo($fabricante) ?></p>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Data de Aquisição</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= formatarData(valorRaw($equipamento, ['data_aquisicao'], null)) ?></p>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Tipo de Entrada</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo(valorRaw($equipamento, ['tipo_entrada'], '—')) ?></p>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Ano de Fabrico</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo(valorRaw($equipamento, ['ano_fabrico'], '—')) ?></p>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Custo de Aquisição</span>
                                        <p class="fs-6 fw-bold text-dark mb-0"><?= formatarMoeda(valorRaw($equipamento, ['custo_aquisicao'], null)) ?></p>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Estado Atual</span>
                                        <span class="badge <?= classeEstado($estado) ?> px-3 py-2"><?= campo($estado) ?></span>
                                    </div>
                                </div>
                            </section>

                            <section class="mb-5">
                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fa-solid fa-location-dot text-secondary me-2"></i>3. Classificação Clínica e Localização
                                </h5>

                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Criticidade Clínica</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo($criticidade) ?></p>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Serviço / Departamento</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo($servico) ?></p>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Sala / Box</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo($sala) ?></p>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Edifício</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo($edificio) ?></p>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Piso</span>
                                        <p class="fs-6 fw-medium text-dark mb-0"><?= campo($piso) ?></p>
                                    </div>

                                    <div class="col-12">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Observações</span>
                                        <div class="p-3 bg-light rounded border text-dark"><?= nl2br(campo($observacoes)) ?></div>
                                    </div>
                                </div>
                            </section>

                            <section class="mb-5">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold text-dark mb-0">
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
                                                <th class="py-2 px-4 border-0">Código</th>
                                                <th class="py-2 border-0">Designação</th>
                                                <th class="py-2 border-0 text-center">Estado</th>
                                                <th class="py-2 px-4 border-0 text-end">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($componentes)): ?>
                                                <tr>
                                                    <td colspan="4" class="py-4 px-4 text-center text-muted fst-italic">Nenhum componente associado.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($componentes as $componente): ?>
                                                    <?php $estadoComponente = valorRaw($componente, ['estado'], '—'); ?>
                                                    <tr>
                                                        <td class="py-3 px-4 font-monospace text-muted small"><?= campo(valorRaw($componente, ['codigo_interno'], '—')) ?></td>
                                                        <td class="py-3 fw-medium text-dark"><?= campo(valorRaw($componente, ['designacao'], '—')) ?></td>
                                                        <td class="py-3 text-center"><span class="badge <?= classeEstado($estadoComponente) ?> rounded-pill px-2"><?= campo($estadoComponente) ?></span></td>
                                                        <td class="py-3 px-4 text-end">
                                                            <a href="detalhes.php?id_equipamento=<?= urlencode(aes_encrypt($componente->id)) ?>" class="btn btn-sm btn-light border px-2" title="Ver componente">
                                                                <i class="fa-solid fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </section>

                            <section class="mb-4">
                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fa-solid fa-box-open text-warning me-2"></i>5. Consumíveis e Material Compatível
                                </h5>

                                <div class="table-responsive border rounded bg-white shadow-sm">
                                    <table class="table align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr class="text-muted small">
                                                <th class="py-2 px-4 border-0">Designação</th>
                                                <th class="py-2 border-0">Categoria</th>
                                                <th class="py-2 px-4 border-0 text-center">Frequência</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($consumiveis)): ?>
                                                <tr>
                                                    <td colspan="3" class="py-4 px-4 text-center text-muted fst-italic">Nenhum consumível associado.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($consumiveis as $consumivel): ?>
                                                    <tr>
                                                        <td class="py-3 px-4 fw-medium text-dark"><?= campo($consumivel->designacao ?? '—') ?></td>
                                                        <td class="py-3 text-muted"><?= campo($consumivel->categoria ?? '—') ?></td>
                                                        <td class="py-3 px-4 text-center"><span class="badge bg-light text-dark border"><?= campo($consumivel->frequencia ?? '—') ?></span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>

                        <div class="tab-pane fade" id="documentacao" role="tabpanel" aria-labelledby="documentacao-tab">

                            <section class="card border-0 shadow-sm p-4 bg-white mb-4">
                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fa-solid fa-shield-halved text-success me-2"></i>6. Garantias e Contratos
                                </h5>

                                <div class="row g-4">
                                    <div class="col-md-4 border-end">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Período de Garantia</span>
                                        <p class="m-0 fw-medium text-dark"><?= formatarData($inicioGarantia) ?> a <?= formatarData($fimGarantia) ?></p>

                                        <?php if (garantiaEstaValida($fimGarantia)): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill mt-2 px-3 py-1">Garantia Válida</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill mt-2 px-3 py-1">Sem garantia válida registada</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-md-4 border-end">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Contrato de Manutenção</span>
                                        <p class="m-0 fw-bold text-dark"><?= campo($contratoManutencao) ?></p>
                                        <small class="text-muted d-block mt-1">Periodicidade: <strong><?= campo($periodicidade) ?></strong></small>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Entidade Responsável</span>
                                        <p class="m-0 fw-medium text-dark"><?= campo($entidadeResponsavel) ?></p>
                                    </div>
                                </div>
                            </section>

                            <section class="card border-0 shadow-sm p-4 bg-white mb-2">
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
                                                <th class="py-3 px-4 border-0">Categoria</th>
                                                <th class="py-3 px-4 border-0">Título / Ficheiro</th>
                                                <th class="py-3 px-4 border-0 text-center">Validade</th>
                                                <th class="py-3 px-4 border-0 text-end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($documentos)): ?>
                                                <tr>
                                                    <td colspan="4" class="py-4 px-4 text-center text-muted fst-italic">Nenhum documento associado a este equipamento.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($documentos as $documento): ?>
                                                    <?php $ficheiro = valorRaw($documento, ['nome_ficheiro'], ''); ?>
                                                    <tr>
                                                        <td class="py-3 px-4 text-muted"><?= campo($documento->categoria ?? '—') ?></td>
                                                        <td class="py-3 px-4">
                                                            <span class="d-block fw-medium text-dark"><?= campo($documento->titulo ?? '—') ?></span>
                                                            <?php if ($ficheiro): ?>
                                                                <small class="text-secondary"><i class="fa-solid fa-file-pdf text-danger me-1"></i><?= campo($ficheiro) ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="py-3 px-4 text-center"><?= formatarData($documento->data_validade ?? null) ?></td>
                                                        <td class="py-3 px-4 text-end">
                                                            <?php if ($ficheiro): ?>
                                                                <a href="<?= caminhoDocumento($ficheiro) ?>" download class="btn btn-sm btn-light border px-2 rounded" title="Transferir ficheiro">
                                                                    <i class="fa-solid fa-download text-secondary"></i>
                                                                </a>
                                                            <?php else: ?>
                                                                <button class="btn btn-sm btn-light border px-2 rounded" disabled>
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
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>