<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

// --- INÍCIO DO PASSO 4: LIGAÇÃO E QUERY ---
try {
    // 1. Ligar ao servidor do ISEP (nota a porta 10464)
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=10464;dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 2. Executar a Query adaptada à tua tabela 'equipamento'
    $resultados = $ligacao->query("SELECT * FROM equipamento")->fetchAll(PDO::FETCH_OBJ);
    $erro = '';

} catch (PDOException $err) {
    // 3. Em caso de erro (ex: servidor em baixo)
    $erro = "Aconteceu um erro na ligação à base de dados.";
    $resultados = []; // Array vazio para não quebrar a página
}

// 4. Fechar a ligação (Boa prática da Ficha 11)
$ligacao = null;
// --- FIM DO PASSO 4 ---

// Definir os detalhes dinâmicos da Navbar para esta página
$titulo_pagina = "Inventário de Equipamentos"; 
$icone_pagina = "fa-solid fa-stethoscope"; 
$subtitulo_pagina = "Consulte e monitorize os equipamentos médicos.";
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex vh-100">
    
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 bg-light overflow-auto w-100">
        
        <?php include '../includes/navbar.php'; ?> 
        
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <h4 class="m-0 fw-bold text-dark">Lista de Equipamentos</h4>
                    <div class="d-flex gap-2">
                        <a href="novo.php" class="btn btn-primary fw-semibold">+ Novo Equipamento</a>
                    </div>
                </div>

                <div class="card w-100 shadow-sm rounded border-0 mb-4" style="max-width: 1200px;">
                    <div class="card-body p-4 bg-white rounded">
                        <form action="lista.php" method="GET">
                            
                            <div class="row g-3 align-items-center">
                                <div class="col-md-7">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white border-end-0 text-primary">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </span>
                                        <input type="text" name="pesquisa" class="form-control border-start-0 ps-0" placeholder="Pesquisar por código, designação, marca, modelo ou nº de série...">
                                    </div>
                                </div>
                                <div class="col-md-5 d-flex justify-content-md-end gap-2">
                                    <div class="input-group shadow-sm w-auto">
                                        <span class="input-group-text bg-light text-muted small"><i class="fa-solid fa-arrow-down-a-z"></i></span>
                                        <select name="ordenacao" class="form-select border-start-0 text-muted" style="min-width: 140px;">
                                            <option value="recentes" selected>Mais Recentes</option>
                                            <option value="az">Designação (A-Z)</option>
                                            <option value="za">Designação (Z-A)</option>
                                            <option value="criticidade">Maior Criticidade</option>
                                        </select>
                                    </div>
                                    <button class="btn btn-outline-primary fw-medium shadow-sm text-nowrap" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosAvancados" aria-expanded="false">
                                        <i class="fa-solid fa-sliders me-2"></i>Filtros
                                    </button>
                                </div>
                            </div>

                            <div class="collapse" id="filtrosAvancados">
                                <div class="row g-3 pt-4 mt-3 border-top border-light">
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted text-uppercase fw-bold mb-1">Categoria</label>
                                        <select name="categoria" class="form-select form-select-sm">
                                            <option value="">Todas as categorias...</option>
                                            <option value="suporte_vida">Suporte de Vida</option>
                                            <option value="monitorizacao">Monitorização</option>
                                            <option value="terapia">Terapia</option>
                                            <option value="diagnostico">Diagnóstico</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small text-muted text-uppercase fw-bold mb-1">Estado</label>
                                        <select name="estado" class="form-select form-select-sm">
                                            <option value="">Todos...</option>
                                            <option value="ativo">Ativo</option>
                                            <option value="manutencao">Em Manutenção</option>
                                            <option value="calibracao">Em calibração</option>
                                            <option value="inativo">Inativo</option>
                                            <option value="abatido">Abatido</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small text-muted text-uppercase fw-bold mb-1">Criticidade</label>
                                        <select name="criticidade" class="form-select form-select-sm">
                                            <option value="">Todas...</option>
                                            <option value="alta">Alta</option>
                                            <option value="media">Média</option>
                                            <option value="baixa">Baixa</option>
                                            <option value="critico_suporte">Suporte de Vida</option>

                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small text-muted text-uppercase fw-bold mb-1">Serviço</label>
                                        <select name="servico" class="form-select form-select-sm">
                                            <option value="">Todos...</option>
                                            <option value="uci">Cuidados Intensivos</option>
                                            <option value="urgencia">Urgência</option>
                                            <option value="bloco">Bloco Operatório</option>
                                            <option value="imagiologia">Imagiologia</option>
                                            <option value="internamento">Internamento</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted text-uppercase fw-bold mb-1">Fornecedor</label>
                                        <select name="fornecedor" class="form-select form-select-sm">
                                            <option value="">Todos os fornecedores...</option>
                                            <option value="1">Dräger Medical</option>
                                            <option value="2">Philips Healthcare</option>
                                            <option value="3">Medtronic</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                                        <button type="reset" class="btn btn-sm btn-light border fw-medium px-4 text-secondary">
                                            <i class="fa-solid fa-eraser me-1"></i> Limpar Tudo
                                        </button>
                                        <button type="submit" class="btn btn-sm btn-primary fw-medium px-4">
                                            <i class="fa-solid fa-check-double me-1"></i> Aplicar Filtros
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                        </form>
                    </div>
                </div>

                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger text-center fw-bold mt-4" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i><?= $erro ?>
                    </div>

                <?php elseif (count($resultados) == 0): ?>
                    <div class="alert alert-info text-center mt-4" role="alert">
                        <i class="fa-solid fa-circle-info me-2"></i>Não existem equipamentos registados.
                    </div>

                <?php else: ?>
                    <div class="table-responsive shadow-sm rounded border bg-white mb-3">
                        <table id="tabela-equipamentos" class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small fw-bold">
                                <tr>
                                    <th scope="col" class="py-3 px-4 border-0">CÓDIGO INTERNO</th>
                                    <th scope="col" class="py-3 border-0">DESIGNAÇÃO</th>
                                    <th scope="col" class="py-3 border-0">MARCA / MODELO</th>
                                    <th scope="col" class="py-3 border-0">CRITICIDADE</th>
                                    <th scope="col" class="py-3 border-0">ESTADO ATUAL</th>
                                    <th scope="col" class="text-end py-3 px-4 border-0">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                
                                <?php foreach ($resultados as $item): ?>
                                <tr>
    <td class="fw-bold text-dark px-4 py-3"><?= htmlspecialchars($item->codigo_interno ?? 'N/A') ?></td>
    
    <td>
        <span class="d-block text-dark fw-medium"><?= htmlspecialchars($item->designacao ?? 'Sem nome') ?></span>
        <small class="text-muted"><?= htmlspecialchars($item->categoria ?? 'Equipamento') ?></small>
    </td>
    
    <td class="text-dark"><?= htmlspecialchars(($item->marca ?? '') . ' ' . ($item->modelo ?? '')) ?></td>
    
    <td>
        <?php
        $cor_criticidade = 'bg-secondary bg-opacity-10 text-secondary border-secondary'; // Cor Padrão
        $crit = strtolower($item->criticidade ?? '');
        if ($crit == 'alta' || $crit == 'suporte de vida') {
            $cor_criticidade = 'bg-danger bg-opacity-10 text-danger border-danger';
        } elseif ($crit == 'média' || $crit == 'media') {
            $cor_criticidade = 'bg-warning bg-opacity-10 text-dark border-warning';
        }
        ?>
        <span class="badge <?= $cor_criticidade ?> border rounded-pill px-3 py-1">
            <?= htmlspecialchars($item->criticidade ?? 'Padrão') ?>
        </span>
    </td>
    
    <td>
        <?php
        $cor_estado = 'bg-secondary'; // Cor Padrão
        $est = strtolower($item->estado ?? '');
        if ($est == 'ativo') {
            $cor_estado = 'bg-success';
        } elseif ($est == 'em manutenção' || $est == 'em calibração') {
            $cor_estado = 'bg-warning text-dark';
        } elseif ($est == 'inativo' || $est == 'abatido') {
            $cor_estado = 'bg-danger';
        }
        ?>
        <span class="badge <?= $cor_estado ?> rounded-pill px-3 py-1">
            <?= htmlspecialchars($item->estado ?? 'Indefinido') ?>
        </span>
    </td>
    
    <td class="text-end px-4">
        <a href="detalhes.php?id=<?= $item->id ?>" class="btn btn-sm btn-outline-primary px-2 me-1" title="Ver Ficha"><i class="fa-solid fa-eye"></i></a>
        <a href="editar.php?id=<?= $item->id ?>" class="btn btn-sm btn-outline-warning px-2 me-1" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
        <button type="button" class="btn btn-sm btn-outline-danger px-2" title="Remover" data-bs-toggle="modal" data-bs-target="#modalRemoverEquipamento"><i class="fa-solid fa-trash"></i></button>
    </td>
