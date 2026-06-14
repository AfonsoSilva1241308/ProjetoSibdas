<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Equipamento — MediLink Digital</title>
    
    <link rel="shortcut icon" href="../../assets/img/logo1.png" type="image/png">
    <link rel="stylesheet" href="../../assets/Bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/fontawesome/all.min.css">
    <link rel="stylesheet" href="../../assets/css/1241308.css">
</head>
<body>

    <div class="d-flex vh-100">
        
       <div class="bg-white p-3 d-flex flex-column border-end" style="width: 260px; min-width: 260px;">
    <a href="../dashboard.html" class="d-flex align-items-center mb-4 text-decoration-none justify-content-center mt-2">
        <img src="../../assets/img/logo.png" alt="MediLink Digital" style="max-height: 45px;">
    </a>
    
    <ul class="nav nav-pills flex-column mb-auto gap-1 mt-2">
        <li class="nav-item">
            <a href="../dashboard.html" class="nav-link text-dark fw-medium px-3 py-2">Visão Geral</a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link active fw-semibold shadow-sm px-3 py-2" aria-current="page">Equipamentos</a>
        </li>
        <li class="nav-item">
            <a href="../localizacoes/lista.html" class="nav-link text-dark fw-medium px-3 py-2">Localizações</a>
        </li>
        <li class="nav-item">
            <a href="../manutencoes/lista.html" class="nav-link text-dark fw-medium px-3 py-2">Manutenção</a>
        </li>
        <li class="nav-item">
            <a href="../fornecedores/lista.html" class="nav-link text-dark fw-medium px-3 py-2">Fornecedores</a>
        </li>
        
        <li class="nav-item mt-2 pt-2 border-top border-secondary border-opacity-25">
            <a href="../definicoes/website.html" class="nav-link text-dark fw-medium px-3 py-2">Gestão de Site</a>
        </li>
    </ul>
    
    <div class="mt-4 pt-4 border-top border-secondary border-opacity-25">
        <a href="../../public/index.html" class="btn btn-outline-primary w-100 fw-bold">Sair do Sistema</a>
    </div>
