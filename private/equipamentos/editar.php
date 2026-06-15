<?php
// 1. Chamar as funções de segurança e verificar a sessão (Crucial!)
require_once '../includes/funcoes.php';
redirect_if_not_logged();

// 2. Ativar o "Superpoder" do botão voltar na Navbar
$link_voltar = "lista.php"; 
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex vh-100">
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100 bg-light">
        
        <?php include '../includes/navbar.php'; ?>

            <div class="d-flex justify-content-center mt-4 mb-5">
    <div class="card w-100 shadow-sm rounded border-top border-primary border-4 h-auto" style="max-width: 1200px;">
        <div class="card-body p-4 p-md-5">
            
            <h2 class="mb-1 text-primary"><strong><i class="fa-solid fa-pen-to-square me-2"></i> Atualização de Dados </strong></h2>
            <p class="text-muted mb-4">A modificar o registo: <span class="badge bg-dark fs-6 font-monospace ms-1">EV500-2021</span></p>
            <hr class="mb-5 text-secondary opacity-25">
            
            <form action="detalhes.php" method="POST" class="form-editar-equipamento" enctype="multipart/form-data"  novalidate>

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
                        
                        <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-2">
                            <i class="fa-solid fa-microchip text-secondary me-2"></i>1. Identificação Técnica
                        </h5>
                        <div class="row g-4 mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-medium">Código Interno <span class="text-danger">*</span></label>
                                <input type="text" name="codigo_interno" class="form-control bg-light" value="EV500-2021" readonly>
                                <small class="text-muted" style="font-size: 0.75rem;">Não editável</small>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-medium">Designação do Equipamento <span class="text-danger">*</span></label>
                                <input type="text" name="designacao"  class="form-control border-0 bg-light shadow-sm" value="Ventilador Pulmonar" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Categoria / Grupo</label>
                                <select name="categoria" class="form-select border-0 bg-light shadow-sm">
                                    <option value="monitorizacao">Monitorização</option>
                                    <option value="suporte_vida" selected>Suporte de Vida</option>
                                    <option value="terapia">Terapia</option>
                                    <option value="diagnostico">Diagnóstico</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Marca</label>
                                <input type="text" name="marca" class="form-control border-0 bg-light shadow-sm" value="Dräger">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Modelo</label>
                                <input type="text" name="modelo" class="form-control border-0 bg-light shadow-sm" value="Evita V500">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Número de Série</label>
                                <input type="text" name="numero_serie" class="form-control border-0 bg-light shadow-sm" value="DRG-984372-V5">
                            </div>
                        </div>

                        <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-5">
                            <i class="fa-solid fa-cart-shopping text-secondary me-2"></i>2. Dados de Aquisição e Estado
                        </h5>
                        <div class="row g-4 mb-5">
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Fabricante</label>
                                <input type="text" name="fabricante" class="form-control border-0 bg-light shadow-sm" value="Dräger Medical GmbH">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Data de Aquisição</label>
                                <input type="date" name="data_aquisicao" class="form-control border-0 bg-light shadow-sm" value="2021-03-15">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Tipo de Entrada</label>
                                <select name="tipo_entrada" class="form-select border-0 bg-light shadow-sm">
                                    <option value="compra" selected>Compra</option>
                                    <option value="doacao">Doação</option>
                                    <option value="aluguer">Aluguer</option>
                                    <option value="emprestimo">Empréstimo</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Ano de Fabrico</label>
                                <input type="number" name="ano_fabrico"  class="form-control border-0 bg-light shadow-sm" min="1990" max="2026" value="2020">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Custo de Aquisição (€)</label>
                                <input type="number" name="custo" class="form-control border-0 bg-light shadow-sm" step="0.01" value="24500.00">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Estado Atual <span class="text-danger">*</span></label>
                                <select name="estado" class="form-select border-success shadow-sm" required>
                                    <option value="ativo" selected>Ativo</option>
                                    <option value="manutencao">Em Manutenção</option>
                                    <option value="inativo">Inativo</option>
                                    <option value="abatido">Abatido</option>
                                    <option value="calibracao">Em Calibração</option>
                                </select>
                            </div>
                        </div>

                        <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-5">
                            <i class="fa-solid fa-location-dot text-secondary me-2"></i>3. Classificação Clínica e Localização
                        </h5>
                        <div class="row g-4 mb-5">
                            <div class="col-md-4">
                                <label class="form-label fw-medium" style="color: #6c757d;">Criticidade Clínica *</label>
                                <select name="criticidade" class="form-select border-warning shadow-sm" required>
                                    <option value="baixa">Baixa</option>
                                    <option value="media">Média</option>
                                    <option value="alta">Alta</option>
                                    <option value="suporte_vida" selected>Suporte de Vida</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium" style="color: #6c757d;">Serviço / Departamento *</label>
                                <select name="servico" class="form-select border-0 bg-light shadow-sm" required>
                                    <option value="" selected disabled>Selecione o serviço...</option>
                                    <option value="urgencia">Urgência Geral</option>
                                    <option value="uci">Cuidados Intensivos (UCI)</option>
                                    <option value="bloco">Bloco Operatório</option>
                                    <option value="imagiologia">Imagiologia</option>
                                    <option value="internamento">Internamento</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium" style="color: #6c757d;">Sala / Gabinete / Box *</label>
                                <select name="sala" class="form-select border-0 bg-light shadow-sm" required>
                                    <option value="" selected disabled>Selecione a sala...</option>
                                    <option value="box1">Box 1</option>
                                    <option value="box2">Box 2</option>
                                    <option value="box3">Box 3</option>
                                    <option value="isolamento">Sala de Raio X</option>
                                    <option value="triagem">Triagem</option>
                                    <option value="gabinete_medico">Gabinete Médico</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium" style="color: #6c757d;">Observações</label>
                                <textarea name="observacoes" class="form-control border-0 bg-light shadow-sm" rows="3">Equipamento em pleno funcionamento. Última calibração realizada com sucesso em janeiro de 2026.</textarea>
                            </div>
                        </div>

                        <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-5">
                            <i class="fa-solid fa-sitemap text-info me-2"></i>4. Componentes e Acessórios Associados
                        </h5>
                        <div class="p-4 bg-white border rounded shadow-sm mb-5">
                            <div class="mb-4 pb-4 border-bottom">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="form-check form-switch fs-5 mb-0">
                                        <input name="is_componente" value="sim" class="form-check-input" style="cursor: pointer;" type="checkbox" id="checkEComponente" role="switch">
                                    </div>
                                    <label class="form-check-label fw-bold text-dark ms-2" style="cursor: pointer;" for="checkEComponente">
                                        Este equipamento é um componente / acessório de outra máquina?
                                    </label>
                                </div>
                                <div id="blocoEquipamentoPai" class="d-none ms-5 mt-3 ps-3 border-start border-info border-3">
                                    <label class="form-label small fw-medium text-secondary">Selecione a máquina a que pertence <span class="text-danger">*</span></label>
                                    <select name="equipamento_pai" class="form-select border-0 bg-light shadow-sm" style="max-width: 400px;">
                                        <option value="" selected disabled>Pesquisar no inventário...</option>
                                        <option value="1">04.002 - Monitor Multiparamétrico</option>
                                        <option value="2">EV500 - Ventilador Pulmonar</option>
                                    </select>
                                </div>
                            </div>
                            <div id="blocoGerirFilhos">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="fw-bold text-dark m-0">Componentes e Acessórios Vinculados</h6>
                                        <small class="text-muted">Adicione equipamentos soltos que pertençam a esta unidade principal.</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-info fw-medium px-3" type="button" data-bs-toggle="collapse" data-bs-target="#painelVincularComponente">
                                        <i class="fa-solid fa-link me-1"></i> Vincular Componente
                                    </button>
                                </div>
                                <div class="collapse mb-3" id="painelVincularComponente">
                                    <div class="card card-body bg-light border-info border-opacity-25 shadow-sm p-3 rounded">
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-9">
                                                <label class="form-label small fw-medium text-dark">Pesquisar Equipamento no Inventário <span class="text-danger">*</span></label>
                                                <select name="novo_componente" class="form-select border-0 shadow-sm" id="novoComponenteSelect">
                                                    <option value="" selected disabled>Selecione um equipamento para vincular...</option>
                                                    <option value="Humidificador Aquecido MR850|EV500-2021-C01">EV500-2021-C01 - Humidificador Aquecido MR850</option>
                                                    <option value="Braço Articulado de Suporte|EV500-2021-C02">EV500-2021-C02 - Braço Articulado de Suporte</option>
                                                    <option value="Sensor de Oximetria SpO2|04.002.01">04.002.01 - Sensor de Oximetria SpO2</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="button" class="btn btn-info text-white fw-bold w-100" id="btnVincularComponente">Vincular</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive border rounded shadow-sm" id="contentorTabelaComp">
                                    <table class="table align-middle mb-0 bg-white">
                                        <thead class="bg-light text-muted small">
                                            <tr>
                                                <th class="py-2 border-0 px-3">Cód. Componente</th>
                                                <th class="py-2 border-0">Designação</th>
                                                <th class="py-2 border-0 text-center">Estado Atual</th>
                                                <th class="py-2 border-0 text-end pe-3">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody id="corpoTabelaComp">
                                            <tr>
                                                <td class="py-3 px-3 border-0 text-muted font-monospace small">EV500-2021-C01</td>
                                                <td class="py-3 border-0 fw-medium text-dark">Humidificador Aquecido MR850</td>
                                                <td class="py-3 border-0 text-center">
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Ativo</span>
                                                </td>
                                                <td class="py-3 border-0 text-end pe-3">
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remover-componente px-2" title="Desvincular" data-bs-toggle="modal" data-bs-target="#modalDesvincularComponente">
                                                        <i class="fa-solid fa-link-slash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center py-4 bg-white border rounded shadow-sm text-muted small d-none" id="msgSemComp">
                                    <i class="fa-solid fa-sitemap fs-4 d-block mb-2 text-secondary opacity-50"></i>
                                    Nenhum componente vinculado a esta unidade.
                                </div>
                            </div>
                        </div>

                        <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-5">
    <i class="fa-solid fa-box-open text-warning me-2"></i>5. Consumíveis e Material Compatível
