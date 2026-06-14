
<?php include '../includes/header.php'; ?>


    <div class="d-flex vh-100">
            <?php include '../includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100">
            
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <a href="lista.php" class="btn btn-sm btn-outline-secondary px-3">
                        <i class="fa-solid fa-arrow-left me-1"></i> Voltar 
                    </a>
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
                            <a class="dropdown-item text-secondary fw-medium py-2" href="../../public/index.php">
                                <i class="fa-solid fa-right-from-bracket me-2 text-primary"></i> Sair da Conta
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="d-flex justify-content-center align-items-start mt-4 mb-5">
    <div class="card w-100 shadow-sm rounded border-top border-secondary border-4 h-auto" style="max-width: 1200px;">
        <div class="card-body p-4 p-md-5">
            
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                <div>
                    <h2 class="text-dark mb-1"><strong><i class="fa-solid fa-file-medical text-secondary me-2"></i> Ficha Técnico do Equipamento</strong></h2>
                    <p class="text-muted m-0">Código de Inventário Hospitalar: <span class="badge bg-dark fs-6 font-monospace">EV500-2021</span></p>
                </div>
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
                                <p class="fs-6 fw-medium text-dark mb-0">EV500-2021</p>
                            </div>
                            <div class="col-md-5">
                                <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Designação do Equipamento</span>
                                <p class="fs-6 fw-medium text-dark mb-0">Ventilador Pulmonar</p>
                            </div>
                            <div class="col-md-4">
                                <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Categoria / Grupo</span>
                                <p class="fs-6 fw-medium text-dark mb-0">Suporte de Vida</p>
                            </div>
                            <div class="col-md-4">
                                <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Marca</span>
                                <p class="fs-6 fw-medium text-dark mb-0">Dräger</p>
                            </div>
                            <div class="col-md-4">
                                <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Modelo</span>
                                <p class="fs-6 fw-medium text-dark mb-0">Evita V500</p>
                            </div>
                            <div class="col-md-4">
                                <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Número de Série</span>
                                <p class="fs-6 fw-medium text-dark mb-0">DRG-984372-V5</p>
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
                                <span class="text-dark fw-medium fs-6">Dräger Medical GmbH</span>
                            </div>
                            <div class="col-md-4">
                                <span class="d-block text-muted small fw-bold text-uppercase mb-1">Data de Aquisição</span>
                                <span class="text-dark fw-medium fs-6">15 de Março de 2021</span>
                            </div>
                            <div class="col-md-4">
                                <span class="d-block text-muted small fw-bold text-uppercase mb-1">Tipo de Entrada</span>
                                <span class="text-dark fw-medium fs-6">Compra</span>
                            </div>
                            <div class="col-md-4">
                                <span class="d-block text-muted small fw-bold text-uppercase mb-1">Ano de Fabrico</span>
                                <span class="text-dark fw-medium fs-6">2020</span>
                            </div>
                            <div class="col-md-4">
                                <span class="d-block text-muted small fw-bold text-uppercase mb-1">Custo de Aquisição</span>
                                <span class="text-dark fw-bold fs-6">24.500,00 €</span>
                            </div>
                            <div class="col-md-4">
                                <span class="d-block text-muted small fw-bold text-uppercase mb-1">Estado Atual</span>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2">
                                    <i class="fa-solid fa-circle-check me-1"></i> Ativo
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
                                <span class="text-dark fw-medium fs-6">Suporte de Vida</span>
                            </div>
                            <div class="col-md-4">
                                <span class="d-block text-muted small fw-bold text-uppercase mb-1">Serviço / Departamento</span>
                                <span class="text-dark fw-medium fs-6">Urgência Geral</span>
                            </div>
                            <div class="col-md-4">
                                <span class="d-block text-muted small fw-bold text-uppercase mb-1">Sala / Gabinete / Box</span>
                                <span class="text-dark fw-medium fs-6">Box 1</span>
                            </div>
                            <div class="col-12">
                                <span class="d-block text-muted small fw-bold text-uppercase mb-1">Observações / Notas Técnicas</span>
                                <p class="text-dark bg-light p-3 rounded shadow-sm border mb-0">
                                    Equipamento em pleno funcionamento. Última calibração realizada com sucesso em janeiro de 2026.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-dark m-0">
                                <i class="fa-solid fa-sitemap text-info me-2"></i>4. Componentes e Acessórios Associados
                            </h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1">Equipamento Principal</span>
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
                                    <tr>
                                        <td class="py-3 px-4 font-monospace text-muted small">EV500-2021-C01</td>
                                        <td class="py-3 fw-medium text-dark">Humidificador Aquecido MR850</td>
                                        <td class="py-3 text-center"><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Ativo</span></td>
                                        <td class="py-3 px-4 text-end">
                                            <a href="#" class="btn btn-sm btn-light border px-2 text-secondary" title="Ver ficha do componente"><i class="fa-solid fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-4 font-monospace text-muted small">EV500-2021-C02</td>
                                        <td class="py-3 fw-medium text-dark">Braço Articulado de Suporte</td>
                                        <td class="py-3 text-center"><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Ativo</span></td>
                                        <td class="py-3 px-4 text-end">
                                            <a href="detalhes_equipamento.novo" class="btn btn-sm btn-light border px-2 text-secondary" title="Ver ficha do componente">
    <i class="fa-solid fa-eye"></i>
