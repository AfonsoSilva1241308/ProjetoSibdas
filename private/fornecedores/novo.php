<?php
// 1. Segurança e sessão (Ficha 12 - Pág 3)
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../../config/config.php';
redirect_if_not_logged();

// Preparar variáveis para distinguir tipos de erro (Ficha 12 - Pág 19)
$erros = []; 
$erro_sistema = ""; 

// 3. Verificar se o formulário foi submetido via POST (Ficha 12 - Pág 4)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recolher dados com o operador null coalescing '??' (Ficha 12 - Pág 4)
    $nome_empresa   = trim($_POST['nome_empresa'] ?? '');
    $nif            = trim($_POST['nif'] ?? '');
    $tipo           = trim($_POST['tipo_fornecedor'] ?? '');
    $telefone_geral = trim($_POST['telefone_geral'] ?? '');
    $email_empresa  = trim($_POST['email_empresa'] ?? '');
    $website        = trim($_POST['website'] ?? '');
    $morada         = trim($_POST['morada'] ?? '');
    $observacoes    = trim($_POST['observacoes'] ?? '');
    $nome_contacto  = trim($_POST['nome_contacto'] ?? '');
    $telefone_pess  = trim($_POST['telefone_pessoal'] ?? '');
    $email_pess     = trim($_POST['email_pessoal'] ?? '');

    // Normalização de entrada (Ficha 12 - Pág 13)
    $nome_empresa = ucwords(strtolower($nome_empresa));
    $email_empresa = strtolower($email_empresa);

    // Validação dos dados obrigatórios (Ficha 12 - Pág 9)
    if (empty($nome_empresa)) {
        $erros[] = "O campo Nome da Empresa é obrigatório.";
    }
    if (empty($nif)) {
        $erros[] = "O campo NIF é obrigatório.";
    }
    if (empty($tipo)) {
        $erros[] = "O Tipo de Fornecedor não foi selecionado.";
    }

    // Código final no contexto do bloco try (Ficha 12 - Pág 21)
    if (empty($erros)) {
        try {
            $ligacao = new PDO(
                "mysql:host=" . MYSQL_HOST . ";port=10464;dbname=" . MYSQL_DATABASE . ";charset=utf8",
                MYSQL_USERNAME,
                MYSQL_PASSWORD
            );
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Preparar a query com parâmetros nomeados (Ficha 12 - Pág 20)
            $sql = "INSERT INTO fornecedor (
                        nome, nif, tipo_fornecedor, telefone, email, 
                        website, morada, observacoes, pessoa_contacto, telefone_contacto, email_pessoal
                    ) VALUES (
                        :nome, :nif, :tipo, :tel_geral, :email_emp, 
                        :web, :morada, :obs, :nome_cont, :tel_pess, :email_pess
                    )";

            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':nome'      => $nome_empresa,
                ':nif'       => $nif,
                ':tipo'      => $tipo,
                ':tel_geral' => $telefone_geral,
                ':email_emp' => $email_empresa,
                ':web'       => !empty($website) ? $website : null,
                ':morada'    => $morada,
                ':obs'       => !empty($observacoes) ? $observacoes : null,
                ':nome_cont' => $nome_contacto,
                ':tel_pess'  => $telefone_pess,
                ':email_pess'=> !empty($email_pess) ? $email_pess : null
            ]);

            // Se a execução correu bem, redireciona (Ficha 12 - Pág 21)
            header("Location: lista.php?sucesso=inserido");
            exit;

        } catch (PDOException $err) {
            // Capturar o erro do PDO no catch (Ficha 12 - Pág 19)
            $erro_sistema = "Erro ao gravar os dados: " . $err->getMessage();
        }
        $ligacao = null;
    }
}

