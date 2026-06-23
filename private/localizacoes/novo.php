<?php
// 1. Segurança e sessão (Ficha 12 - Pág 3)
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../../config/config.php'; // Adicionado para ter acesso ao MYSQL_HOST, etc.
redirect_if_not_logged();

// 2. Preparar variáveis para controlo de erros (Ficha 12 - Pág 19)
$erros = [];
$erro_sistema = "";

// 3. Verificar se o formulário foi submetido via POST (Ficha 12 - Pág 4)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 3.1 Recolher e limpar os dados (Ficha 12 - Pág 4)
    $edificio          = trim($_POST['edificio'] ?? '');
    $piso              = trim($_POST['piso'] ?? '');
    $servico           = trim($_POST['servico'] ?? '');
    $sala              = trim($_POST['sala'] ?? '');
    $capacidade_maxima = trim($_POST['capacidade_maxima'] ?? '');
    $infraestrutura    = trim($_POST['infraestrutura'] ?? '');
    $observacoes       = trim($_POST['observacoes'] ?? '');

    // 3.2 Validação dos campos obrigatórios marcados com * (Ficha 12 - Pág 9)
    if (empty($edificio)) {
        $erros[] = "O campo Edifício / Bloco é obrigatório.";
    }
    if (empty($piso)) {
        $erros[] = "O campo Piso é obrigatório.";
    }
    if (empty($servico)) {
        $erros[] = "O Serviço / Departamento é obrigatório.";
    }

    // 3.3 Gravação na Base de Dados (Ficha 12 - Pág 21)
    if (empty($erros)) {
        try {
            $ligacao = new PDO(
                "mysql:host=" . MYSQL_HOST . ";port=10464;dbname=" . MYSQL_DATABASE . ";charset=utf8",
                MYSQL_USERNAME,
                MYSQL_PASSWORD
            );
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $ligacao->beginTransaction();

            $sql = "INSERT INTO localizacao (
                        edificio, piso, servico, sala, capacidade_maxima, infraestrutura, observacoes
                    ) VALUES (
                        :edificio, :piso, :servico, :sala, :capacidade, :infra, :obs
                    )";

            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':edificio'   => $edificio,
                ':piso'       => $piso,
                ':servico'    => $servico,
                ':sala'       => empty($sala) ? null : $sala,
                ':capacidade' => empty($capacidade_maxima) ? null : $capacidade_maxima,
                ':infra'      => empty($infraestrutura) ? null : $infraestrutura,
                ':obs'        => empty($observacoes) ? null : $observacoes
            ]);

            $ligacao->commit();
            
            // Sucesso - redireciona para a lista para mostrar o Toast (Ficha 12 - Pág 21)
            header("Location: lista.php?sucesso=inserido");
            exit;

        } catch (PDOException $err) {
            $ligacao->rollBack();
            // Capturar erro de sistema (Ficha 12 - Pág 19)
            $erro_sistema = "Erro ao gravar na Base de Dados: " . $err->getMessage();
        }
        $ligacao = null;
    }
}

// 4. Configuração das variáveis da navbar
$link_voltar = "lista.php"; 
$titulo_pagina = "Registar Nova Localização";
$icone_pagina = "fa-solid fa-circle-plus"; 
?>
<?php include '../includes/header.php'; ?>

