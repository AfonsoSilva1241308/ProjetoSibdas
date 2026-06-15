<?php
// 1. Segurança sempre ativa
require_once '../includes/funcoes.php';
redirect_if_not_logged();

// 2. Definir os dados para a navbar
$titulo_pagina = "Gestão de Fornecedores"; 
$icone_pagina = "fa-solid fa-truck-medical"; // Ícone específico para fornecedores de saúde
$subtitulo_pagina = "Consulte e monitorize as entidades parceiras e fabricantes.";
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex vh-100">
    
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 bg-light overflow-auto w-100">
        
        <?php include '../includes/navbar.php'; ?>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold text-dark m-0">Lista de Entidades</h4>
                        <div class="d-flex gap-2">
    <form action="lista.php" method="GET" class="m-0 d-flex gap-2">
        <input type="text" name="pesquisa" class="form-control bg-light border-0" placeholder="Pesquisar por NIF, nome..." style="width: 250px;">
        <button type="submit" class="btn btn-light border-0"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
    <a href="novo.php" class="btn btn-primary fw-semibold">
        + Novo Fornecedor
    </a>
</div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-muted small fw-bold text-uppercase border-0 pb-3">NIF / Entidade</th>
                                    <th scope="col" class="text-muted small fw-bold text-uppercase border-0 pb-3">Categoria</th>
                                    <th scope="col" class="text-muted small fw-bold text-uppercase border-0 pb-3">Contacto Principal</th>
                                    <th scope="col" class="text-muted small fw-bold text-uppercase border-0 pb-3 text-center">Nível SLA</th>
                                    <th scope="col" class="text-muted small fw-bold text-uppercase border-0 pb-3 text-center">Estado</th>
                                    <th scope="col" class="text-muted small fw-bold text-uppercase border-0 pb-3 text-end pe-3">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="border-top">
                                <tr>
                                    <td class="py-3">
                                        <span class="d-block fw-bold text-dark">Dräger Portugal Lda</span>
                                        <small class="text-muted">NIF: 501234567</small>
                                    </td>
                                    <td><span class="text-dark">Fabricante </span></td>
                                    <td>
                                        <span class="d-block text-dark">suporte@draeger.pt</span>
                                        <small class="text-muted">+351 210 000 000</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge border border-danger text-danger rounded-pill px-3 py-1">Até 12h</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success rounded-pill px-3 py-1">Ativo</span>
                                    </td>
                                    <td class="text-end pe-3">
                                      <a href="detalhes.php" class="btn btn-sm btn-outline-primary px-2 me-1" title="Ver Ficha">
                                      <i class="fa-solid fa-eye"></i>
                                      </a>
    
                                      <a href="editar.php" class="btn btn-sm btn-outline-warning px-2 me-1" title="Editar">
                                      <i class="fa-solid fa-pen-to-square"></i>
                                      </a>
    
                                       <button type="button" class="btn btn-sm btn-outline-danger px-2" title="Remover" data-bs-toggle="modal" data-bs-target="#modalRemover">
                                       <i class="fa-solid fa-trash"></i>
                                       </button>
                                       </td>
                                </tr>
                                <tr>
                                    <td class="py-3">
                                        <span class="d-block fw-bold text-dark">MedTech Assist</span>
                                        <small class="text-muted">NIF: 509876543</small>
                                    </td>
                                    <td><span class="text-dark">Fornecedor de consumíveis</span></td>
                                    <td>
                                        <span class="d-block text-dark">geral@medtech.pt</span>
                                        <small class="text-muted">+351 222 111 333</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge border border-warning text-dark rounded-pill px-3 py-1">Até 24h</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success rounded-pill px-3 py-1">Ativo</span>
                                    </td>
                                    <td class="text-end pe-3">
                                      <a href="detalhes.php" class="btn btn-sm btn-outline-primary px-2 me-1" title="Ver Ficha">
                                      <i class="fa-solid fa-eye"></i>
                                      </a>
    
                                      <a href="editar.php" class="btn btn-sm btn-outline-warning px-2 me-1" title="Editar">
                                      <i class="fa-solid fa-pen-to-square"></i>
                                      </a>
    
                                       <button type="button" class="btn btn-sm btn-outline-danger px-2" title="Remover" data-bs-toggle="modal" data-bs-target="#modalRemover">
                                       <i class="fa-solid fa-trash"></i>
                                       </button>
                                       </td>
                                </tr>
                                <tr>
                                    <td class="py-3">
                                        <span class="d-block fw-bold text-dark">ConsumaMed Lda</span>
                                        <small class="text-muted">NIF: 512345678</small>
                                    </td>
                                    <td><span class="text-dark">Empresa de Assistência Técnica</span></td>
                                    <td>
                                        <span class="d-block text-dark">encomendas@consumamed.pt</span>
                                        <small class="text-muted">+351 213 444 555</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge border border-secondary text-secondary rounded-pill px-3 py-1">Até 72h</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill px-3 py-1">Inativo</span>
                                    </td>
                                    <td class="text-end pe-3">
                                      <a href="detalhes.php" class="btn btn-sm btn-outline-primary px-2 me-1" title="Ver Ficha">
                                      <i class="fa-solid fa-eye"></i>
                                      </a>
    
                                      <a href="editar.php" class="btn btn-sm btn-outline-warning px-2 me-1" title="Editar">
                                      <i class="fa-solid fa-pen-to-square"></i>
                                      </a>
    
                                       <button type="button" class="btn btn-sm btn-outline-danger px-2" title="Remover" data-bs-toggle="modal" data-bs-target="#modalRemover">
                                       <i class="fa-solid fa-trash"></i>
                                       </button>
                                       </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <span class="text-muted small">A mostrar 3 de 45 registos</span>
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
<div class="modal fade" id="modalRemover" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body text-center p-5">
                    
                    <i class="fa-solid fa-triangle-exclamation text-warning mb-4" style="font-size: 4rem;"></i>
                    
                    <h5 class="text-dark mb-2">Deseja eliminar o fornecedor?</h5>
                    <h3 class="fw-bold text-dark mb-4">Dräger Portugal Lda</h3>
                    
                    <div class="mb-4">
                        <span class="d-block text-dark fw-bold mb-1">
                            <i class="fa-solid fa-envelope me-2"></i> suporte@draeger.pt
                        </span>
                        <span class="d-block text-dark fw-bold">
                            <i class="fa-solid fa-phone me-2"></i> +351 210 000 000
                        </span>
                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-4">
    <button type="button" class="btn btn-light border fw-medium px-4 py-2" data-bs-dismiss="modal">
        <i class="fa-solid fa-xmark me-2 text-secondary"></i> Não
    </button>
    
    <form action="apagar_fornecedor.php" method="POST" class="m-0">
        <input type="hidden" name="nif_fornecedor" value="501234567">
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