</a>
                                        </td>
                                    </tr>
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
                                    <tr>
                                        <td class="py-3 px-4 fw-medium text-dark">
                                            <i class="fa-solid fa-lungs-virus text-secondary me-2 opacity-50"></i>Filtros Antibacterianos / Virais (HMEF)
                                        </td>
                                        <td class="py-3 text-muted small">Proteção Respiratória</td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-3 py-1">Por Paciente</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-4 fw-medium text-dark">
                                            <i class="fa-solid fa-pump-medical text-secondary me-2 opacity-50"></i>Circuitos Respiratórios Descartáveis
                                        </td>
                                        <td class="py-3 text-muted small">Tubulagem / Cânulas</td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-3 py-1">Por Paciente</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-4 fw-medium text-dark">
                                            <i class="fa-solid fa-plug-circle-bolt text-secondary me-2 opacity-50"></i>Células de Oxigénio (O2 Sensors)
                                        </td>
                                        <td class="py-3 text-muted small">Sensor Interno</td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1">Anual</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted fst-italic d-block"><i class="fa-solid fa-circle-info me-1"></i>O material de desgaste rápido não é inventariado individualmente. O seu fornecimento deve ser gerido através do economato/armazém.</small>
                    </div>

                </div> <div class="tab-pane fade" id="documentacao" role="tabpanel" aria-labelledby="documentacao-tab">
                    
                    <div class="card border-0 shadow-sm p-4 bg-white mb-4">
                        <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">
                            <i class="fa-solid fa-shield-halved text-success me-2"></i>6. Garantias e Contratos
                        </h5>
                        
                        <div class="row g-4">
                            <div class="col-md-4 border-end">
                                <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Período de Garantia</span>
                                <p class="m-0 fw-medium text-dark">
                                    <i class="fa-solid fa-calendar-days text-secondary me-1"></i> 15/03/2021 a 15/03/2027
                                </p>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill mt-2 px-3 py-1">
                                    Garantia Válida
                                </span>
                            </div>

                            <div class="col-md-4 border-end">
                                <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Contrato de Manutenção</span>
                                <p class="m-0 fw-bold text-dark">
                                    <i class="fa-solid fa-file-signature text-primary me-1"></i> Sim — Preventivo e Corretivo
                                </p>
                                <small class="text-muted d-block mt-1">Periodicidade de Revisão: <strong>Anual</strong></small>
                            </div>

                            <div class="col-md-4">
                                <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Entidade Responsável</span>
                                <p class="m-0 fw-medium text-dark">
                                    <i class="fa-solid fa-building text-secondary me-1"></i> Dräger Medical GmbH
                                </p>
                                <small class="text-muted d-block mt-1">Contacto Direto: suporte@draeger.pt</small>
                            </div>

                            <div class="col-12 mt-3 pt-3 border-top">
                                <span class="d-block text-muted small text-uppercase fw-semibold mb-1">Observações do Contrato</span>
                                <div class="p-3 bg-light rounded text-secondary small fst-italic">
                                    <i class="fa-solid fa-circle-info me-1 text-primary"></i> 
                                    O contrato cobre a substituição preventiva de células de O2, calibração anual rastreável de acordo com as normas ISO e assistência técnica corretiva com substituição de peças originais incluída em caso de avaria.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm p-4 bg-white mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                            <h5 class="fw-bold text-dark m-0">
                                <i class="fa-solid fa-folder-open text-primary me-2"></i>7. Documentação Associada
                            </h5>
                            <span class="badge bg-dark rounded-pill px-3 py-2 font-monospace">7 Categorias Analisadas</span>
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
                                    <tr>
                                        <td class="py-3 px-4">
                                            <span class="d-block fw-bold text-dark"><i class="fa-solid fa-book text-primary me-2"></i>Manual de Utilizador</span>
                                            <small class="text-muted">Instruções de operação clínica diária</small>
                                        </td>
                                        <td class="px-4">
                                            <span class="d-block fw-medium text-dark">manual_utilizador_v500.pdf</span>
                                            <small class="text-secondary d-block">Submetido em: 15/03/2021 | Por: Dräger</small>
                                        </td>
                                        <td class="text-center px-4">
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">Disponível</span>
                                        </td>
                                        <td class="text-end px-4">
                                           <a href="../../uploads/documentos/nome_do_ficheiro.pdf" download class="btn btn-sm btn-light border px-2 rounded" title="Transferir Ficheiro">
    <i class="fa-solid fa-download text-secondary"></i>
