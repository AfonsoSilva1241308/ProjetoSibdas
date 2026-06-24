<?php
// 1. Segurança sempre no topo
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../includes/funcoes.php';
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

function obterColunas(PDO $ligacao, $tabela) {
    try {
        $stmt = $ligacao->query("SHOW COLUMNS FROM `$tabela`");
        $colunas = [];
        foreach ($stmt->fetchAll(PDO::FETCH_OBJ) as $coluna) {
            $colunas[$coluna->Field] = true;
        }
        return $colunas;
    } catch (PDOException $e) {
        return [];
    }
}

function primeiraColunaExistente(array $colunas, array $candidatos) {
    foreach ($candidatos as $candidato) {
        if (isset($colunas[$candidato])) {
            return $candidato;
        }
    }
    return null;
}

function resolverId($idParam) {
    if (!$idParam) {
        return null;
    }

    if (is_numeric($idParam)) {
        return (int)$idParam;
    }

    $idDesencriptado = aes_decrypt(urldecode($idParam));
    return is_numeric($idDesencriptado) ? (int)$idDesencriptado : null;
}

function valorPost($nome) {
    return trim((string)($_POST[$nome] ?? ''));
}

// ===============================
// Obter ID vindo da lista.php
// Aceita id_localizacao encriptado e também id normal.
// ===============================
$idParam = $_POST['id_localizacao'] ?? $_GET['id_localizacao'] ?? $_GET['localizacao_id'] ?? $_GET['id'] ?? null;
$idLocalizacao = resolverId($idParam);

if (!$idLocalizacao) {
    header('Location: lista.php');
    exit;
}

$erros = [];

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $colunasLocalizacao = obterColunas($ligacao, 'localizacao');

    // Mapear campos do formulário para as colunas reais da tabela.
    // Assim o código continua a funcionar mesmo que a tua BD tenha nomes ligeiramente diferentes.
    $mapaColunas = [
        'edificio' => primeiraColunaExistente($colunasLocalizacao, ['edificio', 'bloco']),
        'piso' => primeiraColunaExistente($colunasLocalizacao, ['piso']),
        'servico' => primeiraColunaExistente($colunasLocalizacao, ['servico', 'departamento']),
        'sala' => primeiraColunaExistente($colunasLocalizacao, ['sala', 'gabinete']),
        'capacidade_maxima' => primeiraColunaExistente($colunasLocalizacao, ['capacidade_maxima', 'capacidade', 'lotacao']),
        'infraestrutura' => primeiraColunaExistente($colunasLocalizacao, ['infraestrutura', 'requisitos_tecnicos', 'requisitos']),
        'observacoes' => primeiraColunaExistente($colunasLocalizacao, ['observacoes', 'observacao', 'notas'])
    ];

    // ===============================
    // Atualizar localização quando o formulário é submetido
    // ===============================
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $edificio = valorPost('edificio');
        $piso = valorPost('piso');
        $servico = valorPost('servico');
        $sala = valorPost('sala');
        $capacidadeMaxima = valorPost('capacidade_maxima');
        $infraestrutura = valorPost('infraestrutura');
        $observacoes = valorPost('observacoes');

        if ($edificio === '') {
            $erros[] = 'O edifício/bloco é obrigatório.';
        }

        if ($piso === '') {
            $erros[] = 'O piso é obrigatório.';
        }

        if ($servico === '') {
            $erros[] = 'O serviço/departamento é obrigatório.';
        }

        if (empty($erros)) {
            $dadosFormulario = [
                'edificio' => $edificio,
                'piso' => $piso,
                'servico' => $servico,
                'sala' => $sala,
                'capacidade_maxima' => $capacidadeMaxima !== '' ? $capacidadeMaxima : null,
                'infraestrutura' => $infraestrutura,
                'observacoes' => $observacoes
            ];

            $sets = [];
            $params = [':id' => $idLocalizacao];

            foreach ($dadosFormulario as $campoFormulario => $valor) {
                $colunaBD = $mapaColunas[$campoFormulario] ?? null;

                if ($colunaBD) {
                    $sets[] = "`$colunaBD` = :$campoFormulario";
                    $params[":$campoFormulario"] = $valor;
                }
            }

            if (isset($colunasLocalizacao['atualizado_em'])) {
                $sets[] = "`atualizado_em` = NOW()";
            } elseif (isset($colunasLocalizacao['updated_at'])) {
                $sets[] = "`updated_at` = NOW()";
            }

            if (!empty($sets)) {
                $sql = "UPDATE localizacao SET " . implode(', ', $sets) . " WHERE id = :id LIMIT 1";
                $stmt = $ligacao->prepare($sql);
                $stmt->execute($params);

                header('Location: editar.php?id_localizacao=' . urlencode(aes_encrypt($idLocalizacao)) . '&sucesso=1');
                exit;
            } else {
                $erros[] = 'Não foi encontrada nenhuma coluna editável na tabela localizacao.';
            }
        }
    }

    // ===============================
    // Carregar localização atual
    // ===============================
    $stmt = $ligacao->prepare("SELECT * FROM localizacao WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $idLocalizacao]);
    $localizacao = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$localizacao) {
        header('Location: lista.php');
        exit;
    }

} catch (PDOException $err) {
    die('Erro ao ligar à base de dados ou ao carregar a localização: ' . h($err->getMessage()));
}

// ===============================
// Valores para preencher o formulário
// ===============================
$edificioAtual = valorRaw($localizacao, ['edificio', 'bloco'], '');
$pisoAtual = valorRaw($localizacao, ['piso'], '');
$servicoAtual = valorRaw($localizacao, ['servico', 'departamento'], '');
$salaAtual = valorRaw($localizacao, ['sala', 'gabinete'], '');
$capacidadeAtual = valorRaw($localizacao, ['capacidade_maxima', 'capacidade', 'lotacao'], '');
$infraestruturaAtual = valorRaw($localizacao, ['infraestrutura', 'requisitos_tecnicos', 'requisitos'], '');
$observacoesAtual = valorRaw($localizacao, ['observacoes', 'observacao', 'notas'], '');

