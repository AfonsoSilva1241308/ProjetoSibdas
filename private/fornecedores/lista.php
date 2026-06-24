<?php
// 1. Segurança sempre ativa
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../includes/funcoes.php';

redirect_if_not_logged();

function h($valor) {
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

// --- INÍCIO: LIGAÇÃO E QUERY À BASE DE DADOS ---
$pesquisa = trim($_GET['pesquisa'] ?? '');

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /*
        SOFT DELETE:
        A lista só mostra fornecedores que ainda não foram removidos.
        Os fornecedores removidos continuam na BD, mas ficam com deleted_at preenchido.
    */
    $sql = "
        SELECT *
        FROM fornecedor
        WHERE deleted_at IS NULL
    ";

    $params = [];

    if ($pesquisa !== '') {
        $sql .= "
            AND (
                nome LIKE :pesquisa
                OR nif LIKE :pesquisa
                OR tipo_fornecedor LIKE :pesquisa
                OR telefone LIKE :pesquisa
                OR email LIKE :pesquisa
            )
        ";

        $params[':pesquisa'] = '%' . $pesquisa . '%';
    }

    $sql .= " ORDER BY nome ASC";

    $stmt = $ligacao->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_OBJ);

    $erro = '';

} catch (PDOException $err) {
    $erro = "Erro técnico: " . $err->getMessage();
    $resultados = [];
}

$ligacao = null;
// --- FIM: LIGAÇÃO E QUERY ---