</h5>
<div class="p-3 bg-white border rounded shadow-sm mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h6 class="fw-bold text-dark m-0">Consumíveis Atuais</h6>
            <small class="text-muted">Monitorize os itens associados a este equipamento.</small>
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
                    <input type="text" name="cons_designacao" class="form-control border-0 bg-light shadow-sm" id="novoConsDesignacao" placeholder="Ex: Filtro HMEF">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-dark">Categoria <span class="text-danger">*</span></label>
                    <select name="cons_categoria" class="form-select border-0 bg-light shadow-sm" id="novoConsCategoria" required>
                        <option value="" selected disabled>Selecione a categoria...</option>
                        <option value="Materiais de Injeção e Punção">Materiais de Injeção e Punção</option>
                        <option value="Higiene e Descartáveis">Higiene e Descartáveis</option>
                        <option value="Eletrónica de Monitorização">Eletrónica de Monitorização</option>
                        <option value="Tubagens e Acessórios">Tubagens e Acessórios de Imagiologia/Fluídos</option>
                        <option value="Agentes de Desinfeçao">Agentes de Desinfeção</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium text-dark">Frequência <span class="text-danger">*</span></label>
                    <select name="cons_frequencia" class="form-select border-0 bg-light shadow-sm" id="novoConsFreq" required>
                        <option value="" selected disabled>Selecione...</option>
                        <option value="Por Paciente">Por Paciente</option>
                        <option value="Diário">Diário</option>
                        <option value="Mensal">Mensal</option>
                        <option value="Anual ">Anual</option>
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end gap-3 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary fw-medium px-4" data-bs-toggle="collapse" data-bs-target="#painelNovoConsumivel">Cancelar</button>
                    <button type="button" class="btn btn-warning fw-bold px-4" id="btnGuardarNovoConsumivel">
                        <i class="fa-solid fa-plus me-1"></i> Adicionar à Lista
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive" id="contentorTabelaConsumiveis">
        <table class="table align-middle mb-0">
            <thead>
                <tr class="text-muted small">
                    <th class="py-2 border-0 bg-light" style="width: 45%;">Designação do Material *</th>
                    <th class="py-2 border-0 bg-light" style="width: 25%;">Categoria *</th>
                    <th class="py-2 border-0 bg-light" style="width: 20%;">Frequência *</th>
                    <th class="py-2 border-0 bg-light text-end" style="width: 10%;">Ação</th>
                </tr>
            </thead>
            <tbody id="corpoTabelaConsumiveis">
                <tr>
                    <td class="py-3 px-3 border-0">
                        <input type="hidden" name="edit_cons_id[]" value="1">
                        <input type="text" name="edit_cons_designacao[]" class="form-control bg-light border-0 shadow-sm fw-medium text-dark" value="Filtro HMEF" required>
                    </td>
                    <td class="py-3 border-0">
                        <select name="edit_cons_categoria[]" class="form-select bg-light border-0 shadow-sm" required>
                            <option value="Materiais de Injeção e Punção">Materiais de Injeção e Punção</option>
                            <option value="Higiene e Descartáveis">Higiene e Descartáveis</option>
                            <option value="Eletrónica de Monitorização">Eletrónica de Monitorização</option>
                            <option value="Tubagens e Acessórios de Imagiologia/Fluídos" selected>Tubagens e Acessórios de Imagiologia/Fluídos</option>
                            <option value="Agentes de Desinfeçao">Agentes de Desinfeção</option>
                        </select>
                    </td>
                    <td class="py-3 border-0">
                        <select name="edit_cons_frequencia[]" class="form-select bg-light border-0 shadow-sm" required>
                            <option value="Por Paciente" selected>Por Paciente </option>
                            <option value="Diário">Diário</option>
                            <option value="Mensal">Mensal</option>
                            <option value="Anual ">Anual</option>
                        </select>
                    </td>
                    <td class="py-3 pe-3 border-0 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-abrir-modal-remover px-2" data-bs-toggle="modal" data-bs-target="#modalRemoverConsumivel">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="py-3 px-3 border-0">
                        <input type="hidden" name="edit_cons_id[]" value="2">
                        <input type="text" class="form-control bg-light border-0 shadow-sm fw-medium text-dark" value="Célula de O2" required>
                    </td>
                    <td class="py-3 border-0">
                        <select name="edit_cons_categoria[]" class="form-select bg-light border-0 shadow-sm" required>
                            <option value="Materiais de Injeção e Punção">Materiais de Injeção e Punção</option>
                            <option value="Higiene e Descartáveis">Higiene e Descartáveis</option>
                            <option value="Eletrónica de Monitorização" selected>Eletrónica de Monitorização</option>
                            <option value="Tubagens e Acessórios de Imagiologia/Fluídos">Tubagens e Acessórios de Imagiologia/Fluídos</option>
                            <option value="Agentes de Desinfeçao">Agentes de Desinfeção</option>
                        </select>
                    </td>
                    <td class="py-3 border-0">
                        <select name="edit_cons_frequencia[]" class="form-select bg-light border-0 shadow-sm" required>
                            <option value="Por Paciente">Por Paciente </option>
                            <option value="Diário">Diário</option>
                            <option value="Mensal" selected>Mensal</option>
                            <option value="Anual">Anual </option>
                        </select>
                    </td>
                    <td class="py-3 pe-3 border-0 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-abrir-modal-remover px-2" data-bs-toggle="modal" data-bs-target="#modalRemoverConsumivel">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="text-center py-3 text-muted small d-none" id="msgSemConsumiveis">
        <i class="fa-solid fa-circle-info me-1"></i> Nenhum consumível associado.
    </div>
