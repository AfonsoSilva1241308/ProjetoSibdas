<?php
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../../config/config.php';
redirect_if_not_logged();

// 2. Variáveis de controlo da Ficha 12
$erros = [];
$erro_sistema = "";
// 3. Verificar se o formulário foi submetido (Método POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    // 3.1 Recolher e normalizar os dados
    $codigo_interno = trim($_POST['codigo_interno'] ?? '');
    $designacao     = trim($_POST['designacao'] ?? '');
    $categoria      = trim($_POST['categoria'] ?? '');
    $marca          = trim($_POST['marca'] ?? '');
    $modelo         = trim($_POST['modelo'] ?? '');
    $numero_serie   = trim($_POST['numero_serie'] ?? '');
    $fabricante     = trim($_POST['fabricante'] ?? '');
    $data_aquisicao = trim($_POST['data_aquisicao'] ?? '');
    $tipo_entrada   = trim($_POST['tipo_entrada'] ?? '');
    $ano_fabrico    = trim($_POST['ano_fabrico'] ?? '');
    $custo          = trim($_POST['custo'] ?? '');
    $estado         = trim($_POST['estado'] ?? '');
    $criticidade    = trim($_POST['criticidade'] ?? '');
    
    // --> NOVOS CAMPOS ADICIONADOS:
    $servico        = trim($_POST['servico'] ?? '');
    $sala           = trim($_POST['sala'] ?? '');
    $observacoes    = trim($_POST['observacoes'] ?? '');
    $is_componente        = $_POST['is_componente'] ?? 'nao';
    $equipamento_pai      = trim($_POST['equipamento_pai'] ?? '');
    
    $cons_designacao      = trim($_POST['cons_designacao'] ?? '');
    $cons_categoria       = trim($_POST['cons_categoria'] ?? '');
    $cons_frequencia      = trim($_POST['cons_frequencia'] ?? '');

    $inicio_garantia      = trim($_POST['inicio_garantia'] ?? '');
    $fim_garantia         = trim($_POST['fim_garantia'] ?? '');
    $contrato_manutencao  = trim($_POST['contrato_manutencao'] ?? '');
    $entidade_responsavel = trim($_POST['entidade_responsavel'] ?? '');
    $periodicidade        = trim($_POST['periodicidade'] ?? '');

    $doc_titulo           = trim($_POST['doc_titulo'] ?? '');
    $doc_categoria        = trim($_POST['doc_categoria'] ?? '');
    $doc_fornecedor       = trim($_POST['doc_fornecedor'] ?? '');
    $doc_validade         = trim($_POST['doc_validade'] ?? '');
    $doc_alerta           = $_POST['doc_alerta'] ?? 'nao';

    // 3.2 Validações (Campos Obrigatórios marcados com *)
    if (empty($codigo_interno)) $erros[] = "O Código Interno é obrigatório.";
    if (empty($designacao))     $erros[] = "A Designação do Equipamento é obrigatória.";
    if (empty($estado))         $erros[] = "O Estado Atual é obrigatório.";
    if (empty($criticidade))    $erros[] = "A Criticidade Clínica é obrigatória.";
    
    // --> NOVAS VALIDAÇÕES ADICIONADAS:
    if (empty($servico))        $erros[] = "O Serviço / Departamento é obrigatório.";
    if (empty($sala))           $erros[] = "A Sala / Gabinete / Box é obrigatória.";
