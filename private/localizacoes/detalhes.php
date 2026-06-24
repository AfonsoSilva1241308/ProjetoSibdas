<?php
// 1. Segurança sempre no topo
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../../config/config.php';
redirect_if_not_logged();

// 2. Pedir à navbar para mostrar apenas o botão Voltar
$link_voltar = "lista.php";

// ===============================
// Funções auxiliares
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

function classeEstado($estado) {
    $estadoLower = strtolower((string)$estado);

    if (strpos($estadoLower, 'ativo') !== false || strpos($estadoLower, 'operacional') !== false) {
        return 'bg-success bg-opacity-10 text-success rounded-pill px-2 py-1';
    }

    if (strpos($estadoLower, 'manuten') !== false || strpos($estadoLower, 'calibra') !== false) {
        return 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2 py-1';
    }

    if (strpos($estadoLower, 'inativo') !== false || strpos($estadoLower, 'avaria') !== false || strpos($estadoLower, 'abatido') !== false) {
        return 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-1';
    }

    return 'bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-1';
}

// ===============================
// Obter ID vindo da lista.php
// Aceita id_localizacao, localizacao_id ou id.
// Aceita ID normal ou ID encriptado.
// ===============================
$idParam = $_GET['id_localizacao'] ?? $_GET['localizacao_id'] ?? $_GET['id'] ?? null;

if (!$idParam) {
    header('Location: lista.php');
    exit;
}

if (is_numeric($idParam)) {
    $idLocalizacao = (int)$idParam;
} else {
    $idLocalizacao = aes_decrypt(urldecode($idParam));
}

if (!$idLocalizacao || !is_numeric($idLocalizacao)) {
    header('Location: lista.php');
    exit;
}

$idLocalizacao = (int)$idLocalizacao;