</div> <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
    <a href="lista.php" class="btn btn-outline-secondary px-4 fw-medium">
        <i class="fa-solid fa-xmark me-1"></i> Cancelar
    </a>
    <button type="button" class="btn btn-primary px-4 fw-bold" onclick="document.getElementById('documentacao-tab').click();">
        Seguinte <i class="fa-solid fa-arrow-right ms-1"></i>
    </button>
</div>

</div> <div class="tab-pane fade" id="documentacao" role="tabpanel" aria-labelledby="documentacao-tab">
    
    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-2">
        <i class="fa-solid fa-shield-halved text-success me-2"></i>6. Garantias e Contratos
    </h5>
    <div class="row g-4 mb-5 p-3 bg-white border rounded shadow-sm">
        <div class="col-md-4">
            <label class="form-label fw-medium text-secondary">Início da Garantia</label>
            <input type="date" name="inicio_garantia" class="form-control bg-light border-0 shadow-sm" value="2021-03-15">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-medium text-secondary">Fim da Garantia</label>
            <input type="date" name="fim_garantia" class="form-control bg-light border-0 shadow-sm" value="2027-03-15">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-medium text-secondary">Contrato de Manutenção</label>
            <select name="contrato_manutencao" class="form-select bg-light border-0 shadow-sm">
                <option value="sim_preventivo">Sim (Preventivo)</option>
                <option value="sim_integral" selected>Sim (Preventivo e Corretivo)</option>
                <option value="nao">Não</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-medium text-secondary">Entidade Responsável / Fornecedor</label>
            <select name="entidade_responsavel" class="form-select bg-light border-0 shadow-sm">
                <option value="draeger" selected>Dräger Medical GmbH</option>
                <option value="philips">Philips Healthcare</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-medium text-secondary">Periodicidade</label>
            <select name="periodicidade" class="form-select bg-light border-0 shadow-sm">
                <option value="mensal">Mensal</option>
                <option value="semestral">Semestral</option>
                <option value="anual" selected>Anual</option>
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
        <button class="btn btn-sm btn-outline-primary fw-medium px-3" type="button" data-bs-toggle="collapse" data-bs-target="#painelNovoDocumento" aria-expanded="false" aria-controls="painelNovoDocumento">
            <i class="fa-solid fa-plus me-1"></i>Anexar Novo Documento
        </button>
    </div>

    <div class="collapse mb-4 mt-3" id="painelNovoDocumento">
        <div class="card card-body bg-white border border-primary border-opacity-25 shadow-sm rounded p-4">
            <h6 class="fw-bold text-primary mb-4"><i class="fa-solid fa-file-circle-plus me-2"></i>Registar Novo Documento</h6>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium text-dark">Título / Nome do Documento <span class="text-danger">*</span></label>
                    <input type="text" name="doc_titulo" class="form-control border-0 bg-light shadow-sm" id="novoDocTitulo" placeholder="Ex: Contrato de Manutenção 2026">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium text-dark">Categoria <span class="text-danger">*</span></label>
                    <select name="doc_categoria" class="form-select border-0 bg-light shadow-sm" id="novoDocCategoria">
                        <option value="" selected disabled>Selecione...</option>
                        <option value="Manual de Utilizador">Manual de Utilizador</option>
                        <option value="Manual de Serviço">Manual de Serviço</option>
                        <option value="Certificado de Calibração">Certificado de Calibração</option>
                        <option value="Contrato de Manutenção">Contrato de Manutenção</option>
                        <option value="Fatura ou Guia de Aquisição">Fatura ou Guia de Aquisição</option>
                        <option value="Declaração de Conformidade">Declaração de Conformidade</option>
                        <option value="Relatório Técnico">Relatório Técnico</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium text-dark">Ficheiro (PDF/JPG) <span class="text-danger">*</span></label>
                    <input type="file" name="doc_ficheiro" class="form-control border-0 bg-light shadow-sm" id="novoDocFicheiro">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-dark">Entidade Responsável / Fornecedor</label>
                    <select name="doc_fornecedor" class="form-select border-0 bg-light shadow-sm" id="novoDocFornecedor">
                        <option value="" selected disabled>Selecione...</option>
                        <option value="1">Dräger Medical</option>
                        <option value="2">Philips Healthcare</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-dark">Validade / Término</label>
                    <input type="date" name="doc_validade" class="form-control border-0 bg-light shadow-sm" id="novoDocValidade">
                </div>
                <div class="col-md-4 d-flex align-items-end pb-1">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="doc_alerta" value="sim" role="switch" id="alertaExp">
                        <label class="form-check-label fw-medium text-dark ms-1" for="alertaExp">
                            Ativar Alerta de Expiração
                        </label>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary fw-medium px-4" data-bs-toggle="collapse" data-bs-target="#painelNovoDocumento">
                        <i class="fa-solid fa-xmark me-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary fw-bold px-4" id="btnGuardarNovoDoc">
                        <i class="fa-solid fa-plus me-1"></i> Adicionar à Lista
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive bg-white border rounded shadow-sm" id="contentorTabelaDocs">
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
                <tr>
                    <td class="py-3 px-3 border-0 fw-medium text-dark">Contrato de Manutenção</td>
                    <td class="py-3 border-0">
                        <span class="d-block fw-medium">Contrato Dräger 2026</span>
                        <a href="#" class="badge bg-secondary bg-opacity-10 text-dark border px-2 py-1 text-decoration-none shadow-sm mt-1">
                            <i class="fa-solid fa-file-pdf text-danger me-1"></i> contrato_v500.pdf
                        </a>
                    </td>
                    <td class="py-3 border-0 text-muted small">15-03-2027</td>
                    <td class="py-3 pe-3 border-0 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-abrir-modal-remover-doc px-2" data-bs-toggle="modal" data-bs-target="#modalRemoverDocumento">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="text-center py-3 text-muted small d-none" id="msgSemDocs">
        <i class="fa-solid fa-circle-info me-1"></i> Nenhum documento anexado a este equipamento.
    </div>

    <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
        <button type="button" class="btn btn-light border px-4 fw-medium" onclick="document.getElementById('dados-tab').click();">
            <i class="fa-solid fa-arrow-left me-1"></i> Anterior
        </button>
        <button type="submit" class="btn btn-primary px-5 fw-bold btn-guardar">
            <i class="fa-regular fa-floppy-disk me-1"></i> Guardar Alterações
        </button>
    </div>

