<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

$link_voltar = "lista.php";

// ===============================
// Funções auxiliares
// ===============================
if (!function_exists('h')) {
    function h($valor) {
        return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
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

if (!function_exists('obterColunas')) {
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
}

if (!function_exists('primeiraColunaExistente')) {
    function primeiraColunaExistente(array $colunas, array $candidatos) {
        foreach ($candidatos as $candidato) {
            if (isset($colunas[$candidato])) {
                return $candidato;
            }
        }
        return null;
    }
}

if (!function_exists('resolverIdRecebido')) {
    function resolverIdRecebido($valor) {
        if ($valor === null || $valor === '') {
            return null;
        }

        $valor = (string)$valor;

        if (is_numeric($valor)) {
            return (int)$valor;
        }

        // O PHP já decodifica $_GET/$_POST. Mesmo assim, rawurldecode não transforma '+' em espaço.
        $tentativas = array_unique([$valor, rawurldecode($valor)]);

        foreach ($tentativas as $tentativa) {
            $id = aes_decrypt($tentativa);
            if ($id && is_numeric($id)) {
                return (int)$id;
            }
        }

        return null;
    }
}

if (!function_exists('selectedOption')) {
    function selectedOption($valorAtual, $valorOpcao) {
        return strcasecmp(trim((string)$valorAtual), trim((string)$valorOpcao)) === 0 ? 'selected' : '';
    }
}

if (!function_exists('classeEstado')) {
    function classeEstado($estado) {
        $estadoLower = strtolower((string)$estado);

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
}

if (!function_exists('primeiroTermoUtil')) {
    function primeiroTermoUtil($texto) {
        $texto = trim((string)$texto);
        if ($texto === '') {
            return '';
        }

        $partes = preg_split('/\s+/', $texto);
        foreach ($partes as $parte) {
            $parteLimpa = trim($parte, " ,.;:-_()[]{}\t\n\r\0\x0B");
            if (strlen($parteLimpa) >= 3 && !in_array(strtolower($parteLimpa), ['lda', 'sa', 's.a', 'portugal'], true)) {
                return $parteLimpa;
            }
        }

        return $partes[0] ?? '';
    }
}

if (!function_exists('termosPesquisaFornecedor')) {
    function termosPesquisaFornecedor($fornecedor) {
        $textoBase = implode(' ', array_filter([
            valorRaw($fornecedor, ['nome_empresa', 'empresa', 'nome', 'nome_fornecedor', 'designacao', 'razao_social', 'marca'], ''),
            valorRaw($fornecedor, ['fabricante', 'nome_marca', 'marca'], ''),
            valorRaw($fornecedor, ['observacoes', 'notas'], '')
        ]));

        $textoBase = trim((string)$textoBase);
        if ($textoBase === '') {
            return [];
        }

        // Divide o nome do fornecedor em palavras úteis. Ex.: "Dräger Portugal Lda" -> "Dräger".
        $partes = preg_split('/[\s,.;:\-\/\\()\[\]{}]+/u', $textoBase);
        $ignorar = [
            'lda', 'ltda', 'sa', 's.a', 's.a.', 'unipessoal', 'limitada', 'portugal',
            'empresa', 'fornecedor', 'comercial', 'assistencia', 'assistência',
            'tecnica', 'técnica', 'medica', 'médica', 'healthcare', 'medical',
            'systems', 'system', 'group', 'gmbh', 'inc', 'corp', 'corporation'
        ];

        $termos = [];
        $textoCompleto = mb_strtolower($textoBase, 'UTF-8');
        if (mb_strlen($textoCompleto, 'UTF-8') >= 3) {
            $termos[$textoCompleto] = $textoBase;
        }

        foreach ($partes as $parte) {
            $parte = trim((string)$parte);
            if ($parte === '') {
                continue;
            }

            $normalizada = mb_strtolower($parte, 'UTF-8');
            if (mb_strlen($normalizada, 'UTF-8') < 2 || in_array($normalizada, $ignorar, true)) {
                continue;
            }

            $termos[$normalizada] = $parte;
        }

        return array_values($termos);
    }
}


// ===============================
// Obter ID vindo da lista.php
// Aceita id_fornecedor encriptado e também id normal.
// ===============================
$idParam = $_POST['id_fornecedor'] ?? $_GET['id_fornecedor'] ?? $_GET['fornecedor_id'] ?? $_GET['id'] ?? null;
$idFornecedor = resolverIdRecebido($idParam);

if (!$idFornecedor || !is_numeric($idFornecedor)) {
    header('Location: lista.php');
    exit;
}

$idFornecedor = (int)$idFornecedor;
$idFornecedorEncrypted = aes_encrypt((string)$idFornecedor);
$idFornecedorUrl = urlencode($idFornecedorEncrypted);

$erroFormulario = '';
$mensagemSucesso = $_GET['sucesso'] ?? '';

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $colunasFornecedor = obterColunas($ligacao, 'fornecedor');
    $colunasEquipamento = obterColunas($ligacao, 'equipamento');

    $colunaFKFornecedor = primeiraColunaExistente($colunasEquipamento, [
        'fornecedor_id',
        'id_fornecedor',
        'fabricante_id',
        'id_fabricante',
        'entidade_responsavel_id',
        'id_entidade_responsavel'
    ]);

    // ===============================
    // Processar formulário
    // ===============================
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Associar equipamento ao fornecedor, se existir uma coluna FK na tabela equipamento.
        if (isset($_POST['acao_associar'])) {
            if (!$colunaFKFornecedor) {
                $erroFormulario = 'Não foi encontrada uma coluna de associação na tabela equipamento, como fornecedor_id ou id_fornecedor.';
            } else {
                $idEquipamento = resolverIdRecebido($_POST['novo_equipamento_id'] ?? null);

                if (!$idEquipamento) {
                    $erroFormulario = 'Selecione um equipamento válido para associar.';
                } else {
                    $stmt = $ligacao->prepare("UPDATE equipamento SET `$colunaFKFornecedor` = :id_fornecedor WHERE id = :id_equipamento");
                    $stmt->execute([
                        ':id_fornecedor' => $idFornecedor,
                        ':id_equipamento' => $idEquipamento
                    ]);

                    header('Location: editar.php?id_fornecedor=' . $idFornecedorUrl . '&sucesso=associado');
                    exit;
                }
            }
        }
        // Desassociar equipamento do fornecedor.
        elseif (isset($_POST['acao_desassociar'])) {
            if (!$colunaFKFornecedor) {
                $erroFormulario = 'Não foi encontrada uma coluna de associação na tabela equipamento, como fornecedor_id ou id_fornecedor.';
            } else {
                $idEquipamento = resolverIdRecebido($_POST['id_equipamento'] ?? null);

                if (!$idEquipamento) {
                    $erroFormulario = 'Equipamento inválido para desassociar.';
                } else {
                    $stmt = $ligacao->prepare("UPDATE equipamento SET `$colunaFKFornecedor` = NULL WHERE id = :id_equipamento AND `$colunaFKFornecedor` = :id_fornecedor");
                    $stmt->execute([
                        ':id_equipamento' => $idEquipamento,
                        ':id_fornecedor' => $idFornecedor
                    ]);

                    header('Location: editar.php?id_fornecedor=' . $idFornecedorUrl . '&sucesso=desassociado');
                    exit;
                }
            }
        }
        // Guardar dados do fornecedor.
        else {
            $mapaAtualizacao = [
                'nome_empresa' => ['nome_empresa', 'empresa', 'nome', 'nome_fornecedor', 'designacao', 'razao_social', 'marca'],
                'nif' => ['nif', 'nipc', 'contribuinte', 'numero_fiscal', 'nif_entidade'],
                'tipo_fornecedor' => ['tipo_fornecedor', 'tipo', 'categoria', 'tipo_entidade'],
                'telefone_geral' => ['telefone_geral', 'telefone', 'contacto_telefonico', 'telefone_empresa', 'telemovel'],
                'email_empresa' => ['email_empresa', 'email', 'email_geral'],
                'website' => ['website', 'site', 'url'],
                'morada' => ['morada', 'endereco', 'endereço', 'localidade', 'morada_completa'],
                'observacoes' => ['observacoes', 'observação', 'notas', 'descricao', 'descrição'],
                'nome_contacto' => ['nome_contacto', 'contacto_nome', 'pessoa_contacto', 'responsavel', 'nome_responsavel'],
                'telefone_pessoal' => ['telefone_pessoal', 'telefone_contacto', 'contacto_pessoal', 'telemovel_contacto', 'contacto_telefone'],
                'email_pessoal' => ['email_pessoal', 'email_contacto', 'contacto_email', 'email_responsavel']
            ];

            $sets = [];
            $params = [':id' => $idFornecedor];
            $colunasUsadas = [];

            foreach ($mapaAtualizacao as $campoFormulario => $candidatosColuna) {
                $coluna = primeiraColunaExistente($colunasFornecedor, $candidatosColuna);

                if ($coluna && !isset($colunasUsadas[$coluna])) {
                    $sets[] = "`$coluna` = :$campoFormulario";
                    $params[":$campoFormulario"] = trim((string)($_POST[$campoFormulario] ?? ''));
                    $colunasUsadas[$coluna] = true;
                }
            }

            if (empty($sets)) {
                $erroFormulario = 'Não foi possível encontrar colunas compatíveis na tabela fornecedor para atualizar.';
            } else {
                $sql = "UPDATE fornecedor SET " . implode(', ', $sets) . " WHERE id = :id";
                $stmt = $ligacao->prepare($sql);
                $stmt->execute($params);

                header('Location: editar.php?id_fornecedor=' . $idFornecedorUrl . '&sucesso=1');
                exit;
            }
        }
    }

    // ===============================
    // Carregar fornecedor atual
    // ===============================
    $stmt = $ligacao->prepare("SELECT * FROM fornecedor WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $idFornecedor]);
    $fornecedor = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$fornecedor) {
        header('Location: lista.php');
        exit;
    }

    // ===============================
    // Equipamentos associados e disponíveis
    // ===============================
    $equipamentosAssociados = [];
    $equipamentosDisponiveis = [];
    $associacaoAutomaticaDisponivel = (bool)$colunaFKFornecedor;

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
        $equipamentosAssociados = $stmt->fetchAll(PDO::FETCH_OBJ);

        $stmt = $ligacao->prepare("
            SELECT
                e.id,
                e.codigo_interno,
                e.designacao,
                e.marca,
                e.modelo
            FROM equipamento e
            WHERE e.`$colunaFKFornecedor` IS NULL OR e.`$colunaFKFornecedor` = 0
            ORDER BY e.designacao ASC
        ");
        $stmt->execute();
        $equipamentosDisponiveis = $stmt->fetchAll(PDO::FETCH_OBJ);
    } else {
        // A tua tabela equipamento não tem fornecedor_id. Então não dá para associar/desassociar diretamente.
        // Em vez disso, mostramos automaticamente equipamentos relacionados por texto, comparando o fornecedor com marca/fabricante.
        $termosFornecedor = termosPesquisaFornecedor($fornecedor);
        $condicoes = [];
        $params = [];
        $i = 0;

        foreach (['fabricante', 'marca', 'entidade_responsavel'] as $colunaTexto) {
            if (!isset($colunasEquipamento[$colunaTexto])) {
                continue;
            }

            foreach ($termosFornecedor as $termo) {
                $param = ':termo_' . $i;
                $condicoes[] = "e.`$colunaTexto` LIKE $param";
                $params[$param] = '%' . $termo . '%';
                $i++;
            }
        }

        if (!empty($condicoes)) {
            $sql = "
                SELECT DISTINCT
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
            $equipamentosAssociados = $stmt->fetchAll(PDO::FETCH_OBJ);
        }
    }

} catch (PDOException $err) {
    die('Erro ao ligar à base de dados ou ao carregar o fornecedor: ' . h($err->getMessage()));
}

// ===============================
// Campos principais do fornecedor
// ===============================
$nomeEmpresa = valorRaw($fornecedor, ['nome_empresa', 'empresa', 'nome', 'nome_fornecedor', 'designacao', 'razao_social', 'marca'], 'Fornecedor');
$nif = valorRaw($fornecedor, ['nif', 'nipc', 'contribuinte', 'numero_fiscal', 'nif_entidade'], '');
$tipoFornecedor = valorRaw($fornecedor, ['tipo_fornecedor', 'tipo', 'categoria', 'tipo_entidade'], '');
$telefoneEmpresa = valorRaw($fornecedor, ['telefone_geral', 'telefone', 'contacto_telefonico', 'telefone_empresa', 'telemovel'], '');
$emailEmpresa = valorRaw($fornecedor, ['email_empresa', 'email', 'email_geral'], '');
$website = valorRaw($fornecedor, ['website', 'site', 'url'], '');
$morada = valorRaw($fornecedor, ['morada', 'endereco', 'endereço', 'localidade', 'morada_completa'], '');
$observacoes = valorRaw($fornecedor, ['observacoes', 'observação', 'notas', 'descricao', 'descrição'], '');
$nomeContacto = valorRaw($fornecedor, ['nome_contacto', 'contacto_nome', 'pessoa_contacto', 'responsavel', 'nome_responsavel'], '');
$telefoneContacto = valorRaw($fornecedor, ['telefone_pessoal', 'telefone_contacto', 'contacto_pessoal', 'telemovel_contacto', 'contacto_telefone'], '');
$emailContacto = valorRaw($fornecedor, ['email_pessoal', 'email_contacto', 'contacto_email', 'email_responsavel'], '');

$nomeRegisto = trim((string)$nomeEmpresa) !== '' ? $nomeEmpresa : 'Fornecedor';
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
                        <strong><?= campo($nomeRegisto) ?></strong>
                    </p>
                    <hr class="mb-5">

                    <?php if ($mensagemSucesso === '1'): ?>
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fa-solid fa-circle-check me-2"></i>
                            <div>Fornecedor atualizado com sucesso.</div>
                        </div>
                    <?php elseif ($mensagemSucesso === 'associado'): ?>
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fa-solid fa-link me-2"></i>
                            <div>Equipamento associado com sucesso.</div>
                        </div>
                    <?php elseif ($mensagemSucesso === 'desassociado'): ?>
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fa-solid fa-link-slash me-2"></i>
                            <div>Equipamento desassociado com sucesso.</div>
                        </div>
                    <?php endif; ?>

                    <?php if ($erroFormulario !== ''): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            <div><?= h($erroFormulario) ?></div>
                        </div>
                    <?php endif; ?>

                    <form id="formEditarFornecedor" action="editar.php?id_fornecedor=<?= h($idFornecedorUrl) ?>" method="POST" novalidate>
                        <input type="hidden" name="id_fornecedor" value="<?= h($idFornecedorEncrypted) ?>">

                        <h5 class="text-dark mb-4 border-bottom pb-2">Dados da Entidade</h5>

                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <label class="form-label text-dark">Nome da Empresa / Marca <span class="text-danger">*</span></label>
                                <input type="text" name="nome_empresa" class="form-control" value="<?= h($nomeEmpresa) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark">NIF <span class="text-danger">*</span></label>
                                <input type="text" name="nif" class="form-control bg-light" value="<?= h($nif) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark">Tipo de Fornecedor <span class="text-danger">*</span></label>
                                <select name="tipo_fornecedor" class="form-select" required>
                                    <?php
                                        $opcoesTipo = [
                                            'Fabricante' => 'Fabricante',
                                            'Distribuidor' => 'Distribuidor ou fornecedor comercial',
                                            'Assistencia' => 'Empresa de assistência técnica',
                                            'Consumiveis' => 'Fornecedor de consumíveis ou acessórios'
                                        ];

                                        $tipoExisteNasOpcoes = false;
                                        foreach (array_keys($opcoesTipo) as $valorOpcao) {
                                            if (strcasecmp((string)$tipoFornecedor, (string)$valorOpcao) === 0) {
                                                $tipoExisteNasOpcoes = true;
                                            }
                                        }
                                    ?>
                                    <option value="" disabled <?= $tipoFornecedor === '' ? 'selected' : '' ?>>Selecione o tipo...</option>
                                    <?php if ($tipoFornecedor !== '' && !$tipoExisteNasOpcoes): ?>
                                        <option value="<?= h($tipoFornecedor) ?>" selected><?= h($tipoFornecedor) ?></option>
                                    <?php endif; ?>
                                    <?php foreach ($opcoesTipo as $valor => $texto): ?>
                                        <option value="<?= h($valor) ?>" <?= selectedOption($tipoFornecedor, $valor) ?>><?= h($texto) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark">Contacto Telefónico (Geral) <span class="text-danger">*</span></label>
                                <input type="tel" name="telefone_geral" class="form-control" value="<?= h($telefoneEmpresa) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark">Email da Empresa <span class="text-danger">*</span></label>
                                <input type="email" name="email_empresa" class="form-control" value="<?= h($emailEmpresa) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark">Website</label>
                                <input type="url" name="website" class="form-control" value="<?= h($website) ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-dark">Morada <span class="text-danger">*</span></label>
                                <input type="text" name="morada" class="form-control" value="<?= h($morada) ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-dark">Observações</label>
                                <textarea name="observacoes" class="form-control" rows="3"><?= h($observacoes) ?></textarea>
                            </div>
                        </div>

                        <h5 class="text-dark mb-4 border-bottom pb-2 mt-4">Pessoa de Contacto</h5>

                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label class="form-label text-dark">Nome do Contacto <span class="text-danger">*</span></label>
                                <input type="text" name="nome_contacto" class="form-control" value="<?= h($nomeContacto) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-dark">Telefone Direto <span class="text-danger">*</span></label>
                                <input type="tel" name="telefone_pessoal" class="form-control" value="<?= h($telefoneContacto) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-dark">Email Direto <span class="text-danger">*</span></label>
                                <input type="email" name="email_pessoal" class="form-control" value="<?= h($emailContacto) ?>" required>
                            </div>
                        </div>

                        <h5 class="text-dark mb-4 border-bottom pb-2 mt-5">Equipamentos Associados</h5>

                        <?php if ($associacaoAutomaticaDisponivel): ?>
                            <div class="row g-2 mb-4 bg-light p-3 rounded border">
                                <div class="col-md-8">
                                    <select name="novo_equipamento_id" class="form-select border-secondary">
                                        <option selected disabled value="">Selecione um equipamento do inventário para associar...</option>
                                        <?php foreach ($equipamentosDisponiveis as $equipamentoDisponivel): ?>
                                            <?php
                                                $textoEquipamento = valorRaw($equipamentoDisponivel, ['designacao'], 'Equipamento');
                                                $codigoEquipamento = valorRaw($equipamentoDisponivel, ['codigo_interno'], 'Sem código');
                                                $modeloEquipamento = valorRaw($equipamentoDisponivel, ['modelo'], '');
                                            ?>
                                            <option value="<?= h(aes_encrypt((string)$equipamentoDisponivel->id)) ?>">
                                                <?= h($textoEquipamento) ?>
                                                <?php if ($modeloEquipamento !== ''): ?> — <?= h($modeloEquipamento) ?><?php endif; ?>
                                                (<?= h($codigoEquipamento) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" name="acao_associar" value="1" class="btn btn-outline-primary w-100 fw-medium">
                                        <i class="fa-solid fa-link me-2"></i> Associar Equipamento
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive border rounded mb-2">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="small text-uppercase fw-bold text-muted border-0">Código</th>
                                        <th class="small text-uppercase fw-bold text-muted border-0">Equipamento</th>
                                        <th class="small text-uppercase fw-bold text-muted border-0">Serviço</th>
                                        <th class="small text-uppercase fw-bold text-muted border-0 text-center">Estado</th>
                                        <th class="small text-uppercase fw-bold text-muted border-0 text-end">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($equipamentosAssociados)): ?>
                                        <?php foreach ($equipamentosAssociados as $equipamento): ?>
                                            <?php
                                                $codigoEquipamento = valorRaw($equipamento, ['codigo_interno'], '—');
                                                $designacaoEquipamento = valorRaw($equipamento, ['designacao'], 'Equipamento');
                                                $modeloEquipamento = valorRaw($equipamento, ['modelo'], '');
                                                $servicoEquipamento = valorRaw($equipamento, ['localizacao_servico', 'servico'], '—');
                                                $estadoEquipamento = valorRaw($equipamento, ['estado'], '—');
                                                $idEquipamentoEncrypted = aes_encrypt((string)$equipamento->id);
                                            ?>
                                            <tr>
                                                <td class="fw-medium text-dark"><?= campo($codigoEquipamento) ?></td>
                                                <td>
                                                    <a href="../equipamentos/detalhes.php?id_equipamento=<?= urlencode($idEquipamentoEncrypted) ?>" class="text-dark text-decoration-none fw-medium">
                                                        <?= campo($designacaoEquipamento) ?>
                                                        <?php if ($modeloEquipamento !== ''): ?>
                                                            <span class="text-muted">— <?= campo($modeloEquipamento) ?></span>
                                                        <?php endif; ?>
                                                    </a>
                                                </td>
                                                <td><?= campo($servicoEquipamento) ?></td>
                                                <td class="text-center">
                                                    <span class="badge <?= classeEstado($estadoEquipamento) ?>">
                                                        <?= campo($estadoEquipamento) ?>
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <?php if ($associacaoAutomaticaDisponivel): ?>
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-danger px-2"
                                                            title="Remover Associação"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDesassociar"
                                                            data-id-equipamento="<?= h($idEquipamentoEncrypted) ?>"
                                                            data-nome-equipamento="<?= h($designacaoEquipamento . ($modeloEquipamento !== '' ? ' — ' . $modeloEquipamento : '')) ?>"
                                                            data-codigo-equipamento="<?= h($codigoEquipamento) ?>"
                                                        >
                                                            <i class="fa-solid fa-link-slash"></i> Desassociar
                                                        </button>
                                                    <?php else: ?>
                                                        <a href="../equipamentos/detalhes.php?id_equipamento=<?= urlencode($idEquipamentoEncrypted) ?>" class="btn btn-sm btn-outline-primary px-2" title="Ver Ficha">
                                                            <i class="fa-solid fa-eye"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                Nenhum equipamento associado a este fornecedor.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($associacaoAutomaticaDisponivel): ?>
                            <small class="text-muted d-block mb-4">
                                <i class="fa-solid fa-circle-info me-1"></i>
                                A desassociação não elimina o equipamento do sistema, apenas remove a ligação a este fornecedor.
                            </small>
                        <?php else: ?>
                            <small class="text-muted d-block mb-4">
                                <i class="fa-solid fa-circle-info me-1"></i>
                                Equipamentos apresentados por correspondência com a marca/fabricante do fornecedor.
                            </small>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
                            <a href="lista.php" class="btn btn-light border px-4 d-flex align-items-center">
                                <i class="fa-solid fa-xmark me-2"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-4 d-flex align-items-center">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Alterações
                            </button>
                        </div>

                        <div class="alert alert-danger text-center d-none" role="alert" id="mensagemErro">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            Ocorreu um erro ao atualizar o fornecedor. Verifique se preencheu todos os campos corretamente.
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDesassociar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-5">

                <i class="fa-solid fa-link-slash text-warning mb-4" style="font-size: 4rem;"></i>

                <h5 class="text-dark mb-2">Deseja desassociar este equipamento?</h5>
                <h4 class="fw-bold text-dark mb-4" id="modalNomeEquipamento">Equipamento</h4>

                <div class="mb-4">
                    <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Código Interno</span>
                    <span class="badge bg-light text-dark border fs-6" id="modalCodigoEquipamento">—</span>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="button" class="btn btn-light border fw-medium px-4 py-2" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-2 text-secondary"></i> Não
                    </button>

                    <form action="editar.php?id_fornecedor=<?= h($idFornecedorUrl) ?>" method="POST" class="m-0">
                        <input type="hidden" name="id_fornecedor" value="<?= h($idFornecedorEncrypted) ?>">
                        <input type="hidden" name="id_equipamento" id="modalIdEquipamento" value="">
                        <button type="submit" name="acao_desassociar" value="1" class="btn btn-danger fw-medium px-4 py-2">
                            <i class="fa-solid fa-check me-2"></i> Sim
                        </button>
                    </form>
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
document.addEventListener('DOMContentLoaded', function () {
    const modalDesassociar = document.getElementById('modalDesassociar');

    if (modalDesassociar) {
        modalDesassociar.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const idEquipamento = button.getAttribute('data-id-equipamento') || '';
            const nomeEquipamento = button.getAttribute('data-nome-equipamento') || 'Equipamento';
            const codigoEquipamento = button.getAttribute('data-codigo-equipamento') || '—';

            document.getElementById('modalIdEquipamento').value = idEquipamento;
            document.getElementById('modalNomeEquipamento').textContent = nomeEquipamento;
            document.getElementById('modalCodigoEquipamento').textContent = codigoEquipamento;
        });
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>