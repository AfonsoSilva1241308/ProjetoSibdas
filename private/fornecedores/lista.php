<?php
// 1. Segurança sempre ativa
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

// --- INÍCIO: LIGAÇÃO E QUERY À BASE DE DADOS ---
try {
    // A nossa ligação de sucesso com a porta 10464
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=10464;dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ir buscar os fornecedores à base de dados
    $sql = "SELECT * FROM fornecedor ORDER BY nome ASC";
    $resultados = $ligacao->query($sql)->fetchAll(PDO::FETCH_OBJ);
    $erro = '';

} catch (PDOException $err) {
    $erro = "Erro técnico: " . $err->getMessage();
    $resultados = []; // O array vazio impede que o foreach "parta" a página!
}
$ligacao = null;
// --- FIM: LIGAÇÃO E QUERY ---

// 2. Definir os dados para a navbar
$titulo_pagina = "Gestão de Fornecedores"; 
$icone_pagina = "fa-solid fa-truck-medical";
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
        <?php foreach ($resultados as $forn): ?>
            <tr>
                <td class="px-4 py-3">
                    <span class="d-block fw-bold text-dark"><?= htmlspecialchars($forn->nome ?? '') ?></span>
                    <small class="text-muted">NIF: <?= htmlspecialchars($forn->nif ?? '') ?></small>
                </td>
                <td class="text-center"> <span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill px-3 py-1">
                        <?= htmlspecialchars($forn->tipo_fornecedor ?? 'Não definido') ?>
                    </span>
                </td>
                <td class="text-center"> <span class="d-block text-dark fw-medium"><i class="fa-solid fa-phone text-muted me-2 small"></i><?= htmlspecialchars($forn->telefone ?? 'N/A') ?></span>
                    <span class="d-block small text-muted"><i class="fa-solid fa-envelope me-2"></i><?= htmlspecialchars($forn->email ?? 'N/A') ?></span>
                </td>
                <td class="text-end px-4">
                    <div class="btn-group gap-2">
                        <a href="detalhes.php?id=<?= $forn->id ?>" class="btn btn-sm btn-outline-primary px-2 rounded" title="Ver Detalhes"><i class="fa-solid fa-eye"></i></a>
                        <a href="editar.php?id=<?= $forn->id ?>" class="btn btn-sm btn-outline-warning px-2 rounded" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                        <button type="button" class="btn btn-sm btn-outline-danger px-2 rounded" title="Remover" data-bs-toggle="modal" data-bs-target="#modalRemoverFornecedor"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
    <span class="text-muted small">Total de registos: <strong id="total-registos-forn">0</strong></span>
    <nav>
        <ul class="pagination pagination-sm m-0" id="paginacao-forn">
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