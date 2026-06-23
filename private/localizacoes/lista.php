
<?php
// 1. Trancar a porta aos intrusos
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

// --- INÍCIO: LIGAÇÃO E QUERY ---
try {
    // Ligar ao servidor do ISEP com a porta 10464
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=10464;dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query para ir buscar as localizações e contar os equipamentos lá alocados
    // NOTA: Se houver erro de sintaxe, o bloco catch abaixo vai mostrar-te exatamente o que é
    $sql = "SELECT l.*, COUNT(e.id) as total_equipamentos 
            FROM localizacao l 
            LEFT JOIN equipamento e ON l.id = e.localizacao_id 
            GROUP BY l.id";
            
    $resultados = $ligacao->query($sql)->fetchAll(PDO::FETCH_OBJ);
    $erro = '';

} catch (PDOException $err) {
    // Alterado para mostrar a mensagem de erro REAL da base de dados
    $erro = "Erro técnico: " . $err->getMessage();
    $resultados = [];
}
// Fechar a ligação
$ligacao = null;
// --- FIM: LIGAÇÃO E QUERY ---

// Variáveis da Navbar
$titulo_pagina = "Gestão de Localizações"; 
$icone_pagina = "fa-solid fa-stethoscope"; 
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
                        <table id="tabela-localizacoes" class="table align-middle mb-0">
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
        <?php if (!empty($erro)): ?>
            <tr><td colspan="5" class="text-center text-danger fw-bold py-4"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= $erro ?></td></tr>
        <?php elseif (count($resultados) == 0): ?>
            <tr><td colspan="5" class="text-center text-muted py-4"><i class="fa-solid fa-circle-info me-2"></i>Não existem localizações registadas.</td></tr>
        <?php else: ?>
            <?php foreach ($resultados as $loc): ?>
                <tr>
                    <td class="py-3">
                        <span class="d-block fw-bold text-dark"><?= htmlspecialchars($loc->edificio) ?></span>
                        <small class="text-muted"><?= htmlspecialchars($loc->piso) ?></small>
                    </td>
                    <td><?= htmlspecialchars($loc->servico) ?></td>
                    <td><?= htmlspecialchars($loc->sala) ?></td>
                    <td class="text-center">
                        <span class="badge bg-primary rounded-pill px-3 py-1"><?= $loc->total_equipamentos ?> alocados</span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group gap-2">
                            <a href="detalhes.php?id=<?= $loc->id ?>" class="btn btn-sm btn-outline-primary px-2 rounded" title="Ver Detalhes">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="editar.php?id=<?= $loc->id ?>" class="btn btn-sm btn-outline-warning px-2 rounded" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger px-2 rounded" title="Remover" data-bs-toggle="modal" data-bs-target="#modalRemoverLocalizacao">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top text-muted small">
    <span class="text-muted small">Total de registos: <strong id="total-registos-loc">0</strong></span>
    <nav>
        <ul class="pagination pagination-sm m-0" id="paginacao-loc">
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
<?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'inserido'): ?>
    <div class="toast-container position-fixed top-0 end-0 p-4" style="z-index: 1055;">
        <div id="toastSucessoLocalizacao" class="toast show align-items-center text-bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-medium fs-6">
                    <i class="fa-solid fa-circle-check me-2"></i> Localização registada com sucesso no sistema!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close" onclick="this.closest('.toast').classList.remove('show');"></button>
            </div>
        </div>
    </div>
    
    <script>
        setTimeout(function() {
            var toastEl = document.getElementById('toastSucessoLocalizacao');
            if (toastEl) {
                toastEl.classList.remove('show');
            }
        }, 4000);
    </script>
<?php endif; ?>
    <?php include '../includes/footer.php'; ?>