try {
    $porta = defined('MYSQL_PORT') ? MYSQL_PORT : 3306;

    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . $porta . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Localização principal
    $stmt = $ligacao->prepare("SELECT * FROM localizacao WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $idLocalizacao]);
    $localizacao = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$localizacao) {
        header('Location: lista.php');
        exit;
    }

    // Equipamentos alocados a esta localização
    $stmt = $ligacao->prepare("\n        SELECT 
            id,
            codigo_interno,
            designacao,
            marca,
            modelo,
            estado
        FROM equipamento
        WHERE localizacao_id = :id
        ORDER BY designacao ASC, codigo_interno ASC
    ");
    $stmt->execute([':id' => $idLocalizacao]);
    $equipamentos = $stmt->fetchAll(PDO::FETCH_OBJ);

} catch (PDOException $err) {
    die('Erro ao ligar à base de dados ou ao carregar a localização: ' . h($err->getMessage()));
}

// ===============================
// Valores dinâmicos da localização
// Estes nomes estão preparados para a tua tabela localizacao.
// Se alguma coluna não existir, aparece "—".
// ===============================
$edificio = valorRaw($localizacao, ['edificio', 'bloco', 'edificio_bloco'], '—');
$piso = valorRaw($localizacao, ['piso'], '—');
$servico = valorRaw($localizacao, ['servico', 'departamento'], '—');
$sala = valorRaw($localizacao, ['sala', 'gabinete'], '—');

$requisitos = valorRaw($localizacao, [
    'requisitos_eletricos',
    'requisitos_rede',
    'infraestrutura',
    'requisitos',
    'condicoes_especiais'
], 'Sem requisitos registados');

$capacidade = valorRaw($localizacao, [
    'capacidade_maxima',
    'capacidade',
    'lotacao',
    'lotacao_maxima',
    'capacidade_equipamentos'
], null);

$observacoes = valorRaw($localizacao, ['observacoes', 'notas', 'descricao'], 'Sem observações registadas.');
$totalEquipamentos = count($equipamentos);
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="d-flex vh-100">
    
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100 bg-light">
        
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow-sm rounded border-top border-secondary border-4" style="max-width: 1200px;">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                        <div>
                            <h2 class="text-dark mb-1">
                                <strong>
                                    <i class="fa-solid fa-location-dot text-secondary me-2"></i>
                                    Detalhes da Localização
                                </strong>
                            </h2>
                            <p class="text-muted mb-0">
                                <?= campo($servico) ?><?= $sala && $sala !== '—' ? ' · Sala ' . h($sala) : '' ?>
                            </p>
                        </div>
                    </div>
                    <hr class="mb-5">

                    <div class="row g-4 mb-5">
                        <div class="col-12">
                            <h5 class="text-secondary fw-bold mb-3">
                                <i class="fa-solid fa-building me-2"></i>Identificação do Espaço
                            </h5>
                        </div>

                        <div class="col-md-3">
                            <span class="d-block text-muted small text-uppercase fw-semibold">Edifício / Bloco</span>
                            <p class="fs-5 fw-medium text-dark"><?= campo($edificio) ?></p>
                        </div>

                        <div class="col-md-3">
                            <span class="d-block text-muted small text-uppercase fw-semibold">Piso</span>
                            <p class="fs-5 fw-medium text-dark"><?= campo($piso) ?></p>
                        </div>

                        <div class="col-md-3">
                            <span class="d-block text-muted small text-uppercase fw-semibold">Serviço / Dep.</span>
                            <p class="fs-5 fw-medium text-dark"><?= campo($servico) ?></p>
                        </div>

                        <div class="col-md-3">
                            <span class="d-block text-muted small text-uppercase fw-semibold">Sala / Gabinete</span>
                            <p class="fs-5 fw-medium text-dark"><?= campo($sala) ?></p>
                        </div>
                    </div>

                    <div class="row g-4 mb-5 border-top pt-4">
                        <div class="col-12">
                            <h5 class="text-secondary fw-bold mb-3">
                                <i class="fa-solid fa-plug me-2"></i>Infraestrutura e Lotação
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <span class="d-block text-muted small text-uppercase fw-semibold">Requisitos Elétricos/Rede</span>
                            <p class="m-0 mt-1">
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-2 fs-6">
                                    <?= campo($requisitos, 'Sem requisitos registados') ?>
                                </span>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <span class="d-block text-muted small text-uppercase fw-semibold">Capacidade Máxima</span>
                            <p class="fs-5 text-dark">
                                <?php if ($capacidade !== null && $capacidade !== ''): ?>
                                    <?= h($capacidade) ?> Equipamentos
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="col-12 mt-4">
                            <span class="d-block text-muted small text-uppercase fw-semibold mb-2">Observações Adicionais</span>
                            <div class="p-3 bg-light rounded border text-dark">
                                <?= nl2br(campo($observacoes, 'Sem observações registadas.')) ?>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 border-top pt-4">
                        <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                            <h5 class="text-secondary fw-bold m-0">
                                <i class="fa-solid fa-heart-pulse me-2"></i>Equipamentos Alocados Atualmente
                            </h5>
                            <span class="badge bg-primary rounded-pill px-3 py-1">
                                <?= $totalEquipamentos ?> <?= $totalEquipamentos === 1 ? 'Equipamento' : 'Equipamentos' ?>
                            </span>
                        </div>
                        
                        <div class="col-12">
                            <div class="table-responsive border rounded">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-uppercase fw-bold text-muted border-0">Código Interno</th>
                                            <th class="small text-uppercase fw-bold text-muted border-0">Designação / Marca</th>
                                            <th class="small text-uppercase fw-bold text-muted border-0">Estado</th>
                                            <th class="small text-uppercase fw-bold text-muted border-0 text-end">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($equipamentos)): ?>
                                            <?php foreach ($equipamentos as $equipamento): ?>
                                                <tr>
                                                    <td class="font-monospace fw-bold text-dark">
                                                        <?= campo($equipamento->codigo_interno ?? null) ?>
                                                    </td>
                                                    <td>
                                                        <span class="d-block fw-medium">
                                                            <?= campo($equipamento->designacao ?? null, 'Equipamento') ?>
                                                        </span>
                                                        <small class="text-muted">
                                                            <?= campo(trim(($equipamento->marca ?? '') . ' ' . ($equipamento->modelo ?? '')), 'Sem marca/modelo') ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge <?= classeEstado($equipamento->estado ?? '') ?>">
                                                            <?= campo($equipamento->estado ?? null, 'Sem estado') ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="../equipamentos/detalhes.php?id_equipamento=<?= urlencode(aes_encrypt($equipamento->id)) ?>" class="btn btn-sm btn-outline-secondary" title="Ver Ficha do Equipamento">
                                                            <i class="fa-solid fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    Ainda não existem equipamentos associados a esta localização.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

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

<?php include __DIR__ . '/../includes/footer.php'; ?>