</tr>
                                <?php endforeach; ?>
                                
                            </tbody>
                        </table>
                    </div> 
                    
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <span class="text-muted small">Total de registos: <strong><?= count($resultados) ?></strong></span>
                        <nav>
                            <ul class="pagination pagination-sm m-0">
                                <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">Próxima</a></li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <div class="modal fade" id="modalRemoverEquipamento" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-body text-center p-5">
                        <i class="fa-solid fa-triangle-exclamation text-warning mb-4" style="font-size: 4rem;"></i>
                        <h5 class="text-dark mb-2">Deseja eliminar o equipamento do inventário?</h5>
                        <h3 class="fw-bold text-dark mb-4">Ventilador Pulmonar Evita V500</h3>
                        
                        <div class="mb-4">
                            <span class="d-block text-dark fw-bold mb-1" style="font-size: 0.95rem;">
                                Cód. Interno: <span class="text-secondary fw-medium">EQ-V500-01</span>
                            </span>
                            <span class="d-block text-dark fw-bold" style="font-size: 0.95rem;">
                                Serviço Alocado: <span class="text-secondary fw-medium">Cuidados Intensivos (UCI)</span>
                            </span>
                        </div>

                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <button type="button" class="btn btn-light border fw-medium px-4 py-2" data-bs-dismiss="modal">
                                <i class="fa-solid fa-xmark me-2 text-secondary"></i> Não
                            </button>
                            
                            <form action="apagar_equipamento.php" method="POST" class="m-0">
                                <input type="hidden" name="codigo_interno" value="EV500-2021">
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

    </div>
</div>
<?php include '../includes/footer.php'; ?>