$tituloRegisto = trim($edificioAtual . ' - ' . $servicoAtual . ($salaAtual !== '' ? ' (' . $salaAtual . ')' : ''));
if ($tituloRegisto === '-' || $tituloRegisto === '') {
    $tituloRegisto = 'Localização #' . $idLocalizacao;
}

$temCapacidade = !empty($mapaColunas['capacidade_maxima']);
$temInfraestrutura = !empty($mapaColunas['infraestrutura']);
$temObservacoes = !empty($mapaColunas['observacoes']);
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="d-flex vh-100">
    
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100 bg-light">
        
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="d-flex justify-content-center mt-4">
            <div class="card w-100 shadow-sm rounded border-top border-primary border-4" style="max-width: 1200px;">
                <div class="card-body p-4 p-md-5">
                    
                    <h2 class="mb-4 text-primary">
                        <strong><i class="fa-solid fa-pen-to-square me-2"></i> Atualização de Dados</strong>
                    </h2>
                    <p class="text-muted mb-4">
                        A modificar o registo:
                        <strong><?= campo($tituloRegisto) ?></strong>
                    </p>
                    <hr class="mb-5">

                    <?php if (!empty($erros)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            <?= h(implode(' ', $erros)) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === '1'): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fa-solid fa-circle-check me-2"></i>
                            Localização atualizada com sucesso!
                        </div>
                    <?php endif; ?>

                    <form id="formEditarLocalizacao" action="editar.php?id_localizacao=<?= urlencode(aes_encrypt($idLocalizacao)) ?>" method="POST" novalidate>

                        <input type="hidden" name="id_localizacao" value="<?= h(aes_encrypt($idLocalizacao)) ?>">

                        <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Dados da Instalação</h5>
                        
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Edifício / Bloco <span class="text-danger">*</span></label>
                                <input type="text" name="edificio" class="form-control" value="<?= campo($edificioAtual, '') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">Piso <span class="text-danger">*</span></label>
                                <input type="text" name="piso" class="form-control" value="<?= campo($pisoAtual, '') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">Serviço / Departamento <span class="text-danger">*</span></label>
                                <input type="text" name="servico" class="form-control" list="listaServicos" value="<?= campo($servicoAtual, '') ?>" required>
                                <datalist id="listaServicos">
                                    <option value="Urgência Geral">
                                    <option value="Cuidados Intensivos (UCI)">
                                    <option value="Bloco Operatório">
                                    <option value="Imagiologia">
                                    <option value="Internamento">
                                    <option value="Consulta Externa">
                                    <option value="Esterilização">
                                    <option value="Pediatria">
                                    <option value="Cardiologia">
                                </datalist>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">Sala / Gabinete</label>
                                <input type="text" name="sala" class="form-control" list="listaSalas" value="<?= campo($salaAtual, '') ?>">
                                <datalist id="listaSalas">
                                    <option value="Box 1">
                                    <option value="Box 2">
                                    <option value="Box 3">
                                    <option value="Box 4">
                                    <option value="Sala de Raio X">
                                    <option value="Triagem">
                                    <option value="Gabinete Médico">
                                    <option value="Sala 1">
                                    <option value="Sala 2">
                                </datalist>
                            </div>
                        </div>

                        <?php if ($temCapacidade || $temInfraestrutura || $temObservacoes): ?>
                            <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-4">Capacidade e Requisitos Técnicos</h5>
                            
                            <div class="row g-4 mb-4">
                                <?php if ($temCapacidade): ?>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Capacidade Máxima (Equipamentos)</label>
                                        <input type="number" name="capacidade_maxima" class="form-control" value="<?= campo($capacidadeAtual, '') ?>">
                                    </div>
                                <?php endif; ?>

                                <?php if ($temInfraestrutura): ?>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Infraestrutura Disponível</label>
                                        <input type="text" name="infraestrutura" class="form-control" list="listaInfraestruturas" value="<?= campo($infraestruturaAtual, '') ?>">
                                        <datalist id="listaInfraestruturas">
                                            <option value="Tomadas UPS e Ponto de Rede Disponíveis">
                                            <option value="Apenas Tomadas Normais">
                                            <option value="Tomadas UPS (Sem Rede)">
                                            <option value="Sem Requisitos Especiais">
                                        </datalist>
                                    </div>
                                <?php endif; ?>

                                <?php if ($temObservacoes): ?>
                                    <div class="col-12">
                                        <label class="form-label fw-medium">Observações Adicionais</label>
                                        <textarea name="observacoes" class="form-control" rows="3"><?= h($observacoesAtual) ?></textarea>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
                            <a href="lista.php" class="btn btn-outline-secondary px-4 fw-medium">
                                <i class="fa-solid fa-xmark me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-5 fw-bold">
                                <i class="fa-regular fa-floppy-disk me-1"></i> Guardar Alterações
                            </button>
                        </div>

                        <div class="alert alert-danger text-center d-none mt-3" role="alert" id="mensagemErro">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> Ocorreu um erro ao atualizar a localização. Verifique se preencheu todos os campos corretamente.
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div id="sucessoToastLocalizacao" class="toast align-items-center text-bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-medium fs-6">
                <i class="fa-solid fa-circle-check me-2"></i> Localização atualizada com sucesso!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
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

<?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === '1'): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toastElement = document.getElementById('sucessoToastLocalizacao');
    if (toastElement && typeof bootstrap !== 'undefined') {
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
});
</script>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>