<?php include '../includes/header.php'; ?>

    <div class="d-flex vh-100">
        <?php include '../includes/sidebar.php'; ?>

        <!-- Conteúdo Principal -->
        <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100">
            
            <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom">
                <div>
                    <h2 class="m-0 fw-bold text-primary" style="font-size: 2.2rem;">
                        <i class="fa-solid fa-screwdriver-wrench me-2"></i> Registo de Intervenções
                    </h2>
                    <p class="text-muted m-0 mt-2" style="font-size: 1.05rem;">Consulte o histórico de manutenções, reparações e calibrações de equipamentos.</p>
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

            <div class="card w-100 shadow-sm rounded border-0" style="max-width: 1200px;">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <h3 class="m-0 fw-bold text-dark">Histórico de Manutenções</h3>
                        
                        <div class="d-flex gap-3 align-items-center">
    <form action="lista.php" method="GET" class="m-0 d-flex gap-2">
        <input type="text" name="pesquisa" class="form-control" placeholder="Pesquisar intervenção ou equipamento..." style="min-width: 280px;">
        <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
</div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th class="py-3 text-uppercase fw-bold border-0 bg-light">Data</th>
                                    <th class="py-3 text-uppercase fw-bold border-0 bg-light">Equipamento</th>
                                    <th class="py-3 text-uppercase fw-bold border-0 bg-light">Tipo / Entidade</th>
                                    <th class="py-3 text-uppercase fw-bold border-0 bg-light text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <!-- Intervenção 1 -->
                                <tr>
                                    <td class="py-3">
                                        <span class="d-block fw-bold text-dark">12/05/2026</span>
                                        <small class="text-muted">ID: #INT-1024</small>
                                    </td>
                                    <td>
                                        <span class="d-block fw-bold text-dark">BMB-091-A</span>
                                        <small class="text-muted">Bomba de Infusão</small>
                                    </td>
                                    <td>
                                        <span class="d-block fw-medium text-warning"><i class="fa-solid fa-triangle-exclamation me-2"></i>Corretiva (Avaria)</span>
                                        <small class="text-muted">Técnico: <span class="text-dark">Dep. Engenharia Clínica</span></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1">Em Curso</span>
                                    </td>
                                </tr>
                                <!-- Intervenção 2 -->
                                <tr>
                                    <td class="py-3">
                                        <span class="d-block fw-bold text-dark">05/05/2026</span>
                                        <small class="text-muted">ID: #INT-1018</small>
                                    </td>
                                    <td>
                                        <span class="d-block fw-bold text-dark">EV500-2021</span>
                                        <small class="text-muted">Ventilador Pulmonar</small>
                                    </td>
                                    <td>
                                        <span class="d-block fw-medium text-primary"><i class="fa-solid fa-shield-halved me-2"></i>Preventiva (Revisão)</span>
                                        <small class="text-muted">Entidade: <span class="text-dark">Dräger Medical</span></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Concluída</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top text-muted small">
                        <span>A mostrar 2 de 2 registos</span>
                        <nav>
    <ul class="pagination pagination-sm m-0">
        <li class="page-item disabled">
            <a class="page-link text-muted" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
        </li>
        <li class="page-item active" aria-current="page">
            <a class="page-link bg-primary border-primary" href="?pagina=1">1</a>
        </li>
        <li class="page-item">
            <a class="page-link text-primary" href="?pagina=2">Próxima</a>
        </li>
    </ul>
</nav>
                    </div>

                </div>
            </div>
        </div>
    </div>
<!-- Modal Alterar Password -->
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
                <form action="#" method="POST">
                    
                    <div class="mb-3">
    <label class="form-label small fw-bold text-muted text-uppercase">Password Atual</label>
    <div class="input-group shadow-sm">
        <input type="password" class="form-control bg-light border-end-0" id="passAtual" placeholder="Introduza a password atual" required>
        <!-- Removido btn-outline-secondary, adicionado bg-light -->
        <button class="btn bg-light border border-start-0 text-muted px-3" type="button" onclick="togglePassword('passAtual', 'iconAtual')">
            <i class="fa-solid fa-eye" id="iconAtual"></i>
        </button>
    </div>
</div>

<!-- Nova Password -->
<div class="mb-3">
    <label class="form-label small fw-bold text-muted text-uppercase">Nova Password</label>
    <div class="input-group shadow-sm">
        <input type="password" class="form-control border-end-0" id="passNova" placeholder="Mínimo 8 caracteres" required>
        <!-- Removido btn-outline-secondary, adicionado bg-white -->
        <button class="btn bg-white border border-start-0 text-muted px-3" type="button" onclick="togglePassword('passNova', 'iconNova')">
            <i class="fa-solid fa-eye" id="iconNova"></i>
        </button>
    </div>
</div>

<!-- Confirmar Nova Password -->
<div class="mb-4">
    <label class="form-label small fw-bold text-muted text-uppercase">Confirmar Nova Password</label>
    <div class="input-group shadow-sm">
        <input type="password" class="form-control border-end-0" id="passConfirma" placeholder="Repita a nova password" required>
        <!-- Removido btn-outline-secondary, adicionado bg-white -->
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