// 3.3 Se não houver erros, fazer o INSERT PDO com Transação (Ficha 12)
    if (empty($erros)) {
        try {
            $ligacao = new PDO(
                "mysql:host=" . MYSQL_HOST . ";port=10464;dbname=" . MYSQL_DATABASE . ";charset=utf8",
                MYSQL_USERNAME,
                MYSQL_PASSWORD
            );
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // INÍCIO DA TRANSAÇÃO SEGURA
            $ligacao->beginTransaction();

            // 1. INSERIR O EQUIPAMENTO E TODOS OS SEUS DADOS
            // CORREÇÃO: equipamento_pai mudou para equipamento_pai_id
            $sqlEquip = "INSERT INTO equipamento (
                            codigo_interno, designacao, categoria, marca, modelo, 
                            num_serie, fabricante, data_aquisicao, tipo_entrada, 
                            ano_fabrico, custo_aquisicao, estado, criticidade,
                            servico, sala, observacoes, is_componente, equipamento_pai_id,
                            inicio_garantia, fim_garantia, contrato_manutencao, entidade_responsavel, periodicidade
                        ) VALUES (
                            :codigo, :desig, :cat, :marca, :modelo, 
                            :num_serie, :fab, :data_aq, :tipo_ent, 
                            :ano, :custo, :estado, :crit,
                            :servico, :sala, :obs, :is_comp, :eq_pai,
                            :ini_gar, :fim_gar, :cont_manut, :ent_resp, :period
                        )";

            $stmtEquip = $ligacao->prepare($sqlEquip);
            
            // Atribuir valores
            $stmtEquip->execute([
                ':codigo'     => $codigo_interno,
                ':desig'      => $designacao,
                ':cat'        => empty($categoria) ? null : $categoria,
                ':marca'      => empty($marca) ? null : $marca,
                ':modelo'     => empty($modelo) ? null : $modelo,
                ':num_serie'  => empty($numero_serie) ? null : $numero_serie,
                ':fab'        => empty($fabricante) ? null : $fabricante,
                ':data_aq'    => empty($data_aquisicao) ? null : $data_aquisicao,
                ':tipo_ent'   => empty($tipo_entrada) ? null : $tipo_entrada,
                ':ano'        => empty($ano_fabrico) ? null : $ano_fabrico,
                ':custo'      => empty($custo) ? null : $custo,
                ':estado'     => $estado,
                ':crit'       => $criticidade,
                ':servico'    => $servico,
                ':sala'       => $sala,
                ':obs'        => empty($observacoes) ? null : $observacoes,
                ':is_comp'    => $is_componente,
                ':eq_pai'     => empty($equipamento_pai) ? null : $equipamento_pai,
                ':ini_gar'    => empty($inicio_garantia) ? null : $inicio_garantia,
                ':fim_gar'    => empty($fim_garantia) ? null : $fim_garantia,
                ':cont_manut' => empty($contrato_manutencao) ? null : $contrato_manutencao,
                ':ent_resp'   => empty($entidade_responsavel) ? null : $entidade_responsavel,
                ':period'     => empty($periodicidade) ? null : $periodicidade
            ]);

            // Obter o ID do equipamento acabado de gravar
            $equipamento_id = $ligacao->lastInsertId();

            // -------------------------------------------------------------
            // 2. INSERIR MÚLTIPLOS CONSUMÍVEIS
            // CORREÇÃO: Tabela 'consumivel' (singular)
            // -------------------------------------------------------------
            if (isset($_POST['lista_cons_designacao']) && is_array($_POST['lista_cons_designacao'])) {
                $sqlCons = "INSERT INTO consumivel (equipamento_id, designacao, categoria, frequencia) 
                            VALUES (:id, :desig, :cat, :freq)";
                $stmtCons = $ligacao->prepare($sqlCons);

                foreach ($_POST['lista_cons_designacao'] as $index => $designacaoCons) {
                    $catCons = $_POST['lista_cons_categoria'][$index] ?? '';
                    $freqCons = $_POST['lista_cons_frequencia'][$index] ?? '';

                    $stmtCons->execute([
                        ':id'    => $equipamento_id,
                        ':desig' => trim($designacaoCons),
                        ':cat'   => trim($catCons),
                        ':freq'  => trim($freqCons)
                    ]);
                }
            }

            // -------------------------------------------------------------
            // 3. INSERIR MÚLTIPLOS DOCUMENTOS
            // CORREÇÃO: Tabela 'documento' (singular) e 'data_validade'
            // -------------------------------------------------------------
            if (isset($_POST['lista_doc_titulo']) && is_array($_POST['lista_doc_titulo'])) {
                $sqlDoc = "INSERT INTO documento (equipamento_id, titulo, categoria, data_validade) 
                           VALUES (:id, :tit, :cat, :val)";
                $stmtDoc = $ligacao->prepare($sqlDoc);

                foreach ($_POST['lista_doc_titulo'] as $index => $tituloDoc) {
                    $catDoc = $_POST['lista_doc_categoria'][$index] ?? '';
                    $valDoc = $_POST['lista_doc_validade'][$index] ?? '';

                    $stmtDoc->execute([
                        ':id'  => $equipamento_id,
                        ':tit' => trim($tituloDoc),
                        ':cat' => trim($catDoc),
                        ':val' => empty(trim($valDoc)) ? null : trim($valDoc) 
                    ]);
                }
            }

            // 4. CONFIRMAR TRANSAÇÃO
            $ligacao->commit();
            
            header("Location: lista.php?sucesso=inserido");
            exit;

        } catch (PDOException $err) {
            // SE HOUVER ERRO, REVERTER TUDO
            $ligacao->rollBack();
            $erro_sistema = "Erro ao gravar na Base de Dados: " . $err->getMessage();
        }
        $ligacao = null;
    }
}
// Definir os detalhes da navbar
$link_voltar = "lista.php"; 
$titulo_pagina = "Registar Novo Equipamento";
$icone_pagina = "fa-solid fa-circle-plus"; 
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex vh-100">
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100 bg-light">
        <?php include '../includes/navbar.php'; ?>
        
        <div class="card w-100 shadow-sm rounded border-top border-primary border-4" style="max-width: 1200px;">
            <div class="card-body p-4 p-md-5">

                <?php if (!empty($erros)): ?>
                    <div class="alert alert-danger mb-4 rounded-3 shadow-sm" role="alert">
                        <h6 class="fw-bold mb-2"><i class="fa-solid fa-triangle-exclamation me-2"></i>Foram encontrados erros:</h6>
                        <ul class="mb-0">
                            <?php foreach ($erros as $erro): ?>
                                <li><?= htmlspecialchars($erro) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($erro_sistema)): ?>
                    <div class="alert alert-danger mb-4 rounded-3 shadow-sm" role="alert">
                        <strong>Erro de Sistema:</strong> <?= htmlspecialchars($erro_sistema) ?>
                    </div>
                <?php endif; ?>
                <form id="formEquipamentoReal" enctype="multipart/form-data" action="#" method="POST" novalidate>
                        
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
                            
                            <div class="mb-5">
                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-2">
                                    <i class="fa-solid fa-microchip text-secondary me-2"></i>1. Identificação Técnica
                                </h5>
                                <div class="row g-4">
                                    <div class="col-md-3">
                                        <label class="form-label fw-medium">Código Interno <span class="text-danger">*</span></label>
                                        <input type="text" name="codigo_interno" class="form-control border-0 bg-light shadow-sm" placeholder="Ex: EQ-001" value="<?= htmlspecialchars($_POST['codigo_interno'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-medium">Designação do Equipamento <span class="text-danger">*</span></label>
                                        <input type="text" name="designacao" class="form-control border-0 bg-light shadow-sm" placeholder="Ex: Ventilador Pulmonar" value="<?= htmlspecialchars($_POST['designacao'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-medium">Categoria / Grupo</label>
                                        <select name="categoria" class="form-select border-0 bg-light shadow-sm">
                                            <option value="" <?= empty($_POST['categoria']) ? 'selected' : '' ?> disabled>Selecione...</option>
                                            <option value="Monitorização" <?= (($_POST['categoria'] ?? '') === 'Monitorização') ? 'selected' : '' ?>>Monitorização</option>
                                            <option value="Suporte de Vida" <?= (($_POST['categoria'] ?? '') === 'Suporte de Vida') ? 'selected' : '' ?>>Suporte de Vida</option>
                                            <option value="Terapia" <?= (($_POST['categoria'] ?? '') === 'Terapia') ? 'selected' : '' ?>>Terapia</option>
                                            <option value="Diagnóstico" <?= (($_POST['categoria'] ?? '') === 'Diagnóstico') ? 'selected' : '' ?>>Diagnóstico</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-medium">Marca</label>
                                        <input type="text" name="marca" class="form-control border-0 bg-light shadow-sm" placeholder="Ex: Philips" value="<?= htmlspecialchars($_POST['marca'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-medium">Modelo</label>
                                        <input type="text" name="modelo" class="form-control border-0 bg-light shadow-sm" placeholder="Ex: IntelliVue MP5" value="<?= htmlspecialchars($_POST['modelo'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-medium">Número de Série</label>
                                        <input type="text" name="numero_serie" class="form-control border-0 bg-light shadow-sm" placeholder="Ex: SN-123456" value="<?= htmlspecialchars($_POST['numero_serie'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-5">
                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fa-solid fa-cart-shopping text-secondary me-2"></i>2. Dados de Aquisição e Estado
                                </h5>
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-medium">Fabricante</label>
                                        <input type="text" name="fabricante" class="form-control border-0 bg-light shadow-sm" placeholder="Ex: Dräger" value="<?= htmlspecialchars($_POST['fabricante'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-medium">Data de Aquisição</label>
                                        <input type="date" name="data_aquisicao" class="form-control border-0 bg-light shadow-sm" value="<?= htmlspecialchars($_POST['data_aquisicao'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-medium">Tipo de Entrada</label>
                                        <select name="tipo_entrada" class="form-select border-0 bg-light shadow-sm">
                                            <option value="Compra" <?= (($_POST['tipo_entrada'] ?? '') === 'Compra') ? 'selected' : '' ?>>Compra</option>
                                            <option value="Doação" <?= (($_POST['tipo_entrada'] ?? '') === 'Doação') ? 'selected' : '' ?>>Doação</option>
                                            <option value="Aluguer" <?= (($_POST['tipo_entrada'] ?? '') === 'Aluguer') ? 'selected' : '' ?>>Aluguer</option>
                                            <option value="Empréstimo" <?= (($_POST['tipo_entrada'] ?? '') === 'Empréstimo') ? 'selected' : '' ?>>Empréstimo</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-medium">Ano de Fabrico</label>
                                        <input type="number" name="ano_fabrico" class="form-control border-0 bg-light shadow-sm" min="1990" max="2026" placeholder="YYYY" value="<?= htmlspecialchars($_POST['ano_fabrico'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-medium">Custo de Aquisição (€)</label>
                                        <input type="number" name="custo" class="form-control border-0 bg-light shadow-sm" step="0.01" placeholder="0.00" value="<?= htmlspecialchars($_POST['custo'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-medium">Estado Atual <span class="text-danger">*</span></label>
                                        <select  name="estado" class="form-select border-success shadow-sm" required>
                                            <option value="Ativo" <?= (($_POST['estado'] ?? 'Ativo') === 'Ativo') ? 'selected' : '' ?>>Ativo</option>
                                            <option value="Em Manutenção" <?= (($_POST['estado'] ?? '') === 'Em Manutenção') ? 'selected' : '' ?>>Em Manutenção</option>
                                            <option value="Inativo" <?= (($_POST['estado'] ?? '') === 'Inativo') ? 'selected' : '' ?>>Inativo</option>
                                            <option value="Abatido" <?= (($_POST['estado'] ?? '') === 'Abatido') ? 'selected' : '' ?>>Abatido</option>
                                            <option value="Em Calibração" <?= (($_POST['estado'] ?? '') === 'Em Calibração') ? 'selected' : '' ?>>Em Calibração</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                                <div class="mb-5">
                                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                        <i class="fa-solid fa-location-dot text-secondary me-2"></i>3. Classificação Clínica e Localização
                                    </h5>
                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium" style="color: #6c757d;">Criticidade Clínica <span class="text-danger">*</span></label>
                                            <select name="criticidade" class="form-select border-warning shadow-sm" required>
                                                <option value="" <?= empty($_POST['criticidade']) ? 'selected' : '' ?> disabled>Definir nível...</option>
                                                <option value="Baixa" <?= (($_POST['criticidade'] ?? '') === 'Baixa') ? 'selected' : '' ?>>Baixa</option>
                                                <option value="Média" <?= (($_POST['criticidade'] ?? '') === 'Média') ? 'selected' : '' ?>>Média</option>
                                                <option value="Alta" <?= (($_POST['criticidade'] ?? '') === 'Alta') ? 'selected' : '' ?>>Alta</option>
                                                <option value="Suporte de Vida" <?= (($_POST['criticidade'] ?? '') === 'Suporte de Vida') ? 'selected' : '' ?>>Suporte de Vida</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium" style="color: #6c757d;">Serviço / Departamento <span class="text-danger">*</span></label>
                                            <select name="servico" class="form-select border-0 bg-light shadow-sm" required>
                                                <option value="" <?= empty($_POST['servico']) ? 'selected' : '' ?> disabled>Selecione o serviço...</option>
                                                <option value="Urgência Geral" <?= (($_POST['servico'] ?? '') === 'Urgência Geral') ? 'selected' : '' ?>>Urgência Geral</option>
                                                <option value="Cuidados Intensivos (UCI)" <?= (($_POST['servico'] ?? '') === 'Cuidados Intensivos (UCI)') ? 'selected' : '' ?>>Cuidados Intensivos (UCI)</option>
                                                <option value="Bloco Operatório" <?= (($_POST['servico'] ?? '') === 'Bloco Operatório') ? 'selected' : '' ?>>Bloco Operatório</option>
                                                <option value="Imagiologia" <?= (($_POST['servico'] ?? '') === 'Imagiologia') ? 'selected' : '' ?>>Imagiologia</option>
                                                <option value="Internamento" <?= (($_POST['servico'] ?? '') === 'Internamento') ? 'selected' : '' ?>>Internamento</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium" style="color: #6c757d;">Sala / Gabinete / Box <span class="text-danger">*</span></label>
                                            <select name="sala" class="form-select border-0 bg-light shadow-sm" required>
                                                <option value="" <?= empty($_POST['sala']) ? 'selected' : '' ?> disabled>Selecione a sala...</option>
                                                <option value="Box 1" <?= (($_POST['sala'] ?? '') === 'Box 1') ? 'selected' : '' ?>>Box 1</option>
                                                <option value="Box 2" <?= (($_POST['sala'] ?? '') === 'Box 2') ? 'selected' : '' ?>>Box 2</option>
                                                <option value="Box 3" <?= (($_POST['sala'] ?? '') === 'Box 3') ? 'selected' : '' ?>>Box 3</option>
                                                <option value="Sala de Raio X" <?= (($_POST['sala'] ?? '') === 'Sala de Raio X') ? 'selected' : '' ?>>Sala de Raio X</option>
                                                <option value="Triagem" <?= (($_POST['sala'] ?? '') === 'Triagem') ? 'selected' : '' ?>>Triagem</option>
                                                <option value="Gabinete Médico" <?= (($_POST['sala'] ?? '') === 'Gabinete Médico') ? 'selected' : '' ?>>Gabinete Médico</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-medium" style="color: #6c757d;">Observações Iniciais</label>
                                            <textarea name="observacoes" class="form-control border-0 bg-light shadow-sm" rows="3" placeholder="Informações adicionais relevantes..."><?= htmlspecialchars($_POST['observacoes'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-5">
                                        <i class="fa-solid fa-sitemap text-info me-2"></i>4. Componentes e Acessórios Associados
                                    </h5>
                                    <div class="p-4 bg-white border rounded shadow-sm mb-5">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="form-check form-switch fs-5 mb-0">
                                                <input name="is_componente" value="sim" class="form-check-input" style="cursor: pointer;" type="checkbox" id="checkEComponente" role="switch" <?= (isset($_POST['is_componente']) && $_POST['is_componente'] === 'sim') ? 'checked' : '' ?>>
                                            </div>
                                            <label class="form-check-label fw-bold text-dark ms-2" style="cursor: pointer;" for="checkEComponente">
                                                Este equipamento é um componente / acessório de outra máquina?
                                            </label>
                                        </div>
                                        
                                        <div id="blocoEquipamentoPai" class="d-none ms-5 mt-3 ps-3 border-start border-info border-3">
                                            <label class="form-label small fw-medium text-secondary">Selecione a máquina a que pertence <span class="text-danger">*</span></label>
                                            <select name="equipamento_pai" class="form-select border-0 bg-light shadow-sm" style="max-width: 400px;">
                                                <option value="" <?= empty($_POST['equipamento_pai']) ? 'selected' : '' ?> disabled>Pesquisar no inventário...</option>
                                                <option value="1" <?= (($_POST['equipamento_pai'] ?? '') === '1') ? 'selected' : '' ?>>04.002 - Monitor Multiparamétrico</option>
                                                <option value="2" <?= (($_POST['equipamento_pai'] ?? '') === '2') ? 'selected' : '' ?>>EV500 - Ventilador Pulmonar</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                        <i class="fa-solid fa-box-open text-warning me-2"></i>5. Consumíveis e Material Compatível
                                    </h5>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="fw-bold text-dark m-0">Consumíveis</h6>
                                        </div>
                                        <button class="btn btn-sm btn-outline-warning text-dark fw-medium px-3" type="button" data-bs-toggle="collapse" data-bs-target="#painelNovoConsumivel">
                                            <i class="fa-solid fa-plus me-1"></i>Adicionar Consumível
                                        </button>
                                    </div>

                                    <div class="collapse mb-4 mt-3" id="painelNovoConsumivel">
                                        <div class="card card-body bg-white border border-warning border-opacity-50 shadow-sm rounded p-4">
                                            <div class="row g-4">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-medium text-dark">Designação do Material</label>
                                                    <input type="text" name="cons_designacao" class="form-control border-0 bg-light shadow-sm" id="novoConsDesignacao" placeholder="Ex: Filtro HMEF" value="<?= htmlspecialchars($_POST['cons_designacao'] ?? '') ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium text-dark">Categoria <span class="text-danger">*</span></label>
                                                    <select name="cons_categoria" class="form-select border-0 bg-light shadow-sm" id="novoConsCategoria">
                                                        <option value="" <?= empty($_POST['cons_categoria']) ? 'selected' : '' ?> disabled>Selecione a categoria...</option>
                                                        <option value="Materiais de Injeção e Punção" <?= (($_POST['cons_categoria'] ?? '') === 'Materiais de Injeção e Punção') ? 'selected' : '' ?>>Materiais de Injeção e Punção</option>
                                                        <option value="Higiene e Descartáveis" <?= (($_POST['cons_categoria'] ?? '') === 'Higiene e Descartáveis') ? 'selected' : '' ?>>Higiene e Descartáveis</option>
                                                        <option value="Eletrónica de Monitorização" <?= (($_POST['cons_categoria'] ?? '') === 'Eletrónica de Monitorização') ? 'selected' : '' ?>>Eletrónica de Monitorização</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-medium text-dark">Frequência <span class="text-danger">*</span></label>
                                                    <select name="cons_frequencia" class="form-select border-0 bg-light shadow-sm" id="novoConsFreq">
                                                        <option value="" <?= empty($_POST['cons_frequencia']) ? 'selected' : '' ?> disabled>Selecione...</option>
                                                        <option value="Por Paciente / Uso Único" <?= (($_POST['cons_frequencia'] ?? '') === 'Por Paciente / Uso Único') ? 'selected' : '' ?>>Por Paciente / Uso Único</option>
                                                        <option value="Diário" <?= (($_POST['cons_frequencia'] ?? '') === 'Diário') ? 'selected' : '' ?>>Diário</option>
                                                        <option value="Mensal" <?= (($_POST['cons_frequencia'] ?? '') === 'Mensal') ? 'selected' : '' ?>>Mensal</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 d-flex justify-content-end gap-3 pt-3 border-top">
                                                    <button type="button" class="btn btn-outline-secondary fw-medium px-4" data-bs-toggle="collapse" data-bs-target="#painelNovoConsumivel">Cancelar</button>
                                                    <button type="button" class="btn btn-warning fw-bold px-4" id="btnGuardarNovoConsumivel"><i class="fa-solid fa-plus me-1"></i> Adicionar à Lista</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="table-responsive bg-white border rounded shadow-sm d-none" id="contentorTabelaConsumiveis">
                                        <table class="table align-middle mb-0">
                                            <thead>
                                                <tr class="text-muted small">
                                                    <th class="py-2 px-3 border-0 bg-light" style="width: 45%;">Designação</th>
                                                    <th class="py-2 border-0 bg-light" style="width: 25%;">Categoria</th>
                                                    <th class="py-2 border-0 bg-light" style="width: 20%;">Frequência</th>
                                                    <th class="py-2 pe-3 border-0 bg-light text-end" style="width: 10%;">Ação</th>
                                                </tr>
                                            </thead>
                                            <tbody id="corpoTabelaConsumiveis"></tbody>
                                        </table>
                                    </div>
                                    <div class="text-center py-4 bg-white border rounded shadow-sm text-muted small" id="msgSemConsumiveis">
                                        <i class="fa-solid fa-box-open fs-4 d-block mb-2 text-secondary opacity-50"></i>
                                        Nenhum consumível adicionado.
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
                                    <a href="lista.php" class="btn btn-outline-secondary px-4 fw-medium"><i class="fa-solid fa-xmark me-1"></i> Cancelar</a>
                                    <button type="button" class="btn btn-primary px-4 fw-bold" onclick="document.getElementById('documentacao-tab').click();">Seguinte <i class="fa-solid fa-arrow-right ms-1"></i></button>
                                </div>
                            </div> 
                            
                            <div class="tab-pane fade" id="documentacao" role="tabpanel" aria-labelledby="documentacao-tab">
                                
                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-2">
                                    <i class="fa-solid fa-shield-halved text-success me-2"></i>6. Garantias e Contratos 
                                </h5>
                                <div class="row g-4 mb-5 p-3 bg-white border rounded shadow-sm">
                                    <div class="col-md-4">
                                        <label class="form-label fw-medium text-secondary">Início da Garantia</label>
                                        <input type="date" name="inicio_garantia" class="form-control bg-light border-0 shadow-sm" value="<?= htmlspecialchars($_POST['inicio_garantia'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-medium text-secondary">Fim da Garantia</label>
                                        <input type="date" name="fim_garantia" class="form-control bg-light border-0 shadow-sm" value="<?= htmlspecialchars($_POST['fim_garantia'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-medium text-secondary">Tipo de Manutenção</label>
<select name="manutencao_tipo" class="form-select bg-light border-0 shadow-sm" required>
    <option value="" <?= empty($_POST['manutencao_tipo']) ? 'selected' : '' ?> disabled>Selecione o tipo...</option>
    <option value="Preventiva" <?= (($_POST['manutencao_tipo'] ?? '') === 'Preventiva') ? 'selected' : '' ?>>Preventiva</option>
    <option value="Corretiva" <?= (($_POST['manutencao_tipo'] ?? '') === 'Corretiva') ? 'selected' : '' ?>>Corretiva</option>
</select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium text-secondary">Entidade Responsável</label>
                                        <select name="entidade_responsavel" class="form-select bg-light border-0 shadow-sm">
                                            <option value="" <?= empty($_POST['entidade_responsavel']) ? 'selected' : '' ?> disabled>Selecione...</option>
                                            <option value="draeger" <?= (($_POST['entidade_responsavel'] ?? '') === 'draeger') ? 'selected' : '' ?>>Dräger Medical GmbH</option>
                                            <option value="philips" <?= (($_POST['entidade_responsavel'] ?? '') === 'philips') ? 'selected' : '' ?>>Philips Healthcare</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium text-secondary">Periodicidade</label>
                                        <select name="periodicidade" class="form-select bg-light border-0 shadow-sm">
                                            <option value="" <?= empty($_POST['periodicidade']) ? 'selected' : '' ?> disabled>Selecione...</option>
                                            <option value="mensal" <?= (($_POST['periodicidade'] ?? '') === 'mensal') ? 'selected' : '' ?>>Mensal</option>
                                            <option value="semestral" <?= (($_POST['periodicidade'] ?? '') === 'semestral') ? 'selected' : '' ?>>Semestral</option>
                                            <option value="anual" <?= (($_POST['periodicidade'] ?? '') === 'anual') ? 'selected' : '' ?>>Anual</option>
                                        </select>
                                    </div>
                                </div>

                                <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-5">
                                    <i class="fa-solid fa-folder-open text-primary me-2"></i>7. Documentação Associada
                                </h5>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="fw-bold text-dark m-0">Ficheiros e Contratos Atuais</h6>
                                        <small class="text-muted">Faça a gestão documental associada a este equipamento.</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary fw-medium px-3" type="button" data-bs-toggle="collapse" data-bs-target="#painelNovoDocumento">
                                        <i class="fa-solid fa-plus me-1"></i>Anexar Novo Documento
                                    </button>
                                </div>

                                <div class="collapse mb-4 mt-3" id="painelNovoDocumento">
                                    <div class="card card-body bg-white border border-primary border-opacity-25 shadow-sm rounded p-4">
                                        <h6 class="fw-bold text-primary mb-4"><i class="fa-solid fa-file-circle-plus me-2"></i>Registar Novo Documento</h6>
                                        
                                        <div id="formNovoDocInline">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-medium text-dark">Título / Nome do Documento <span class="text-danger">*</span></label>
                                                    <input type="text" name="doc_titulo" class="form-control border-0 bg-light shadow-sm" id="novoDocTitulo" placeholder="Ex: Manual de Utilizador V500" value="<?= htmlspecialchars($_POST['doc_titulo'] ?? '') ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-medium text-dark">Categoria <span class="text-danger">*</span></label>
                                                    <select name="doc_categoria" class="form-select border-0 bg-light shadow-sm" id="novoDocCategoria">
    <option value="" <?= empty($_POST['doc_categoria']) ? 'selected' : '' ?> disabled>Selecione...</option>
    <option value="Manual de Utilizador" <?= (($_POST['doc_categoria'] ?? '') === 'Manual de Utilizador') ? 'selected' : '' ?>>Manual de Utilizador</option>
    <option value="Manual de Serviço" <?= (($_POST['doc_categoria'] ?? '') === 'Manual de Serviço') ? 'selected' : '' ?>>Manual de Serviço</option>
    <option value="Certificado de Calibração" <?= (($_POST['doc_categoria'] ?? '') === 'Certificado de Calibração') ? 'selected' : '' ?>>Certificado de Calibração</option>
    <option value="Contrato de Manutenção" <?= (($_POST['doc_categoria'] ?? '') === 'Contrato de Manutenção') ? 'selected' : '' ?>>Contrato de Manutenção</option>
    <option value="Fatura ou Guia de Aquisição" <?= (($_POST['doc_categoria'] ?? '') === 'Fatura ou Guia de Aquisição') ? 'selected' : '' ?>>Fatura ou Guia de Aquisição</option>
    <option value="Declaração de Conformidade" <?= (($_POST['doc_categoria'] ?? '') === 'Declaração de Conformidade') ? 'selected' : '' ?>>Declaração de Conformidade</option>
    <option value="Relatório Técnico" <?= (($_POST['doc_categoria'] ?? '') === 'Relatório Técnico') ? 'selected' : '' ?>>Relatório Técnico</option>
</select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-medium text-dark">Ficheiro (PDF/JPG) <span class="text-danger">*</span></label>
                                                    <input type="file" name="doc_ficheiro" class="form-control border-0 bg-light shadow-sm" id="novoDocFicheiro">
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium text-dark">Entidade Responsável / Fornecedor</label>
                                                    <select name="entidade_responsavel" class="form-select bg-light border-0 shadow-sm">
    <option value="" <?= empty($_POST['entidade_responsavel']) ? 'selected' : '' ?> disabled>Selecione...</option>
    <option value="B. Braun Portugal" <?= (($_POST['entidade_responsavel'] ?? '') === 'B. Braun Portugal') ? 'selected' : '' ?>>B. Braun Portugal</option>
    <option value="Siemens Healthineers" <?= (($_POST['entidade_responsavel'] ?? '') === 'Siemens Healthineers') ? 'selected' : '' ?>>Siemens Healthineers</option>
    <option value="Johnson & Johnson Medical" <?= (($_POST['entidade_responsavel'] ?? '') === 'Johnson & Johnson Medical') ? 'selected' : '' ?>>Johnson & Johnson Medical</option>
    <option value="Dräger Medical GmbH" <?= (($_POST['entidade_responsavel'] ?? '') === 'Dräger Medical GmbH') ? 'selected' : '' ?>>Dräger Medical GmbH</option>
    <option value="Philips Healthcare" <?= (($_POST['entidade_responsavel'] ?? '') === 'Philips Healthcare') ? 'selected' : '' ?>>Philips Healthcare</option>
</select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium text-dark">Validade / Término</label>
                                                    <input name="doc_validade" type="date" class="form-control border-0 bg-light shadow-sm" id="novoDocValidade" value="<?= htmlspecialchars($_POST['doc_validade'] ?? '') ?>">
                                                </div>
                                                <div class="col-md-4 d-flex align-items-end pb-1">
                                                    <div class="form-check form-switch mb-2">
                                                        <input class="form-check-input" type="checkbox" name="doc_alerta" value="sim" role="switch" id="alertaExp" style="cursor: pointer;" <?= (isset($_POST['doc_alerta']) && $_POST['doc_alerta'] === 'sim') ? 'checked' : '' ?>>
                                                        <label class="form-check-label fw-medium text-dark ms-1" for="alertaExp" style="cursor: pointer;">
                                                            Ativar Alerta de Expiração
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-12 d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                                                    <button type="button" class="btn btn-outline-secondary fw-medium px-4" data-bs-toggle="collapse" data-bs-target="#painelNovoDocumento"><i class="fa-solid fa-xmark me-1"></i> Cancelar</button>
                                                    <button type="button" class="btn btn-primary fw-bold px-4" id="btnGuardarNovoDoc"><i class="fa-solid fa-plus me-1"></i> Adicionar à Lista</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

<div class="table-responsive bg-white border rounded shadow-sm d-none" id="contentorTabelaDocs">
    <table class="table align-middle mb-0">
        <thead>
            <tr class="text-muted small">
                <th class="py-2 px-3 border-0 bg-light" style="width: 25%;">Categoria</th>
                <th class="py-2 border-0 bg-light" style="width: 45%;">Título / Ficheiro</th>
                <th class="py-2 border-0 bg-light" style="width: 20%;">Validade</th>
                <th class="py-2 pe-3 border-0 bg-light text-end" style="width: 10%;">Ação</th>
            </tr>
        </thead>
        <tbody id="corpoTabelaDocs">
            </tbody>
    </table>
</div>

<div class="text-center py-5 bg-white border rounded shadow-sm text-muted small" id="msgSemDocs">
    <i class="fa-solid fa-folder-open fs-2 d-block mb-3 opacity-25"></i>
    Nenhum documento anexado a este novo registo.
</div>

                                <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
                                    <button type="button" class="btn btn-light border px-4 fw-medium" onclick="document.getElementById('dados-tab').click();">
                                        <i class="fa-solid fa-arrow-left me-1"></i> Anterior
                                    </button>
                                    <button type="submit" class="btn btn-primary px-5 fw-bold btn-guardar">
                                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Guardar Registo 
                                    </button>
                                </div>

                            </div> </div> </form>
                    
                </div>
            </div>

        </div>
    </div>
<div class="modal fade" id="modalRemoverDocumento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-5">
                
                <i class="fa-solid fa-triangle-exclamation text-warning mb-4" style="font-size: 4rem;"></i>
                
                <h5 class="text-dark mb-2">Deseja remover este documento?</h5>
                
                <h3 class="fw-bold text-dark mb-4" id="nomeDocModal">nome_do_ficheiro.pdf</h3>
                
                <div class="mb-4">
                    <span class="d-block text-dark fw-bold mb-1" style="font-size: 0.95rem;">
                        Tipo de Documento: <span class="text-secondary fw-medium" id="tipoDocModal">Tipo</span>
                    </span>
                    <span class="d-block text-muted small mt-3">
                        A ligação a este ficheiro será eliminada permanentemente.
                    </span>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="button" class="btn btn-light border fw-medium px-4 py-2" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-2 text-secondary"></i> Não
                    </button>
                    <button type="button" class="btn btn-danger fw-medium px-4 py-2" id="btnConfirmarRemocaoDoc">
                        <i class="fa-solid fa-check me-2"></i> Sim
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalRemoverConsumivel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-5">
                
                <i class="fa-solid fa-triangle-exclamation text-warning mb-4" style="font-size: 4rem;"></i>
                
                <h5 class="text-dark mb-2">Deseja remover este consumível?</h5>
                
                <h3 class="fw-bold text-dark mb-4" id="nomeConsModal">nome_do_consumivel</h3>
                
                <div class="mb-4">
                    <span class="d-block text-dark fw-bold mb-1" style="font-size: 0.95rem;">
                        Categoria: <span class="text-secondary fw-medium" id="catConsModal">Categoria</span>
                    </span>
                    <span class="d-block text-muted small mt-3">
                        Este consumível será removido da lista associada a este equipamento.
                    </span>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="button" class="btn btn-light border fw-medium px-4 py-2" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-2 text-secondary"></i> Não
                    </button>
                    <button type="button" class="btn btn-danger fw-medium px-4 py-2" id="btnConfirmarRemocaoConsumivel">
                        <i class="fa-solid fa-check me-2"></i> Sim
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
        <div id="sucessoToast" class="toast align-items-center text-bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-medium fs-6">
                    <i class="fa-solid fa-circle-check me-2"></i> Equipamento registado com sucesso no inventário!
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

<?php include '../includes/footer.php'; ?>