</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-4">
                                            <span class="d-block fw-bold text-dark"><i class="fa-solid fa-gears text-danger me-2"></i>Manual de Serviço</span>
                                            <small class="text-muted">Esquemas técnicos e engenharia de blocos</small>
                                        </td>
                                        <td class="px-4">
                                            <span class="d-block fw-medium text-dark">service_manual_evita_v5.pdf</span>
                                            <small class="text-secondary d-block">Submetido em: 15/03/2021 | Por: Dräger</small>
                                        </td>
                                        <td class="text-center px-4">
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">Disponível</span>
                                        </td>
                                        <td class="text-end px-4">
                                            <a href="../../uploads/documentos/nome_do_ficheiro.pdf" download class="btn btn-sm btn-light border px-2 rounded" title="Transferir Ficheiro">
    <i class="fa-solid fa-download text-secondary"></i>
</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-4">
                                            <span class="d-block fw-bold text-dark"><i class="fa-solid fa-scale-balanced text-warning me-2"></i>Certificado de Calibração</span>
                                            <small class="text-muted">Rastreabilidade e padrões metrológicos</small>
                                        </td>
                                        <td class="px-4">
                                            <span class="d-block fw-medium text-dark">cert_calibracao_2026_jan.pdf</span>
                                            <small class="text-secondary d-block">Emitido em: 10/01/2026 | Validade: 10/01/2027</small>
                                        </td>
                                        <td class="text-center px-4">
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">Válido</span>
                                        </td>
                                        <td class="text-end px-4">
                                            <a href="../../uploads/documentos/nome_do_ficheiro.pdf" download class="btn btn-sm btn-light border px-2 rounded" title="Transferir Ficheiro">
    <i class="fa-solid fa-download text-secondary"></i>
