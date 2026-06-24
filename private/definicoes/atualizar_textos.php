<?php
require_once __DIR__ . '/../includes/funcoes.php';

bloquear_se_nao_tiver_perfil(['administrador']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/private/definicoes/website.php');
    exit;
}

$texto_sobre_nos = trim($_POST['texto_sobre_nos'] ?? '');
$texto_solucao = trim($_POST['texto_solucao'] ?? '');
$texto_funcionalidades = trim($_POST['texto_funcionalidades'] ?? '');

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        UPDATE gestao_site
        SET
            texto_sobre_nos = :texto_sobre_nos,
            texto_solucao = :texto_solucao,
            texto_funcionalidades = :texto_funcionalidades,
            atualizado_em = NOW(),
            atualizado_por = :atualizado_por
        WHERE id = 1
        LIMIT 1
    ");

    $stmt->execute([
        ':texto_sobre_nos' => $texto_sobre_nos,
        ':texto_solucao' => $texto_solucao,
        ':texto_funcionalidades' => $texto_funcionalidades,
        ':atualizado_por' => $_SESSION['utilizador_id'] ?? null
    ]);

    if (function_exists('registar_alteracao')) {
        registar_alteracao(
            'EDITAR',
            'Gestão de Site',
            'gestao_site',
            1,
            'Textos institucionais do website atualizados.',
            null,
            $_POST
        );
    }

    $_SESSION['server_success'] = 'Textos atualizados com sucesso.';

} catch (PDOException $e) {
    $_SESSION['server_error'] = 'Erro ao atualizar os textos.';
}

header('Location: ' . BASE_URL . '/private/definicoes/website.php');
exit;