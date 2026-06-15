<?php
// 1. Segurança sempre no topo
require_once '../includes/funcoes.php';
redirect_if_not_logged();

// 2. Pedir à navbar para mostrar apenas o botão Voltar
$link_voltar = "lista.php"; 
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex vh-100">
    
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100 bg-light">
        
        <?php include '../includes/navbar.php'; ?>

            <div class="d-flex justify-content-center mt-4">
                <div class="card w-100 shadow-sm rounded border-top border-secondary border-4" style="max-width: 1200px;">
                    <div class="card-body p-4 p-md-5">
                        
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                            <div>
                                <h2 class="text-dark mb-1"><strong><i class="fa-solid fa-location-dot text-secondary me-2"></i> Detalhes da Localização</strong></h2>

                            </div>
                        </div>
                        <hr class="mb-5">

                        <div class="row g-4 mb-5">
                            <div class="col-12">
                                <h5 class="text-secondary fw-bold mb-3"><i class="fa-solid fa-building me-2"></i>Identificação do Espaço</h5>
                            </div>
                            <div class="col-md-3">
                                <span class="d-block text-muted small text-uppercase fw-semibold">Edifício / Bloco</span>
                                <p class="fs-5 fw-medium text-dark">Edifício Principal</p>
                            </div>
                            <div class="col-md-3">
                                <span class="d-block text-muted small text-uppercase fw-semibold">Piso</span>
                                <p class="fs-5 fw-medium text-dark">Piso 2</p>
                            </div>
                            <div class="col-md-3">
                                <span class="d-block text-muted small text-uppercase fw-semibold">Serviço / Dep.</span>
                                <p class="fs-5 fw-medium text-dark">Cuidados Intensivos (UCI)</p>
                            </div>
                            <div class="col-md-3">
                                <span class="d-block text-muted small text-uppercase fw-semibold">Sala / Gabinete</span>
                                <p class="fs-5 fw-medium text-dark">Box 4</p>
                            </div>
                        </div>

                        <div class="row g-4 mb-5 border-top pt-4">
                            <div class="col-12">
                                <h5 class="text-secondary fw-bold mb-3"><i class="fa-solid fa-plug me-2"></i>Infraestrutura e Lotação</h5>
                            </div>
                            <div class="col-md-6">
                                <span class="d-block text-muted small text-uppercase fw-semibold">Requisitos Elétricos/Rede</span>
                                <p class="m-0 mt-1"><span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-2 fs-6">Tomadas UPS e Ponto de Rede Disponíveis</span></p>
                            </div>
                            <div class="col-md-6">
                                <span class="d-block text-muted small text-uppercase fw-semibold">Capacidade Máxima</span>
                                <p class="fs-5 text-dark">15 Equipamentos</p>
                            </div>
                            <div class="col-12 mt-4">
                                <span class="d-block text-muted small text-uppercase fw-semibold mb-2">Observações Adicionais</span>
                                <div class="p-3 bg-light rounded border text-dark">
                                    Restrições de acesso biológico elevado. Necessita de fardamento específico para manutenção local.
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 border-top pt-4">
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                                <h5 class="text-secondary fw-bold m-0"><i class="fa-solid fa-heart-pulse me-2"></i>Equipamentos Alocados Atualmente</h5>
                                <span class="badge bg-primary rounded-pill px-3 py-1">3 Equipamentos</span>
                            </div>
                            
                            <div class="col-12">
                                <div class="table-responsive border rounded">
                                    <table class="table align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="small text-uppercase fw-bold text-muted border-0">Código Interno</th>
                                                <th class="small text-uppercase fw-bold text-muted border-0">Designação / Marca</th>
                                                <th class="small text-uppercase fw-bold text-muted border-0">Estado</th>
                                                <th class="small text-uppercase fw-bold text-muted border-0 text-end">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="font-monospace fw-bold text-dark">EV500-2021</td>
                                                <td>
                                                    <span class="d-block fw-medium">Ventilador Pulmonar</span>
                                                    <small class="text-muted">Dräger Evita V500</small>
                                                </td>
                                                <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">Operacional</span></td>
                                                <td class="text-end">
                                                    <a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-secondary" title="Ver Ficha do Equipamento">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font-monospace fw-bold text-dark">MON-883-X</td>
                                                <td>
                                                    <span class="d-block fw-medium">Monitor de Sinais Vitais</span>
                                                    <small class="text-muted">Philips IntelliVue</small>
                                                </td>
                                                <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">Operacional</span></td>
                                                <td class="text-end">
                                                    <a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-secondary" title="Ver Ficha do Equipamento">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font-monospace fw-bold text-dark">BMB-091-A</td>
                                                <td>
                                                    <span class="d-block fw-medium">Bomba de Infusão</span>
                                                    <small class="text-muted">B. Braun Space</small>
                                                </td>
                                                <td><span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2 py-1">Em Manutenção</span></td>
                                                <td class="text-end">
                                                    <a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-secondary" title="Ver Ficha do Equipamento">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
<?php include '../includes/footer.php'; ?>  