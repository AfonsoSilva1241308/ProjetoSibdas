<?php
require_once __DIR__ . '/../includes/funcoes.php';

bloquear_se_nao_tiver_perfil(['administrador']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/private/definicoes/website.php');
    exit;
}

$titulo_form = trim($_POST['titulo_form'] ?? '');
$texto_apoio = trim($_POST['texto_apoio'] ?? '');
$morada_rua = trim($_POST['morada_rua'] ?? '');
$morada_cod_postal = trim($_POST['morada_cod_postal'] ?? '');
$horario_semana = trim($_POST['horario_semana'] ?? '');
$horario_fim_semana = trim($_POST['horario_fim_semana'] ?? '');
$email_contato = trim($_POST['email_contato'] ?? '');
$telefone_contato = trim($_POST['telefone_contato'] ?? '');

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        UPDATE gestao_site
        SET
            titulo_form = :titulo_form,
            texto_apoio = :texto_apoio,
            morada_rua = :morada_rua,
            morada_cod_postal = :morada_cod_postal,
            horario_semana = :horario_semana,
            horario_fim_semana = :horario_fim_semana,
            email_contato = :email_contato,
            telefone_contato = :telefone_contato,
            atualizado_em = NOW(),
            atualizado_por = :atualizado_por
        WHERE id = 1
        LIMIT 1
    ");

    $stmt->execute([
        ':titulo_form' => $titulo_form,
        ':texto_apoio' => $texto_apoio,
        ':morada_rua' => $morada_rua,
        ':morada_cod_postal' => $morada_cod_postal,
        ':horario_semana' => $horario_semana,
        ':horario_fim_semana' => $horario_fim_semana,
        ':email_contato' => $email_contato,
        ':telefone_contato' => $telefone_contato,
        ':atualizado_por' => $_SESSION['utilizador_id'] ?? null
    ]);

    if (function_exists('registar_alteracao')) {
        registar_alteracao(
            'EDITAR',
            'Gestão de Site',
            'gestao_site',
            1,
            'Contactos públicos do website atualizados.',
            null,
            $_POST
        );
    }

    $_SESSION['server_success'] = 'Contactos atualizados com sucesso.';

} catch (PDOException $e) {
    $_SESSION['server_error'] = 'Erro ao atualizar os contactos.';
}

header('Location: ' . BASE_URL . '/private/definicoes/website.php');
exit;