// Variáveis para a Navbar
$link_voltar = "lista.php"; 
$titulo_pagina = "Registar Novo Fornecedor";
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
                    <div class="alert alert-danger" role="alert">
                        <strong>Foram encontrados os seguintes erros:</strong>
                        <ul class="mb-0">
                            <?php foreach ($erros as $erro): ?>
                                <li><?= htmlspecialchars($erro) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($erro_sistema)): ?>
                    <div class="alert alert-danger">
                        <strong>Erro de Sistema:</strong>
                        <p><?= htmlspecialchars($erro_sistema) ?></p>
                    </div>
                <?php endif; ?>

                <form id="formNovoFornecedor" action="#" method="POST" novalidate>
                    
                    <h5 class="text-dark mb-4 border-bottom pb-2">Dados da Entidade</h5>
                    
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label text-dark">Nome da Empresa / Marca *</label>
                            <input type="text" name="nome_empresa" class="form-control" placeholder="Ex: Dräger Portugal Lda" value="<?= htmlspecialchars($_POST['nome_empresa'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark">NIF *</label>
                            <input type="text" name="nif" class="form-control" placeholder="Ex: 501234567" maxlength="9" value="<?= htmlspecialchars($_POST['nif'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark">Tipo de Fornecedor *</label>
                            <select name="tipo_fornecedor" class="form-select" required>
                                <option value="" <?= empty($_POST['tipo_fornecedor']) ? 'selected' : '' ?> disabled>Selecione uma opção...</option>
                                <option value="Fabricante" <?= (($_POST['tipo_fornecedor'] ?? '') === 'Fabricante') ? 'selected' : '' ?>>Fabricante</option>
                                <option value="Distribuidor ou fornecedor comercial" <?= (($_POST['tipo_fornecedor'] ?? '') === 'Distribuidor ou fornecedor comercial') ? 'selected' : '' ?>>Distribuidor ou fornecedor comercial</option>
                                <option value="Empresa de assistência técnica" <?= (($_POST['tipo_fornecedor'] ?? '') === 'Empresa de assistência técnica') ? 'selected' : '' ?>>Empresa de assistência técnica</option>
                                <option value="Fornecedor de consumíveis ou acessórios" <?= (($_POST['tipo_fornecedor'] ?? '') === 'Fornecedor de consumíveis ou acessórios') ? 'selected' : '' ?>>Fornecedor de consumíveis ou acessórios</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark">Contacto Telefónico (Geral) *</label>
                            <input type="tel" name="telefone_geral" class="form-control" placeholder="Ex: +351 210 123 456" value="<?= htmlspecialchars($_POST['telefone_geral'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark">Email da Empresa *</label>
                            <input type="email" name="email_empresa" class="form-control" placeholder="Ex: geral@empresa.pt" value="<?= htmlspecialchars($_POST['email_empresa'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark">Website</label>
                            <input type="url" name="website" class="form-control" placeholder="Ex: https://www.empresa.com" value="<?= htmlspecialchars($_POST['website'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-dark">Morada *</label>
                            <input type="text" name="morada" class="form-control" placeholder="Ex: Rua Direita, nº 10, Porto" value="<?= htmlspecialchars($_POST['morada'] ?? '') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-dark">Observações</label>
                            <textarea name="observacoes" class="form-control" rows="3" placeholder="Notas internas..."><?= htmlspecialchars($_POST['observacoes'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <h5 class="text-dark mb-4 border-bottom pb-2 mt-4">Pessoa de Contacto</h5>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-dark">Nome do Contacto *</label>
                            <input type="text" name="nome_contacto" class="form-control" placeholder="Ex: Eng. Carlos Mendes" value="<?= htmlspecialchars($_POST['nome_contacto'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark">Telefone Pessoal *</label>
                            <input type="tel" name="telefone_pessoal" class="form-control" placeholder="Ex: +351 912 345 678" value="<?= htmlspecialchars($_POST['telefone_pessoal'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark">Email Pessoal *</label>
                            <input type="email" name="email_pessoal" class="form-control" placeholder="Ex: carlos.mendes@empresa.pt" value="<?= htmlspecialchars($_POST['email_pessoal'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
                        <button type="reset" class="btn btn-outline-secondary px-4 fw-medium">
                            <i class="fa-solid fa-eraser me-1"></i> Limpar
                        </button>
                        <button type="submit" class="btn btn-primary px-5 fw-bold">
                            <i class="fa-regular fa-floppy-disk me-1"></i> Guardar Registo
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>