<?php
require_once __DIR__ . '/../includes/funcoes.php';

bloquear_se_nao_tiver_perfil(['administrador']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/private/definicoes/website.php');
    exit;
}

$campoImagem = null;

if (isset($_FILES['banner_imagem'])) {
    $campoImagem = 'banner_imagem';
} elseif (isset($_FILES['imagem_banner'])) {
    $campoImagem = 'imagem_banner';
}

if (!$campoImagem || !isset($_FILES[$campoImagem]) || $_FILES[$campoImagem]['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['server_error'] = 'Nenhuma imagem válida foi enviada.';
    header('Location: ' . BASE_URL . '/private/definicoes/website.php');
    exit;
}

$imagem = $_FILES[$campoImagem];

$extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
$extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($extensao, $extensoesPermitidas, true)) {
    $_SESSION['server_error'] = 'Formato de imagem inválido. Usa JPG, PNG ou WEBP.';
    header('Location: ' . BASE_URL . '/private/definicoes/website.php');
    exit;
}

if ($imagem['size'] > 3 * 1024 * 1024) {
    $_SESSION['server_error'] = 'A imagem não pode ter mais de 3MB.';
    header('Location: ' . BASE_URL . '/private/definicoes/website.php');
    exit;
}

$pastaDestino = __DIR__ . '/../../assets/img/';

if (!is_dir($pastaDestino)) {
    mkdir($pastaDestino, 0777, true);
}

$nomeFicheiro = 'banner_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extensao;
$caminhoFinal = $pastaDestino . $nomeFicheiro;

if (!move_uploaded_file($imagem['tmp_name'], $caminhoFinal)) {
    $_SESSION['server_error'] = 'Erro ao guardar a imagem.';
    header('Location: ' . BASE_URL . '/private/definicoes/website.php');
    exit;
}

$caminhoBD = 'assets/img/' . $nomeFicheiro;

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        UPDATE gestao_site
        SET
            banner_imagem = :banner_imagem,
            atualizado_em = NOW(),
            atualizado_por = :atualizado_por
        WHERE id = 1
        LIMIT 1
    ");

    $stmt->execute([
        ':banner_imagem' => $caminhoBD,
        ':atualizado_por' => $_SESSION['utilizador_id'] ?? null
    ]);

    if (function_exists('registar_alteracao')) {
        registar_alteracao(
            'EDITAR',
            'Gestão de Site',
            'gestao_site',
            1,
            'Imagem/banner do website atualizado.',
            null,
            ['banner_imagem' => $caminhoBD]
        );
    }

    $_SESSION['server_success'] = 'Imagem atualizada com sucesso.';

} catch (PDOException $e) {
    $_SESSION['server_error'] = 'Erro ao atualizar a imagem na base de dados.';
}

header('Location: ' . BASE_URL . '/private/definicoes/website.php');
exit;