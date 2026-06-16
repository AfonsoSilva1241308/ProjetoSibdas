<?php
// 1. Segurança e sessão
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

// 2. Configuração das variáveis da navbar
$link_voltar = "lista.php"; 
$titulo_pagina = "Registar Nova Localização";
$icone_pagina = "fa-solid fa-circle-plus"; 
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex vh-100">
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100 bg-light">
        
        <?php include '../includes/navbar.php'; ?>
        
            <div class="card w-100 shadow-sm rounded border-top border-primary border-4" style="max-width: 1200px;">
                <div class="card-body p-4 p-md-5">
                    
                    <form id="formNovaLocalizacao" action="lista.php" method="POST">
                        
                        <h5 class="text-dark mb-4 border-bottom pb-2">Dados da Instalação</h5>
                        
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
    <label class="form-label text-dark">Edifício / Bloco *</label>
    <input type="text" name="edificio" class="form-control" placeholder="Ex: Edifício Principal" required>
</div>
<div class="col-md-6">
    <label class="form-label text-dark">Piso *</label>
    <input type="text" name="piso" class="form-control" placeholder="Ex: Piso 2" required>
</div>
                            <div class="col-md-6">
    <label class="form-label text-dark">Serviço / Departamento *</label>
    <select name="servico" class="form-select" required>
        <option value="" selected disabled>Selecione o serviço...</option>
        <option value="urgencia">Urgência Geral</option>
        <option value="uci">Cuidados Intensivos (UCI)</option>
        <option value="bloco">Bloco Operatório</option>
        <option value="imagiologia">Imagiologia</option>
        <option value="internamento">Internamento</option>
    </select>
</div>
                            <div class="col-md-6">
    <label class="form-label text-dark">Sala / Gabinete</label>
    <select name="sala" class="form-select">
        <option value="" selected disabled>Selecione a sala...</option>
        <option value="box1">Box 1</option>
        <option value="box2">Box 2</option>
        <option value="box3">Box 3</option>
        <option value="isolamento">Sala de Raio X</option>
        <option value="triagem">Triagem</option>
        <option value="gabinete_medico">Gabinete Médico</option>
    </select>
</div>
                        </div>

                        <h5 class="text-dark mb-4 border-bottom pb-2 mt-4">Capacidade e Requisitos Técnicos</h5>
                        
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label class="form-label text-dark">Capacidade Máxima (Equipamentos)</label>
        <input type="number" name="capacidade_maxima" class="form-control" placeholder="Ex: 15">
    </div>
    <div class="col-md-6">
        <label class="form-label text-dark">Infraestrutura Disponível</label>
        <select name="infraestrutura" class="form-select">
            <option value="ups_rede" selected>Tomadas UPS e Ponto de Rede Disponíveis</option>
            <option value="tomadas_normais">Apenas Tomadas Normais</option>
            <option value="ups_sem_rede">Tomadas UPS (Sem Rede)</option>
            <option value="sem_requisitos">Sem Requisitos Especiais</option>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label text-dark">Observações Adicionais</label>
        <textarea name="observacoes" class="form-control" rows="3" placeholder="Restrições de acesso, notas para a equipa técnica, etc."></textarea>
    </div>
</div>
                        <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
                            <a href="lista.php" class="btn btn-light border px-4 d-flex align-items-center">
                                <i class="fa-solid fa-xmark me-2"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-4 d-flex align-items-center">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Registar Localização
                            </button>
                            <div class="alert alert-danger text-center d-none mt-3" role="alert" id="mensagemErro">
                              <i class="fa-solid fa-triangle-exclamation me-2"></i> Ocorreu um erro ao registar a localização. Verifique os dados.
                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
       <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
         <div id="sucessoToastLocalizacao" class="toast align-items-center text-bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body fw-medium fs-6">
                <i class="fa-solid fa-circle-check me-2"></i> Localização registada com sucesso!
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