<?php include '../includes/header.php'; ?>

    <div class="d-flex vh-100">
            <?php include '../includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100">
            
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <a href="lista.php" class="btn btn-sm btn-outline-secondary px-3">&larr; Voltar</a>
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

            <div class="d-flex justify-content-center mt-4">
                <div class="card w-100 shadow-sm rounded border-top border-primary border-4" style="max-width: 1200px;">
                    <div class="card-body p-4 p-md-5">
                        
                        <h2 class="mb-4 text-primary"><strong><i class="fa-solid fa-pen-to-square me-2"></i> Atualização de Dados </strong></h2>
                        <p class="text-muted mb-4">A modificar o registo: <strong>Edifício Principal - UCI (Box 4)</strong></p>
                        <hr class="mb-5">

                        <form id="formNovaLocalizacao" action="lista.php" method="POST" novalidate>
    
    <input type="hidden" name="id_localizacao" value="1">

    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">Dados da Instalação</h5>
    
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <label class="form-label fw-medium">Edifício / Bloco <span class="text-danger">*</span></label>
            <input type="text" name="edificio" class="form-control" value="Edifício Principal" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-medium">Piso <span class="text-danger">*</span></label>
            <input type="text" name="piso" class="form-control" value="Piso 2" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-medium">Serviço / Departamento <span class="text-danger">*</span></label>
            <select name="servico" class="form-select" required>
                <option value="" disabled>Selecione o serviço...</option>
                <option value="urgencia">Urgência Geral</option>
                <option value="uci" selected>Cuidados Intensivos (UCI)</option>
                <option value="bloco">Bloco Operatório</option>
                <option value="imagiologia">Imagiologia</option>
                <option value="internamento">Internamento</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-medium">Sala / Gabinete</label>
            <select name="sala" class="form-select">
                <option value="" disabled>Selecione a sala...</option>
                <option value="box1">Box 1</option>
                <option value="box2">Box 2</option>
                <option value="box3">Box 3</option>
                <option value="box4" selected>Box 4</option>
                <option value="isolamento">Sala de Raio X</option>
                <option value="triagem">Triagem</option>
                <option value="gabinete_medico">Gabinete Médico</option>
            </select>
        </div>
    </div>

    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2 mt-4">Capacidade e Requisitos Técnicos</h5>
    
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <label class="form-label fw-medium">Capacidade Máxima (Equipamentos)</label>
            <input type="number" name="capacidade_maxima" class="form-control" value="15">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-medium">Infraestrutura Disponível</label>
            <select name="infraestrutura" class="form-select">
                <option value="ups_rede" selected>Tomadas UPS e Ponto de Rede Disponíveis</option>
                <option value="tomadas_normais">Apenas Tomadas Normais</option>
                <option value="ups_sem_rede">Tomadas UPS (Sem Rede)</option>
                <option value="sem_requisitos">Sem Requisitos Especiais</option>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label fw-medium">Observações Adicionais</label>
            <textarea name="observacoes" class="form-control" rows="3">Restrições de acesso biológico elevado. Necessita de fardamento específico para manutenção local.</textarea>
        </div>
    </div>

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
   <?php include '../includes/footer.php'; ?>