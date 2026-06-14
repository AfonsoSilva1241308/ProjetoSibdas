<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Localizações — MediLink Digital</title>
    
    <link rel="shortcut icon" href="../../assets/img/logo1.png" type="image/png">
    <link rel="stylesheet" href="../../assets/Bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/fontawesome/all.min.css">
    <link rel="stylesheet" href="../../assets/css/1241308.css">
</head>
<body class="bg-light">

    <div class="d-flex vh-100">
        
        <div class="bg-white p-3 d-flex flex-column border-end" style="width: 260px; min-width: 260px;">
            <a href="../dashboard.html" class="d-flex align-items-center mb-4 text-decoration-none justify-content-center mt-2">
                <img src="../../assets/img/logo.png" alt="MediLink Digital" style="max-height: 45px;">
            </a>
            
            <ul class="nav nav-pills flex-column mb-auto gap-2 mt-3">
                <li class="nav-item">
                    <a href="../dashboard.html" class="nav-link text-dark fw-medium px-3">
                        Visão Geral
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../equipamentos/lista.html" class="nav-link text-dark fw-medium px-3">
                        Equipamentos
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link active fw-semibold shadow-sm px-3" aria-current="page">
                        Localizações
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../manutencoes/lista.html" class="nav-link text-dark fw-medium px-3">
                        Manutenção
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../fornecedores/lista.html" class="nav-link text-dark fw-medium px-3">
                        Fornecedores
                    </a>
                </li>
                 <li class="nav-item mt-2 pt-2 border-top border-secondary border-opacity-25">
                   <a href="../definicoes/website.html" class="nav-link text-dark fw-medium px-3 py-2">Gestão de Site</a>
                </li>
            </ul>
            <hr class="text-muted mt-4">
            <div>
                <a href="../../public/index.html" class="btn btn-outline-primary w-100 fw-bold">
                    Sair do Sistema
                </a>
            </div>
        </div>

        <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100">
            
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h2 class="m-0 fw-bold text-primary"><i class="fa-solid fa-stethoscope me-2"></i>Gestão de Localizações</h2>
                    <p class="text-muted m-0 mt-1">Consulte e administre os edifícios, serviços e salas do hospital.</p>
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

            <div class="card shadow-sm border-0 rounded">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <h3 class="m-0 fw-bold text-dark">Gestão de Localizações</h3>
                        
                        <div class="d-flex gap-2">
    <form action="lista.html" method="GET" class="m-0 d-flex gap-2">
        <input type="text" name="pesquisa" class="form-control" placeholder="Pesquisar edifício, serviço ou sala..." style="width: 250px;">
        <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
    <a href="novo.html" class="btn btn-primary fw-semibold">+ Nova Localização</a>
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
                                            <a href="detalhes.html" class="btn btn-sm btn-outline-primary px-2 rounded" title="Ver Detalhes">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="editar.html" class="btn btn-sm btn-outline-warning px-2 rounded" title="Editar">
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
                                            <a href="detalhes.html" class="btn btn-sm btn-outline-primary px-2 rounded" title="Ver Detalhes">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="editar.html" class="btn btn-sm btn-outline-warning px-2 rounded" title="Editar">
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
    <script src="../../assets/Bootstrap/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/1241308.js"></script>
</body>
</html>