</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-4">
                                            <span class="d-block fw-bold text-dark"><i class="fa-solid fa-file-contract text-info me-2"></i>Contrato de Manutenção</span>
                                            <small class="text-muted">Apólice de assistência e SLAs</small>
                                        </td>
                                        <td class="px-4">
                                            <span class="d-block fw-medium text-dark">contrato_manut_integral_draeger.pdf</span>
                                            <small class="text-secondary d-block">Vigência até: 15/03/2027</small>
                                        </td>
                                        <td class="text-center px-4">
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">Ativo</span>
                                        </td>
                                        <td class="text-end px-4">
                                            <a href="../../uploads/documentos/nome_do_ficheiro.pdf" download class="btn btn-sm btn-light border px-2 rounded" title="Transferir Ficheiro">
    <i class="fa-solid fa-download text-secondary"></i>
</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-4">
                                            <span class="d-block fw-bold text-dark"><i class="fa-solid fa-receipt text-secondary me-2"></i>Fatura ou Guia de Aquisição</span>
                                            <small class="text-muted">Comprovativo financeiro e registo patrimonial</small>
                                        </td>
                                        <td class="px-4">
                                            <span class="d-block fw-medium text-dark">fatura_compra_2021_4402.pdf</span>
                                            <small class="text-secondary d-block">Data do documento: 12/03/2021</small>
                                        </td>
                                        <td class="text-center px-4">
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">Disponível</span>
                                        </td>
                                        <td class="text-end px-4">
                                            <a href="../../uploads/documentos/nome_do_ficheiro.pdf" download class="btn btn-sm btn-light border px-2 rounded" title="Transferir Ficheiro">
    <i class="fa-solid fa-download text-secondary"></i>
</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-4">
                                            <span class="d-block fw-bold text-dark"><i class="fa-solid fa-certificate" style="color: #6f42c1;"></i>Declaração de Conformidade</span>
                                            <small class="text-muted">Marcação CE e conformidade com diretivas médicas</small>
                                        </td>
                                        <td class="px-4">
                                            <span class="d-block fw-medium text-dark">ce_declaration_evita_v500.pdf</span>
                                            <small class="text-secondary d-block">Registo do fabricante original</small>
                                        </td>
                                        <td class="text-center px-4">
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">Disponível</span>
                                        </td>
                                        <td class="text-end px-4">
                                            <a href="../../uploads/documentos/nome_do_ficheiro.pdf" download class="btn btn-sm btn-light border px-2 rounded" title="Transferir Ficheiro">
    <i class="fa-solid fa-download text-secondary"></i>
</a>
                                        </td>
                                    </tr>
                                    <tr class="table-light bg-opacity-10">
                                        <td class="py-3 px-4">
                                            <span class="d-block fw-bold text-muted"><i class="fa-solid fa-clipboard-check text-muted me-2"></i>Relatório Técnico</span>
                                            <small class="text-muted">Relatórios avulsos de peritagem ou modificação</small>
                                        </td>
                                        <td class="px-4 text-muted fst-italic small">
                                            <i class="fa-solid fa-ban me-1 text-secondary"></i> Nenhum relatório técnico arquivado para este dispositivo.
                                        </td>
                                        <td class="text-center px-4">
                                            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">Opcional</span>
                                        </td>
                                        <td class="text-end px-4">
                                            <button class="btn btn-sm btn-light border px-2 rounded disabled" disabled><i class="fa-solid fa-download text-muted"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div> </div> </div> </div> </div><div class="modal fade" id="modalAlterarPassword" tabindex="-1" aria-hidden="true">
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
<div class="modal fade" id="modalArquivarDocumento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-5">
                <i class="fa-solid fa-triangle-exclamation text-danger mb-4" style="font-size: 4rem;"></i>
                <h5 class="text-dark mb-2">Deseja remover este documento?</h5>
                <p class="text-muted mb-4">Esta ação irá desassociar o ficheiro deste equipamento.</p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-light border fw-medium px-4 py-2" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger fw-medium px-4 py-2" data-bs-dismiss="modal">Remover Ficheiro</button>
                </div>
            </div>
        </div>
    </div>
</div>
   
<?php include '../includes/footer.php'; ?>