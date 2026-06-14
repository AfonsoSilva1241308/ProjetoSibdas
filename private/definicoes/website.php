<?php include '../includes/header.php'; ?>

    <div class="d-flex vh-100">
        <?php include '../includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4 p-md-5 bg-light overflow-auto w-100">
            
            <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-3">
                <div class="d-flex align-items-center">
                    <h2 class="fw-bold text-primary mb-0">Gestão de Conteúdos</h2>
                </div>
                <div class="dropdown m-0">
                    <button class="btn btn-light border shadow-sm d-flex align-items-center gap-2 dropdown-toggle text-secondary fw-medium" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-user-circle text-secondary"></i> Administrador
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                        <li>
                            <a class="dropdown-item py-2 text-secondary fw-medium" href="#" data-bs-toggle="modal" data-bs-target="#modalAlterarPassword">
                                <i class="fa-solid fa-key text-primary me-2"></i> Alterar Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 text-secondary fw-medium" href="../../public/index.php">
                                <i class="fa-solid fa-arrow-right-from-bracket text-primary me-2"></i> Sair da Conta
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="accordion shadow-sm border-0 rounded-3 overflow-hidden mb-5" id="accordionGestaoSite">

                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header" id="headingTextos">
                        <button class="accordion-button fw-bold text-dark bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTextos" aria-expanded="true" aria-controls="collapseTextos">
                            <i class="fa-solid fa-align-left text-primary me-3"></i> 1. Textos Institucionais
                        </button>
                    </h2>
                    <div id="collapseTextos" class="accordion-collapse collapse show" aria-labelledby="headingTextos" data-bs-parent="#accordionGestaoSite">
                        <div class="accordion-body p-4 bg-white border-top">
                            <form action="atualizar_textos.php" method="POST" class="form-editar-conteudo">
                                <div class="mb-4">
                                    <label for="textoSobreNos" class="form-label fw-medium text-secondary">Sobre Nós</label>
                                    <textarea name="texto_sobre_nos" class="form-control bg-light border-0 shadow-sm" id="textoSobreNos" rows="3">Na MediLink Digital, desenvolvemos soluções de software robustas e especializadas em sistemas de informação hospitalar. O nosso foco é otimizar a gestão de equipamentos clínicos e garantir a evolução, segurança e conformidade do ecossistema tecnológico de saúde em Portugal.</textarea>
                                </div>
                                <div class="mb-4">
                                    <label for="textoSolucao" class="form-label fw-medium text-secondary">A Nossa Solução</label>
                                    <textarea name="texto_solucao" class="form-control bg-light border-0 shadow-sm" id="textoSolucao" rows="3">A nossa solução digital permite centralizar e organizar a gestão tecnológica dos dispositivos médicos numa única plataforma. Asseguramos uma monitorização contínua e uma gestão integrada ao longo de todo o ciclo de vida, desde a entrada em operação até à desativação técnica, garantindo a rastreabilidade e a eficiência operacional da Engenharia Clínica.</textarea>
                                </div>
                                <div class="mb-4">
                                    <label for="textoFuncionalidades" class="form-label fw-medium text-secondary">Funcionalidades da Plataforma</label>
                                    <textarea name="texto_funcionalidades" class="form-control bg-light border-0 shadow-sm" id="textoFuncionalidades" rows="2">Os equipamentos abrangidos incluem dispositivos médicos com diferentes níveis de criticidade clínica, integrando desde sistemas de monitorização até tecnologias críticas de suporte de vida.</textarea>
                                </div>
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Textos
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header" id="headingIndicadores">
                        <button class="accordion-button collapsed fw-bold text-dark bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseIndicadores" aria-expanded="false" aria-controls="collapseIndicadores">
                            <i class="fa-solid fa-chart-pie text-warning me-3"></i> 2. Indicadores e Gráficos
                        </button>
                    </h2>
                    <div id="collapseIndicadores" class="accordion-collapse collapse" aria-labelledby="headingIndicadores" data-bs-parent="#accordionGestaoSite">
                        <div class="accordion-body p-4 bg-white border-top">
                            <form action="atualizar_indicadores.php" method="POST" class="form-editar-conteudo">
                                <h6 class="text-dark fw-bold border-bottom pb-2 mb-3">Métricas Principais</h6>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label for="numInstituicoes" class="form-label fw-medium text-secondary">Instituições Alocadas</label>
                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text bg-light border-0"><i class="fa-solid fa-hospital text-primary"></i></span>
                                            <input type="number" name="num_instituicoes" class="form-control bg-light border-0" id="numInstituicoes" value="45">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="numDispositivos" class="form-label fw-medium text-secondary">Dispositivos Médicos</label>
                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text bg-light border-0"><i class="fa-solid fa-heart-pulse text-success"></i></span>
                                            <input type="number" name="num_dispositivos" class="form-control bg-light border-0" id="numDispositivos" value="1200">
                                        </div>
                                    </div>
                                </div>

                                <h6 class="text-dark fw-bold border-bottom pb-2 mb-3">Distribuição de Equipamentos (%)</h6>
                                <div class="row g-4 mb-3">
                                    <div class="col-md-3">
                                        <label for="percMonitorizacao" class="form-label fw-medium text-secondary">Monitorização</label>
                                        <div class="input-group shadow-sm">
                                            <input type="number" name="perc_monitorizacao" class="form-control bg-light border-0" id="percMonitorizacao" value="45">
                                            <span class="input-group-text bg-light border-0">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="percSuporteVida" class="form-label fw-medium text-secondary">Suporte de Vida</label>
                                        <div class="input-group shadow-sm">
                                            <input type="number" name="perc_suporte" class="form-control bg-light border-0" id="percSuporteVida" value="35">
                                            <span class="input-group-text bg-light border-0">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="percTerapia" class="form-label fw-medium text-secondary">Terapia</label>
                                        <div class="input-group shadow-sm">
                                            <input type="number" name="perc_terapia" class="form-control bg-light border-0" id="percTerapia" value="12">
                                            <span class="input-group-text bg-light border-0">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="percDiagnostico" class="form-label fw-medium text-secondary">Diagnóstico</label>
                                        <div class="input-group shadow-sm">
                                            <input type="number" name="perc_diagnostico" class="form-control bg-light border-0" id="percDiagnostico" value="8">
                                            <span class="input-group-text bg-light border-0">%</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-warning text-dark fw-bold px-4">
                                        <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Alterações
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header" id="headingImagens">
                        <button class="accordion-button collapsed fw-bold text-dark bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseImagens" aria-expanded="false" aria-controls="collapseImagens">
                            <i class="fa-solid fa-image text-info me-3"></i> 3. Banner Principal 
                        </button>
                    </h2>
                    <div id="collapseImagens" class="accordion-collapse collapse" aria-labelledby="headingImagens" data-bs-parent="#accordionGestaoSite">
                        <div class="accordion-body p-4 bg-white border-top">
                            <form action="atualizar_imagem.php" method="POST" enctype="multipart/form-data" class="form-editar-conteudo">
                                
                                <h6 class="text-dark fw-bold border-bottom pb-2 mb-4">Imagem de Fundo (Banner)</h6>
                                <div class="row g-4 align-items-center mb-3">
                                    <div class="col-md-8">
                                        <label for="uploadBanner" class="form-label fw-medium text-secondary">Carregar Nova Imagem</label>
                                        <input type="file" name="banner_imagem" class="form-control bg-light border-0 shadow-sm" id="uploadBanner" accept="image/png, image/jpeg, image/webp">
                                        <small class="text-muted mt-2 d-block">
                                            <i class="fa-solid fa-circle-info me-1"></i> Formatos suportados: JPG, PNG, WEBP. Tamanho recomendado: 1920x1080px.
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <p class="form-label fw-medium text-secondary mb-2">Imagem Atual</p>
                                        <div class="border rounded-3 overflow-hidden shadow-sm">
                                            <img src="../../assets/img/hospital.png" alt="Pré-visualização do Banner" class="img-fluid" style="height: 100px; width: 100%; object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-info text-white fw-bold px-4">
                                        <i class="fa-solid fa-upload me-2"></i>Atualizar Banner
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingContactos">
                        <button class="accordion-button collapsed fw-bold text-dark bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContactos" aria-expanded="false" aria-controls="collapseContactos">
                            <i class="fa-solid fa-address-card text-success me-3"></i> 4. Formulário e Contactos
                        </button>
                    </h2>
                    <div id="collapseContactos" class="accordion-collapse collapse" aria-labelledby="headingContactos" data-bs-parent="#accordionGestaoSite">
                        <div class="accordion-body p-4 bg-white border-top">
                            <form action="atualizar_contactos.php" method="POST" class="form-editar-conteudo">
                                
                                <h6 class="text-dark fw-bold border-bottom pb-2 mb-3">Secção de Fale Connosco</h6>
                                <div class="mb-4">
                                    <label for="tituloContacto" class="form-label fw-medium text-secondary">Título do Formulário</label>
                                    <input type="text" name="titulo_form" class="form-control bg-light border-0 shadow-sm mb-2" id="tituloContacto" value="Tem alguma dúvida? Fale connosco.">
                                    
                                    <label for="textoContacto" class="form-label fw-medium text-secondary mt-2">Mensagem de Apoio</label>
                                    <textarea name="texto_apoio" class="form-control bg-light border-0 shadow-sm" id="textoContacto" rows="2">A nossa equipa especializada está totalmente disponível para esclarecer qualquer questão relacionada com a plataforma, a sua implementação ou a gestão da engenharia clínica do seu hospital. Envie-nos a sua mensagem!</textarea>
                                </div>

                                <div class="row g-4 mb-3">
                                    <div class="col-12">
                                        <h6 class="text-dark fw-bold border-bottom pb-2 mb-3">Localização (Rodapé)</h6>
                                        <input type="text" name="morada_rua" class="form-control bg-light border-0 shadow-sm mb-2" id="moradaRua" value="Rua Dr. António Bernardino de Almeida, 431">
                                        <input type="text" name="morada_cod_postal" class="form-control bg-light border-0 shadow-sm" id="moradaCodPostal" value="4200-072, Porto">
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-dark fw-bold border-bottom pb-2 mb-3">Horário de Atendimento</h6>
                                        <label class="form-label small text-secondary mb-1">Dias Úteis</label>
                                        <input type="text" name="horario_semana" class="form-control bg-light border-0 shadow-sm mb-2" id="horarioSemana" value="2ª a 6ª Feira: 8h30 — 18h30">
                                        <label class="form-label small text-secondary mb-1">Fim de Semana</label>
                                        <input type="text" name="horario_fim_semana" class="form-control bg-light border-0 shadow-sm" id="horarioSabado" value="Sábado: 9h00 — 13h00">
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-dark fw-bold border-bottom pb-2 mb-3">Contactos Diretos</h6>
                                        <label class="form-label small text-secondary mb-1">Email</label>
                                        <input type="email" name="email_contato" class="form-control bg-light border-0 shadow-sm mb-2" id="emailPublico" value="geral@medilinkdigital.pt">
                                        <label class="form-label small text-secondary mb-1">Telefone</label>
                                        <input type="text" name="telefone_contato" class="form-control bg-light border-0 shadow-sm" id="telefonePublico" value="+351 220 121 022">
                                    </div>
                                </div>
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-success px-4">
                                        <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Contactos
                                    </button>
                                </div>
                            </form>
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

    <div class="toast-container position-fixed top-0 end-0 p-4" style="z-index: 1055;">
        <div id="toastGravacao" class="toast align-items-center text-bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center">
                    <i class="fa-solid fa-circle-check fs-5 me-2 text-white"></i>
                    <span class="fw-medium text-white">Conteúdo atualizado com sucesso no site público!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

 <?php include '../includes/footer.php'; ?>   