</div>
        <div class="flex-grow-1 p-4 p-md-5 bg-light overflow-auto w-100">
            
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <a href="lista.html" class="btn btn-sm btn-outline-secondary px-3">
                        <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                    </a>
                    <h2 class="m-0 fw-bold text-primary">
                        <i class="fa-solid fa-circle-plus me-2"></i>Registar Novo Equipamento
                    </h2>
                </div>
                <div class="dropdown m-0">
                    <button class="btn btn-light border shadow-sm fw-medium text-secondary dropdown-toggle" type="button" id="dropdownUtilizador" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-user-circle me-1"></i> Administrador
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="dropdownUtilizador">
                        <li>
                            <a class="dropdown-item text-secondary fw-medium py-2" href="#" data-bs-toggle="modal" data-bs-target="#modalAlterarPassword">
                                <i class="fa-solid fa-key me-2 text-primary"></i> Alterar Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-secondary fw-medium py-2" href="../../public/index.html">
                                <i class="fa-solid fa-right-from-bracket me-2 text-primary"></i> Sair da Conta
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card w-100 shadow-sm rounded border-top border-primary border-4" style="max-width: 1200px;">
                <div class="card-body p-4 p-md-5">
                    
                    <form id="formNovoEquipamento" enctype="multipart/form-data" action="detalhes.html" method="POST" novalidate>
                        
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
                                            <input type="text" name="codigo_interno" class="form-control border-0 bg-light shadow-sm" placeholder="Ex: EQ-001" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label fw-medium">Designação do Equipamento <span class="text-danger">*</span></label>
                                            <input type="text" name="designacao" class="form-control border-0 bg-light shadow-sm" placeholder="Ex: Ventilador Pulmonar" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Categoria / Grupo</label>
                                            <select name="categoria" class="form-select border-0 bg-light shadow-sm">
                                                <option value="" selected disabled>Selecione...</option>
                                                <option value="monitorizacao">Monitorização</option>
                                                <option value="suporte_vida">Suporte de Vida</option>
                                                <option value="terapia">Terapia</option>
                                                <option value="diagnostico">Diagnóstico</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Marca</label>
                                            <input type="text" name="marca" class="form-control border-0 bg-light shadow-sm" placeholder="Ex: Philips">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Modelo</label>
                                            <input type="text" name="modelo" class="form-control border-0 bg-light shadow-sm" placeholder="Ex: IntelliVue MP5">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Número de Série</label>
                                            <input type="text" name="numero_serie" class="form-control border-0 bg-light shadow-sm" placeholder="Ex: SN-123456">
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
                                            <input type="text" name="fabricante" class="form-control border-0 bg-light shadow-sm" placeholder="Ex: Dräger">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Data de Aquisição</label>
                                            <input type="date" name="data_aquisicao" class="form-control border-0 bg-light shadow-sm">
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
                                            <input type="number" name="ano_fabrico" class="form-control border-0 bg-light shadow-sm" min="1990" max="2026" placeholder="YYYY">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Custo de Aquisição (€)</label>
                                            <input type="number" name="custo" class="form-control border-0 bg-light shadow-sm" step="0.01" placeholder="0.00">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Estado Atual <span class="text-danger">*</span></label>
                                            <select  name="estado" class="form-select border-success shadow-sm" required>
                                    <option value="ativo" selected>Ativo</option>
                                    <option value="manutencao">Em Manutenção</option>
                                    <option value="inativo">Inativo</option>
                                    <option value="abatido">Abatido</option>
                                    <option value="calibracao">Em Calibração</option>
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
                                                <option value="" selected disabled>Definir nível...</option>
                                                <option value="baixa">Baixa</option>
                                                <option value="media">Média</option>
                                                <option value="alta">Alta</option>
                                                <option value="suporte_vida">Suporte de Vida</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium" style="color: #6c757d;">Serviço / Departamento <span class="text-danger">*</span></label>
                                            <select  name="servico" class="form-select border-0 bg-light shadow-sm" required>
                                    <option value="" selected disabled>Selecione o serviço...</option>
                                    <option value="urgencia">Urgência Geral</option>
                                    <option value="uci">Cuidados Intensivos (UCI)</option>
                                    <option value="bloco">Bloco Operatório</option>
                                    <option value="imagiologia">Imagiologia</option>
                                    <option value="internamento">Internamento</option>
                                </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium" style="color: #6c757d;">Sala / Gabinete / Box <span class="text-danger">*</span></label>
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
                                            <label class="form-label fw-medium" style="color: #6c757d;">Observações Iniciais</label>
                                            <textarea name="observacoes" class="form-control border-0 bg-light shadow-sm" rows="3" placeholder="Informações adicionais relevantes..."></textarea>
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
                                                    <input type="text" name="cons_designacao" class="form-control border-0 bg-light shadow-sm" id="novoConsDesignacao" placeholder="Ex: Filtro HMEF">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium text-dark">Categoria <span class="text-danger">*</span></label>
                                                    <select name="cons_categoria" class="form-select border-0 bg-light shadow-sm" id="novoConsCategoria" required>
                                                        <option value="" selected disabled>Selecione a categoria...</option>
                                                        <option value="Materiais de Injeção e Punção">Materiais de Injeção e Punção</option>
                                                        <option value="Higiene e Descartáveis">Higiene e Descartáveis</option>
                                                        <option value="Eletrónica de Monitorização">Eletrónica de Monitorização</option>
                                                        <option value="Tubagens e Acessórios de Imagiologia/Fluídos">Tubagens e Acessórios de Imagiologia/Fluídos</option>
                                                        <option value="Agentes de Contraste e Desinfetantes">Agentes de Desinfeção</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-medium text-dark">Frequência <span class="text-danger">*</span></label>
                                                    <select name="cons_frequencia" class="form-select border-0 bg-light shadow-sm" id="novoConsFreq" required>
                                                        <option value="" selected disabled>Selecione...</option>
                                                        <option value="Por Paciente / Uso Único">Por Paciente</option>
                                                        <option value="Diário">Diário</option>
                                                        <option value="Mensal">Mensal</option>
                                                        <option value="Anual / Preventivo">Anual</option>
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

                                    <div class="table-responsive bg-white border rounded shadow-sm d-none" id="contentorTabelaConsumiveis">
                                        <table class="table align-middle mb-0">
                                            <thead>
                                                <tr class="text-muted small">
                                                    <th class="py-2 border-0 bg-light" style="width: 45%;">Designação</th>
                                                    <th class="py-2 border-0 bg-light" style="width: 25%;">Categoria</th>
                                                    <th class="py-2 border-0 bg-light" style="width: 20%;">Frequência</th>
                                                    <th class="py-2 border-0 bg-light text-end" style="width: 10%;">Ação</th>
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
                                    <a href="lista.html" class="btn btn-outline-secondary px-4 fw-medium">
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
                                        <input type="date" name="inicio_garantia" class="form-control bg-light border-0 shadow-sm">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-medium text-secondary">Fim da Garantia</label>
                                        <input type="date" name="fim_garantia" class="form-control bg-light border-0 shadow-sm">
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
                                        <label class="form-label fw-medium text-secondary">Entidade Responsável</label>
                                        <select name="entidade_responsavel" class="form-select bg-light border-0 shadow-sm">
                                            <option value="" selected disabled>Selecione...</option>
                                            <option value="draeger">Dräger Medical GmbH</option>
                                            <option value="philips">Philips Healthcare</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium text-secondary">Periodicidade</label>
                                        <select name="periodicidade" class="form-select bg-light border-0 shadow-sm">
                                            <option value="" selected disabled>Selecione...</option>
                                            <option value="mensal">Mensal</option>
                                            <option value="semestral">Semestral</option>
                                            <option value="anual">Anual</option>
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
                    <input type="text" name="doc_titulo" class="form-control border-0 bg-light shadow-sm" id="novoDocTitulo" placeholder="Ex: Manual de Utilizador V500">
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
                        <option value="3">Siemens Healthineers</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium text-dark">Validade / Término</label>
                    <input name="doc_validade" type="date" class="form-control border-0 bg-light shadow-sm" id="novoDocValidade">
                </div>
                <div class="col-md-4 d-flex align-items-end pb-1">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="doc_alerta" value="sim" role="switch" id="alertaExp" style="cursor: pointer;">
                        <label class="form-check-label fw-medium text-dark ms-1" for="alertaExp" style="cursor: pointer;">
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

    <script src="../../assets/Bootstrap/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/1241308.js"></script>
</body>
</html>