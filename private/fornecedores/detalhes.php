<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

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

function limparTexto($valor) {
    return trim((string)($valor ?? ''));
}

function primeiroTermoUtil($texto) {
    $texto = trim((string)$texto);
    if ($texto === '') {
        return '';
    }

    $partes = preg_split('/\s+/', $texto);
    foreach ($partes as $parte) {
        $parteLimpa = trim($parte, " ,.;:-_()[]{}\t\n\r\0\x0B");
        if (mb_strlen($parteLimpa, 'UTF-8') >= 3 && !in_array(mb_strtolower($parteLimpa, 'UTF-8'), ['lda', 'sa', 's.a', 'portugal'], true)) {
            return $parteLimpa;
        }
    }

    return $partes[0] ?? '';
}

function classeEstado($estado) {
    $estadoLower = mb_strtolower((string)$estado, 'UTF-8');

    if (strpos($estadoLower, 'ativo') !== false) {
        return 'bg-success rounded-pill px-3 py-1';
    }

    if (strpos($estadoLower, 'manuten') !== false || strpos($estadoLower, 'calibra') !== false) {
        return 'bg-warning text-dark rounded-pill px-3 py-1';
    }

    if (strpos($estadoLower, 'inativo') !== false || strpos($estadoLower, 'avaria') !== false || strpos($estadoLower, 'abatido') !== false) {
        return 'bg-danger rounded-pill px-3 py-1';
    }

    return 'bg-secondary rounded-pill px-3 py-1';
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

// ===============================
// Obter ID vindo da lista.php
// Aceita id_fornecedor encriptado e também id normal.
// ===============================
$idParam = $_GET['id_fornecedor'] ?? $_GET['fornecedor_id'] ?? $_GET['id'] ?? null;

if (!$idParam) {
    header('Location: lista.php');
    exit;
}

if (is_numeric($idParam)) {
    $idFornecedor = (int)$idParam;
} else {
    $idFornecedor = aes_decrypt(urldecode($idParam));
}

if (!$idFornecedor || !is_numeric($idFornecedor)) {
    header('Location: lista.php');
    exit;
}

$idFornecedor = (int)$idFornecedor;

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fornecedor principal
    $stmt = $ligacao->prepare("SELECT * FROM fornecedor WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $idFornecedor]);
    $fornecedor = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$fornecedor) {
        header('Location: lista.php');
        exit;
    }

    // Tentar encontrar equipamentos associados ao fornecedor.
    // Primeiro tenta por chaves estrangeiras, se existirem. Se não existirem, tenta por nome/marca/fabricante.
    $equipamentos = [];
    $colunasEquipamento = obterColunas($ligacao, 'equipamento');

    $colunaFKFornecedor = primeiraColunaExistente($colunasEquipamento, [
        'fornecedor_id',
        'id_fornecedor',
        'fabricante_id',
        'id_fabricante',
        'entidade_responsavel_id',
        'id_entidade_responsavel'
    ]);

    if ($colunaFKFornecedor) {
        $stmt = $ligacao->prepare("
            SELECT
                e.id,
                e.codigo_interno,
                e.designacao,
                e.marca,
                e.modelo,
                e.estado,
                e.servico,
                l.servico AS localizacao_servico
            FROM equipamento e
            LEFT JOIN localizacao l ON e.localizacao_id = l.id
            WHERE e.`$colunaFKFornecedor` = :id
            ORDER BY e.designacao ASC
        ");
        $stmt->execute([':id' => $idFornecedor]);
        $equipamentos = $stmt->fetchAll(PDO::FETCH_OBJ);
    } else {
        $nomeFornecedor = valorRaw($fornecedor, ['nome', 'empresa', 'nome_empresa', 'nome_fornecedor', 'designacao', 'razao_social', 'marca'], '');
        $marcaFornecedor = valorRaw($fornecedor, ['marca', 'nome_marca', 'empresa', 'nome', 'nome_empresa', 'designacao'], $nomeFornecedor);
        $termoCurto = primeiroTermoUtil($marcaFornecedor ?: $nomeFornecedor);

        $condicoes = [];
        $params = [];

        foreach (['fabricante', 'marca', 'entidade_responsavel'] as $colunaTexto) {
            if (isset($colunasEquipamento[$colunaTexto])) {
                $condicoes[] = "e.`$colunaTexto` LIKE :termoCompleto";
                $condicoes[] = ":nomeCompleto LIKE CONCAT('%', e.`$colunaTexto`, '%')";
                if ($termoCurto !== '') {
                    $condicoes[] = "e.`$colunaTexto` LIKE :termoCurto";
                }
            }
        }

        if (!empty($condicoes)) {
            $params[':termoCompleto'] = '%' . $nomeFornecedor . '%';
            $params[':nomeCompleto'] = $nomeFornecedor;
            if ($termoCurto !== '') {
                $params[':termoCurto'] = '%' . $termoCurto . '%';
            }

            $sql = "
                SELECT
                    e.id,
                    e.codigo_interno,
                    e.designacao,
                    e.marca,
                    e.modelo,
                    e.estado,
                    e.servico,
                    l.servico AS localizacao_servico
                FROM equipamento e
                LEFT JOIN localizacao l ON e.localizacao_id = l.id
                WHERE " . implode(' OR ', $condicoes) . "
                ORDER BY e.designacao ASC
            ";
            $stmt = $ligacao->prepare($sql);
            $stmt->execute($params);
            $equipamentos = $stmt->fetchAll(PDO::FETCH_OBJ);
        }
    }

} catch (PDOException $err) {
    die('Erro ao ligar à base de dados ou ao carregar o fornecedor: ' . h($err->getMessage()));
}

// ===============================
// Campos principais do fornecedor
// Ajustei vários nomes possíveis para funcionar mesmo que as colunas da tua BD tenham nomes ligeiramente diferentes.
// ===============================
$nomeEmpresa = valorRaw($fornecedor, ['nome', 'empresa', 'nome_empresa', 'nome_fornecedor', 'designacao', 'razao_social', 'marca'], 'Fornecedor');
$nif = valorRaw($fornecedor, ['nif', 'nipc', 'contribuinte', 'numero_fiscal', 'nif_entidade'], '—');
$tipoFornecedor = valorRaw($fornecedor, ['tipo_fornecedor', 'tipo', 'categoria', 'tipo_entidade'], '—');
$telefoneEmpresa = valorRaw($fornecedor, ['telefone', 'contacto', 'contacto_telefonico', 'telefone_geral', 'telemovel'], '—');
$morada = valorRaw($fornecedor, ['morada', 'endereco', 'endereço', 'localidade', 'morada_completa'], '—');
$emailEmpresa = valorRaw($fornecedor, ['email', 'email_empresa', 'email_geral'], '—');
$website = valorRaw($fornecedor, ['website', 'site', 'url'], '—');
$observacoes = valorRaw($fornecedor, ['observacoes', 'observação', 'notas', 'descricao', 'descrição'], 'Sem observações registadas.');

$nomeContacto = valorRaw($fornecedor, ['nome_contacto', 'contacto_nome', 'pessoa_contacto', 'responsavel', 'nome_responsavel'], '—');
$telefoneContacto = valorRaw($fornecedor, ['telefone_contacto', 'contacto_pessoal', 'telemovel_contacto', 'contacto_telefone'], '—');
$emailContacto = valorRaw($fornecedor, ['email_contacto', 'contacto_email', 'email_responsavel'], '—');
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="d-flex vh-100">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100 bg-light">
        
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="card w-100 shadow-sm rounded border-top border-secondary border-4" style="max-width: 1200px;">
            <div class="card-body p-4 p-md-5">
                
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h2 class="fw-bold text-dark m-0 d-flex align-items-center mb-2">
                            <i class="fa-solid fa-file-contract text-secondary me-3"></i> Ficha do Fornecedor
                        </h2>
                        <p class="text-muted m-0" style="font-size: 0.95rem;">
                            NIF da Entidade:
                            <span class="badge bg-dark text-white rounded-1 px-2 ms-1" style="font-size: 0.85rem;">
                                <?= campo($nif) ?>
                            </span>
                        </p>
                    </div>
                </div>

                <hr class="my-4 text-muted opacity-25">

                <h5 class="fw-bold text-secondary mb-4" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-circle-info me-2"></i> Dados da Entidade
                </h5>
                
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Empresa / Marca</small>
                        <p class="text-dark mb-0 fs-6"><?= campo($nomeEmpresa) ?></p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tipo de Fornecedor</small>
                        <p class="text-dark mb-0 fs-6"><?= campo($tipoFornecedor) ?></p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Contacto Telefónico (Geral)</small>
                        <p class="text-dark mb-0 fs-6"><?= campo($telefoneEmpresa) ?></p>
                    </div>
                    <div class="col-md-8">
                        <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Morada</small>
                        <p class="text-dark mb-0 fs-6"><?= campo($morada) ?></p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Email da Empresa</small>
                        <p class="text-dark mb-0 fs-6">
                            <?php if ($emailEmpresa && $emailEmpresa !== '—'): ?>
                                <a href="mailto:<?= h($emailEmpresa) ?>" class="text-primary text-decoration-none"><?= h($emailEmpresa) ?></a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Website</small>
                        <p class="text-dark mb-0 fs-6">
                            <?php if ($website && $website !== '—'): ?>
                                <?php
                                    $urlWebsite = preg_match('/^https?:\/\//i', (string)$website) ? $website : 'https://' . $website;
                                ?>
                                <a href="<?= h($urlWebsite) ?>" target="_blank" rel="noopener" class="text-primary text-decoration-none">
                                    <?= h($website) ?>
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-12 mt-4">
                        <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Observações</small>
                        <p class="text-dark mb-0 fs-6"><?= campo($observacoes, 'Sem observações registadas.') ?></p>
                    </div>
                </div>

                <hr class="my-4 text-muted opacity-25" style="border-width: 2px;">

                <h5 class="fw-bold text-secondary mb-4" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-address-book me-2"></i> Pessoa de Contacto
                </h5>
                
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Nome do Contacto</small>
                        <p class="text-dark mb-0 fs-6"><?= campo($nomeContacto) ?></p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Telefone Pessoal</small>
                        <p class="text-dark mb-0 fs-6"><?= campo($telefoneContacto) ?></p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Email Pessoal</small>
                        <p class="text-dark mb-0 fs-6">
                            <?php if ($emailContacto && $emailContacto !== '—'): ?>
                                <a href="mailto:<?= h($emailContacto) ?>" class="text-primary text-decoration-none"><?= h($emailContacto) ?></a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <hr class="my-4 text-muted opacity-25" style="border-width: 2px;">

                <h5 class="fw-bold text-secondary mb-4" style="font-size: 1.1rem;">
                    <i class="fa-solid fa-stethoscope me-2"></i> Equipamentos Cobertos
                </h5>
                
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="border-bottom">
                            <tr>
                                <th class="text-muted small text-uppercase fw-bold pb-2 px-0">Cód. Interno</th>
                                <th class="text-muted small text-uppercase fw-bold pb-2">Equipamento</th>
                                <th class="text-muted small text-uppercase fw-bold pb-2">Serviço Alocado</th>
                                <th class="text-muted small text-uppercase fw-bold pb-2 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($equipamentos)): ?>
                                <?php foreach ($equipamentos as $equipamento): ?>
                                    <?php
                                        $servicoEquipamento = valorRaw($equipamento, ['localizacao_servico', 'servico'], '—');
                                        $nomeEquipamento = trim(campo(valorRaw($equipamento, ['designacao'], 'Equipamento')) . ' ' . campo(valorRaw($equipamento, ['modelo'], '')));
                                    ?>
                                    <tr class="border-bottom">
                                        <td class="fw-bold text-dark px-0 py-3"><?= campo(valorRaw($equipamento, ['codigo_interno'], '—')) ?></td>
                                        <td class="text-dark py-3">
                                            <a href="../equipamentos/detalhes.php?id_equipamento=<?= urlencode(aes_encrypt($equipamento->id)) ?>" class="text-dark text-decoration-none fw-medium">
                                                <?= campo(valorRaw($equipamento, ['designacao'], 'Equipamento')) ?>
                                                <?php if (valorRaw($equipamento, ['modelo'], '') !== ''): ?>
                                                    <span class="text-muted">— <?= campo(valorRaw($equipamento, ['modelo'], '')) ?></span>
                                                <?php endif; ?>
                                            </a>
                                        </td>
                                        <td class="text-dark py-3"><?= campo($servicoEquipamento) ?></td>
                                        <td class="text-center py-3">
                                            <span class="badge <?= classeEstado(valorRaw($equipamento, ['estado'], '—')) ?>">
                                                <?= campo(valorRaw($equipamento, ['estado'], '—')) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Nenhum equipamento associado a este fornecedor.
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