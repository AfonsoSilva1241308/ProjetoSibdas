
<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

// --- INÍCIO: LIGAÇÃO E QUERY ---
$pesquisa = trim($_GET['pesquisa'] ?? '');
$erro = '';
$resultados = [];

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /*
        SOFT DELETE:
        As localizações removidas continuam na base de dados,
        mas deixam de aparecer na lista porque têm deleted_at preenchido.
    */
    $sql = "
        SELECT 
            l.*, 
            COUNT(e.id) AS total_equipamentos
        FROM localizacao l
        LEFT JOIN equipamento e ON l.id = e.localizacao_id
        WHERE l.deleted_at IS NULL
    ";

    $params = [];

    if ($pesquisa !== '') {
        $sql .= "
            AND (
                l.edificio LIKE :pesquisa
                OR l.piso LIKE :pesquisa
                OR l.servico LIKE :pesquisa
                OR l.sala LIKE :pesquisa
            )
        ";
        $params[':pesquisa'] = '%' . $pesquisa . '%';
    }

    $sql .= "
        GROUP BY l.id
        ORDER BY l.id DESC
    ";

    $stmt = $ligacao->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_OBJ);

} catch (PDOException $err) {
    $erro = "Erro técnico: " . $err->getMessage();
    $resultados = [];
}

$ligacao = null;
// --- FIM: LIGAÇÃO E QUERY ---

// Variáveis da Navbar
$titulo_pagina = "Gestão de Localizações";
$icone_pagina = "fa-solid fa-location-dot";
$subtitulo_pagina = "Consulte e administre os edifícios, serviços e salas do hospital.";
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="d-flex vh-100">
    
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 bg-light overflow-auto w-100">
        
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'removida'): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                Localização removida da lista com sucesso. O registo continua guardado na base de dados.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['sucesso']) && $_GET['sucesso'] === 'inserido'): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                Localização registada com sucesso no sistema!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['sucesso']) && $_GET['sucesso'] === 'editada'): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                Localização atualizada com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['server_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <?= htmlspecialchars($_SESSION['server_error'], ENT_QUOTES, 'UTF-8') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['server_error']); ?>
        <?php endif; ?>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body p-4 p-md-5">
                
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <h3 class="m-0 fw-bold text-dark">Gestão de Localizações</h3>
                    
                    <div class="d-flex gap-2">
                        <form action="lista.php" method="GET" class="m-0 d-flex gap-2">
                            <input 
                                type="text" 
                                name="pesquisa" 
                                class="form-control" 
                                placeholder="Pesquisar edifício, serviço ou sala..." 
                                style="width: 250px;"
                                value="<?= htmlspecialchars($pesquisa, ENT_QUOTES, 'UTF-8') ?>"
                            >
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
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
                                <tr>
                                    <td colspan="5" class="text-center text-danger fw-bold py-4">
                                        <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                </tr>
                            <?php elseif (count($resultados) === 0): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fa-solid fa-circle-info me-2"></i>Não existem localizações registadas.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($resultados as $loc): ?>
                                    <?php
                                        $edificio = $loc->edificio ?? '';
                                        $piso = $loc->piso ?? '';
                                        $servico = $loc->servico ?? '';
                                        $sala = $loc->sala ?? '';
                                        $idEncriptado = urlencode(aes_encrypt($loc->id));
                                    ?>
                                    <tr>
                                        <td class="py-3">
                                            <span class="d-block fw-bold text-dark"><?= htmlspecialchars($edificio, ENT_QUOTES, 'UTF-8') ?></span>
                                            <small class="text-muted"><?= htmlspecialchars($piso, ENT_QUOTES, 'UTF-8') ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($servico, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($sala, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary rounded-pill px-3 py-1">
                                                <?= (int) $loc->total_equipamentos ?> alocados
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group gap-2">
                                                <a href="detalhes.php?id_localizacao=<?= $idEncriptado ?>" class="btn btn-sm btn-outline-primary px-2 rounded" title="Ver Detalhes">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="editar.php?id_localizacao=<?= $idEncriptado ?>" class="btn btn-sm btn-outline-warning px-2 rounded" title="Editar">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-outline-danger px-2 rounded" 
                                                    title="Remover" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalRemoverLocalizacao"
                                                    data-id="<?= $idEncriptado ?>"
                                                    data-servico="<?= htmlspecialchars($servico, ENT_QUOTES, 'UTF-8') ?>"
                                                    data-edificio="<?= htmlspecialchars($edificio, ENT_QUOTES, 'UTF-8') ?>"
                                                    data-piso="<?= htmlspecialchars($piso, ENT_QUOTES, 'UTF-8') ?>"
                                                    data-sala="<?= htmlspecialchars($sala, ENT_QUOTES, 'UTF-8') ?>"
                                                >
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
                    <span class="text-muted small">
                        Total de registos: <strong id="total-registos-loc"><?= count($resultados) ?></strong>
                    </span>
                    <nav>
                        <ul class="pagination pagination-sm m-0" id="paginacao-loc"></ul>
                    </nav>
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
                
                <h5 class="text-dark mb-2">Deseja remover esta localização da lista?</h5>
                <h3 class="fw-bold text-dark mb-4" id="modalNomeLocalizacao">Localização</h3>
                
                <div class="mb-4">
                    <span class="d-block text-dark fw-bold mb-1" style="font-size: 0.95rem;">
                        Edifício:
                        <span class="text-secondary fw-medium" id="modalEdificioLocalizacao">-</span>
                    </span>
                    <span class="d-block text-dark fw-bold" style="font-size: 0.95rem;">
                        Sala / Gabinete:
                        <span class="text-secondary fw-medium" id="modalSalaLocalizacao">-</span>
                    </span>
                </div>

                <p class="text-muted small mb-4">
                    Esta ação não apaga a localização da base de dados. Apenas a oculta da listagem através de soft delete.
                </p>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="button" class="btn btn-light border fw-medium px-4 py-2" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-2 text-secondary"></i> Não
                    </button>
                    
                    <form action="apagar_localizacao.php" method="POST" class="m-0">
                        <input type="hidden" name="id_localizacao" id="modalIdLocalizacao">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalRemoverLocalizacao');

    if (modal) {
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            const id = button.getAttribute('data-id');
            const servico = button.getAttribute('data-servico') || 'Localização';
            const edificio = button.getAttribute('data-edificio') || '-';
            const piso = button.getAttribute('data-piso') || '';
            const sala = button.getAttribute('data-sala') || '-';

            document.getElementById('modalIdLocalizacao').value = id;
            document.getElementById('modalNomeLocalizacao').textContent = servico;
            document.getElementById('modalEdificioLocalizacao').textContent = piso ? edificio + ' (' + piso + ')' : edificio;
            document.getElementById('modalSalaLocalizacao').textContent = sala;
        });
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>