</div> </div> </form>
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

<div class="modal fade" id="modalDesvincularComponente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 text-center p-4">
            <div class="modal-body p-0">
                <i class="fa-solid fa-triangle-exclamation text-warning mb-3" style="font-size: 3rem;"></i>
                <h5 class="fw-medium text-dark mb-1">Deseja desvincular este componente?</h5>
                <h4 class="fw-bold text-dark mb-4" id="textoComponenteModal">Nome do Componente</h4>
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="button" class="btn btn-light border px-4 fw-medium" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-1"></i> Não
                    </button>
                    <button type="button" class="btn btn-danger px-4 fw-bold" id="btnConfirmarDesvincular">
                        <i class="fa-solid fa-check me-1"></i> Sim
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
                <h5 class="text-dark mb-2">Deseja eliminar este consumível?</h5>
                <h3 class="fw-bold text-dark mb-4" id="textoConsumivelModal">Nome do Consumível</h3>
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="button" class="btn btn-light border fw-medium px-4 py-2" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-2 text-secondary"></i> Não
                    </button>
                    <button type="button" class="btn btn-danger fw-medium px-4 py-2" id="btnConfirmarRemoverConsumivel">
                        <i class="fa-solid fa-check me-2"></i> Sim
                    </button>
                </div>
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

<div class="toast-container position-fixed top-0 end-0 p-4" style="z-index: 1055;">
    <div id="toastGravacao" class="toast align-items-center text-bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center">
                <i class="fa-solid fa-circle-check fs-5 me-2 text-white"></i>
                <span class="fw-medium text-white">Dados do equipamento atualizados com sucesso!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>   