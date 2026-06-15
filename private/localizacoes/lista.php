<?php
// 1. Trancar a porta aos intrusos
require_once '../includes/funcoes.php';
redirect_if_not_logged();

// 2. Passar as variáveis à nossa navbar
$titulo_pagina = "Gestão de Localizações"; 
$icone_pagina = "fa-solid fa-stethoscope"; // Nota: Se quiseres variar o ícone, experimenta usar "fa-solid fa-location-dot" ou "fa-solid fa-building"!
$subtitulo_pagina = "Consulte e administre os edifícios, serviços e salas do hospital.";
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex vh-100">
    
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 bg-light overflow-auto w-100">
        
        <?php include '../includes/navbar.php'; ?>

            <div class="card shadow-sm border-0 rounded">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <h3 class="m-0 fw-bold text-dark">Gestão de Localizações</h3>
                        
                        <div class="d-flex gap-2">
    <form action="lista.php" method="GET" class="m-0 d-flex gap-2">
        <input type="text" name="pesquisa" class="form-control" placeholder="Pesquisar edifício, serviço ou sala..." style="width: 250px;">
        <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
    <a href="novo.php" class="btn btn-primary fw-semibold">+ Nova Localização</a>
</div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th class="py-3 text-uppercase fw-bold border-0 bg-light">Edifício / Piso</th>
                                    <th class="py-3 text-uppercase fw-bold border-0 bg-light">Serviço / Departamento</th>
                                    <th class="py-3 text-uppercase fw-bold border-0 bg-light">Sala / Gabinete</th>
                                    <th class="py-3 text-uppercase fw-bold border-0 bg-light text-center">Equipamentos</th>
                                    <th class="py-3 text-uppercase fw-bold border-0 bg-light text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <tr>
                                    <td class="py-3">
                                        <span class="d-block fw-bold text-dark">Edifício Principal</span>
                                        <small class="text-muted">Piso 2</small>
                                    </td>
                                    <td>Cuidados Intensivos (UCI)</td>
                                    <td>Box 4</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill px-3 py-1">3 alocados</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group gap-2">
                                            <a href="detalhes.php" class="btn btn-sm btn-outline-primary px-2 rounded" title="Ver Detalhes">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="editar.php" class="btn btn-sm btn-outline-warning px-2 rounded" title="Editar">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger px-2 rounded" title="Remover" data-bs-toggle="modal" data-bs-target="#modalRemoverLocalizacao">
                                               <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3">
                                        <span class="d-block fw-bold text-dark">Edifício Sul</span>
                                        <small class="text-muted">Piso 0</small>
                                    </td>
                                    <td>Imagiologia</td>
                                    <td>Sala de RX 1</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill px-3 py-1">1 alocado</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group gap-2">
                                            <a href="detalhes.php" class="btn btn-sm btn-outline-primary px-2 rounded" title="Ver Detalhes">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="editar.php" class="btn btn-sm btn-outline-warning px-2 rounded" title="Editar">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger px-2 rounded" title="Remover" data-bs-toggle="modal" data-bs-target="#modalRemoverLocalizacao">
                                               <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
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
    </div>
<div class="modal fade" id="modalRemoverLocalizacao" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body text-center p-5">
                    
                    <i class="fa-solid fa-triangle-exclamation text-warning mb-4" style="font-size: 4rem;"></i>
                    
                    <h5 class="text-dark mb-2">Deseja eliminar esta localização do sistema?</h5>
                    <h3 class="fw-bold text-dark mb-4">Cuidados Intensivos (UCI)</h3>
                    
                    <div class="mb-4">
                        <span class="d-block text-dark fw-bold mb-1" style="font-size: 0.95rem;">
                            Edifício: <span class="text-secondary fw-medium">Edifício Principal (Piso 2)</span>
                        </span>
                        <span class="d-block text-dark fw-bold" style="font-size: 0.95rem;">
                            Sala / Gabinete: <span class="text-secondary fw-medium">Box 4</span>
                        </span>
                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-4">
    <button type="button" class="btn btn-light border fw-medium px-4 py-2" data-bs-dismiss="modal">
        <i class="fa-solid fa-xmark me-2 text-secondary"></i> Não
    </button>
    
    <form action="apagar_localizacao.php" method="POST" class="m-0">
        <input type="hidden" name="id_localizacao" value="1">
        <button type="submit" class="btn btn-danger fw-medium px-4 py-2">
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
    <?php include '../includes/footer.php'; ?>