<div class="d-flex vh-100">
    <?php include '../includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4 p-md-5 overflow-auto w-100 bg-light">
        
        <?php include '../includes/navbar.php'; ?>
        
            <div class="card w-100 shadow-sm rounded border-top border-primary border-4" style="max-width: 1200px;">
                <div class="card-body p-4 p-md-5">

                    <?php if (!empty($erros)): ?>
                        <div class="alert alert-danger mb-4 rounded-3 shadow-sm" role="alert">
                            <h6 class="fw-bold mb-2"><i class="fa-solid fa-triangle-exclamation me-2"></i>Foram encontrados erros:</h6>
                            <ul class="mb-0">
                                <?php foreach ($erros as $erro): ?>
                                    <li><?= htmlspecialchars($erro) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($erro_sistema)): ?>
                        <div class="alert alert-danger mb-4 rounded-3 shadow-sm" role="alert">
                            <strong>Erro de Sistema:</strong> <?= htmlspecialchars($erro_sistema) ?>
                        </div>
                    <?php endif; ?>
                    <form id="formNovaLocalizacao" action="#" method="POST" novalidate>
                        
                        <h5 class="text-dark mb-4 border-bottom pb-2">Dados da Instalação</h5>
                        
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <label class="form-label text-dark">Edifício / Bloco *</label>
                                <input type="text" name="edificio" class="form-control" placeholder="Ex: Edifício Principal" value="<?= htmlspecialchars($_POST['edificio'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark">Piso *</label>
                                <input type="text" name="piso" class="form-control" placeholder="Ex: Piso 2" value="<?= htmlspecialchars($_POST['piso'] ?? '') ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label text-dark">Serviço / Departamento *</label>
                                <select name="servico" class="form-select" required>
                                    <option value="" <?= empty($_POST['servico']) ? 'selected' : '' ?> disabled>Selecione o serviço...</option>
                                    <option value="Urgência Geral" <?= (($_POST['servico'] ?? '') === 'Urgência Geral') ? 'selected' : '' ?>>Urgência Geral</option>
                                    <option value="Cuidados Intensivos (UCI)" <?= (($_POST['servico'] ?? '') === 'Cuidados Intensivos (UCI)') ? 'selected' : '' ?>>Cuidados Intensivos (UCI)</option>
                                    <option value="Bloco Operatório" <?= (($_POST['servico'] ?? '') === 'Bloco Operatório') ? 'selected' : '' ?>>Bloco Operatório</option>
                                    <option value="Imagiologia" <?= (($_POST['servico'] ?? '') === 'Imagiologia') ? 'selected' : '' ?>>Imagiologia</option>
                                    <option value="Internamento" <?= (($_POST['servico'] ?? '') === 'Internamento') ? 'selected' : '' ?>>Internamento</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label text-dark">Sala / Gabinete</label>
                                <select name="sala" class="form-select">
                                    <option value="" <?= empty($_POST['sala']) ? 'selected' : '' ?> disabled>Selecione a sala...</option>
                                    <option value="Box 1" <?= (($_POST['sala'] ?? '') === 'Box 1') ? 'selected' : '' ?>>Box 1</option>
                                    <option value="Box 2" <?= (($_POST['sala'] ?? '') === 'Box 2') ? 'selected' : '' ?>>Box 2</option>
                                    <option value="Box 3" <?= (($_POST['sala'] ?? '') === 'Box 3') ? 'selected' : '' ?>>Box 3</option>
                                    <option value="Sala de Raio X" <?= (($_POST['sala'] ?? '') === 'Sala de Raio X') ? 'selected' : '' ?>>Sala de Raio X</option>
                                    <option value="Triagem" <?= (($_POST['sala'] ?? '') === 'Triagem') ? 'selected' : '' ?>>Triagem</option>
                                    <option value="Gabinete Médico" <?= (($_POST['sala'] ?? '') === 'Gabinete Médico') ? 'selected' : '' ?>>Gabinete Médico</option>
                                </select>
                            </div>
                        </div>

                        <h5 class="text-dark mb-4 border-bottom pb-2 mt-4">Capacidade e Requisitos Técnicos</h5>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-dark">Capacidade Máxima (Equipamentos)</label>
                                <input type="number" name="capacidade_maxima" class="form-control" placeholder="Ex: 15" value="<?= htmlspecialchars($_POST['capacidade_maxima'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark">Infraestrutura Disponível</label>
                                <select name="infraestrutura" class="form-select">
                                    <option value="1" <?= (($_POST['infraestrutura'] ?? '1') === '1') ? 'selected' : '' ?>>Tomadas UPS e Ponto de Rede Disponíveis</option>
                                    <option value="2" <?= (($_POST['infraestrutura'] ?? '') === '2') ? 'selected' : '' ?>>Apenas Tomadas Normais</option>
                                    <option value="3" <?= (($_POST['infraestrutura'] ?? '') === '3') ? 'selected' : '' ?>>Tomadas UPS (Sem Rede)</option>
                                    <option value="4" <?= (($_POST['infraestrutura'] ?? '') === '4') ? 'selected' : '' ?>>Sem Requisitos Especiais</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-dark">Observações Adicionais</label>
                                <textarea name="observacoes" class="form-control" rows="3" placeholder="Restrições de acesso, notas para a equipa técnica, etc."><?= htmlspecialchars($_POST['observacoes'] ?? '') ?></textarea>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
                            <a href="lista.php" class="btn btn-light border px-4 d-flex align-items-center">
                                <i class="fa-solid fa-xmark me-2"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-4 d-flex align-items-center">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Registar Localização
                            </button>
                        </div>
                    </form>

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
<?php include '../includes/footer.php'; ?>