// 2. Definir os dados para a navbar
$titulo_pagina = "Gestão de Fornecedores";
$icone_pagina = "fa-solid fa-truck-medical";
$subtitulo_pagina = "Consulte e monitorize as entidades parceiras e fabricantes.";
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="d-flex vh-100">

    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 bg-light overflow-auto w-100">

        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-dark m-0">Lista de Entidades</h4>

                    <div class="d-flex gap-2">
                        <form action="lista.php" method="GET" class="m-0 d-flex gap-2">
                            <input
                                type="text"
                                name="pesquisa"
                                class="form-control bg-light border-0"
                                placeholder="Pesquisar por NIF, nome..."
                                style="width: 250px;"
                                value="<?= h($pesquisa) ?>"
                            >
                            <button type="submit" class="btn btn-light border-0">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </form>

                        <a href="novo.php" class="btn btn-primary fw-semibold">
                            + Novo Fornecedor
                        </a>
                    </div>
                </div>

                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        <?= h($erro) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'fornecedor_apagado'): ?>
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check me-2"></i>
                        Fornecedor removido da lista com sucesso. O registo continua guardado na base de dados.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['erro']) && $_GET['erro'] === 'apagar'): ?>
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        Não foi possível remover o fornecedor da lista. Tenta novamente.
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table id="tabela-fornecedores" class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small fw-bold">
                            <tr>
                                <th scope="col" class="py-3 px-4 border-0">FORNECEDOR</th>
                                <th scope="col" class="py-3 border-0 text-center">TIPO DE FORNECIMENTO</th>
                                <th scope="col" class="py-3 border-0 text-center">CONTACTO PRINCIPAL</th>
                                <th scope="col" class="text-end py-3 px-4 border-0">AÇÕES</th>
                            </tr>
                        </thead>

                        <tbody class="border-top-0">
                            <?php if (!empty($resultados)): ?>
                                <?php foreach ($resultados as $forn): ?>
                                    <tr>
                                        <td class="px-4 py-3">
                                            <span class="d-block fw-bold text-dark">
                                                <?= h($forn->nome ?? '') ?>
                                            </span>
                                            <small class="text-muted">
                                                NIF: <?= h($forn->nif ?? '') ?>
                                            </small>
                                        </td>

                                        <td class="text-center">
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill px-3 py-1">
                                                <?= h($forn->tipo_fornecedor ?? 'Não definido') ?>
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            <span class="d-block text-dark fw-medium">
                                                <i class="fa-solid fa-phone text-muted me-2 small"></i>
                                                <?= h($forn->telefone ?? 'N/A') ?>
                                            </span>
                                            <span class="d-block small text-muted">
                                                <i class="fa-solid fa-envelope me-2"></i>
                                                <?= h($forn->email ?? 'N/A') ?>
                                            </span>
                                        </td>

                                        <td class="text-end px-4">
                                            <div class="btn-group gap-2">
                                                <a
                                                    href="detalhes.php?id_fornecedor=<?= urlencode(aes_encrypt($forn->id)) ?>"
                                                    class="btn btn-sm btn-outline-primary px-2 rounded"
                                                    title="Ver Detalhes"
                                                >
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>

                                                <a
                                                    href="editar.php?id_fornecedor=<?= urlencode(aes_encrypt($forn->id)) ?>"
                                                    class="btn btn-sm btn-outline-warning px-2 rounded"
                                                    title="Editar"
                                                >
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger px-2 rounded"
                                                    title="Remover da lista"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalRemoverFornecedor"
                                                    data-id="<?= urlencode(aes_encrypt($forn->id)) ?>"
                                                    data-nome="<?= h($forn->nome ?? 'Fornecedor') ?>"
                                                    data-email="<?= h($forn->email ?? 'N/A') ?>"
                                                    data-telefone="<?= h($forn->telefone ?? 'N/A') ?>"
                                                >
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <?php if ($pesquisa !== ''): ?>
                                            Nenhum fornecedor encontrado para a pesquisa efetuada.
                                        <?php else: ?>
                                            Nenhum fornecedor ativo registado.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <span class="text-muted small">
                        Total de registos:
                        <strong id="total-registos-forn"><?= count($resultados) ?></strong>
                    </span>

                    <nav>
                        <ul class="pagination pagination-sm m-0" id="paginacao-forn"></ul>
                    </nav>
                </div>

            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modalRemoverFornecedor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-5">

                <i class="fa-solid fa-triangle-exclamation text-warning mb-4" style="font-size: 4rem;"></i>

                <h5 class="text-dark mb-2">Deseja remover este fornecedor da lista?</h5>
                <p class="text-muted small mb-3">
                    O fornecedor não será apagado da base de dados. Ficará apenas marcado como removido.
                </p>

                <h3 class="fw-bold text-dark mb-4" id="modalNomeFornecedor">
                    Fornecedor
                </h3>

                <div class="mb-4">
                    <span class="d-block text-dark fw-bold mb-1">
                        <i class="fa-solid fa-envelope me-2"></i>
                        <span id="modalEmailFornecedor">N/A</span>
                    </span>
                    <span class="d-block text-dark fw-bold">
                        <i class="fa-solid fa-phone me-2"></i>
                        <span id="modalTelefoneFornecedor">N/A</span>
                    </span>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="button" class="btn btn-light border fw-medium px-4 py-2" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-2 text-secondary"></i> Não
                    </button>

                    <form action="apagar_fornecedor.php" method="POST" class="m-0">
                        <input type="hidden" name="id_fornecedor" id="modalIdFornecedor">

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
        <div id="toastSucessoFornecedor" class="toast show align-items-center text-bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-medium fs-6">
                    <i class="fa-solid fa-circle-check me-2"></i> Fornecedor registado com sucesso no sistema!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close" onclick="this.closest('.toast').classList.remove('show');"></button>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalRemoverFornecedor');

    if (modal) {
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            if (!button) {
                return;
            }

            const id = button.getAttribute('data-id') || '';
            const nome = button.getAttribute('data-nome') || 'Fornecedor';
            const email = button.getAttribute('data-email') || 'N/A';
            const telefone = button.getAttribute('data-telefone') || 'N/A';

            document.getElementById('modalIdFornecedor').value = id;
            document.getElementById('modalNomeFornecedor').textContent = nome;
            document.getElementById('modalEmailFornecedor').textContent = email;
            document.getElementById('modalTelefoneFornecedor').textContent = telefone;
        });
    }

    const toastEl = document.getElementById('toastSucessoFornecedor');
    if (toastEl) {
        setTimeout(function () {
            toastEl.classList.remove('show');
        }, 4000);
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>