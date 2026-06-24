<?php
require_once __DIR__ . '/../includes/funcoes.php';

bloquear_se_nao_tiver_perfil(['administrador']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/private/definicoes/website.php');
    exit;
}

$num_instituicoes = (int)($_POST['num_instituicoes'] ?? 0);
$num_dispositivos = (int)($_POST['num_dispositivos'] ?? 0);
$perc_monitorizacao = (int)($_POST['perc_monitorizacao'] ?? 0);
$perc_suporte = (int)($_POST['perc_suporte'] ?? 0);
$perc_terapia = (int)($_POST['perc_terapia'] ?? 0);
$perc_diagnostico = (int)($_POST['perc_diagnostico'] ?? 0);

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        UPDATE gestao_site
        SET
            num_instituicoes = :num_instituicoes,
            num_dispositivos = :num_dispositivos,
            perc_monitorizacao = :perc_monitorizacao,
            perc_suporte = :perc_suporte,
            perc_terapia = :perc_terapia,
            perc_diagnostico = :perc_diagnostico,
            atualizado_em = NOW(),
            atualizado_por = :atualizado_por
        WHERE id = 1
        LIMIT 1
    ");

    $stmt->execute([
        ':num_instituicoes' => $num_instituicoes,
        ':num_dispositivos' => $num_dispositivos,
        ':perc_monitorizacao' => $perc_monitorizacao,
        ':perc_suporte' => $perc_suporte,
        ':perc_terapia' => $perc_terapia,
        ':perc_diagnostico' => $perc_diagnostico,
        ':atualizado_por' => $_SESSION['utilizador_id'] ?? null
    ]);

    if (function_exists('registar_alteracao')) {
        registar_alteracao(
            'EDITAR',
            'Gestão de Site',
            'gestao_site',
            1,
            'Indicadores públicos do website atualizados.',
            null,
            $_POST
        );
    }

    $_SESSION['server_success'] = 'Indicadores atualizados com sucesso.';

} catch (PDOException $e) {
    $_SESSION['server_error'] = 'Erro ao atualizar os indicadores.';
}

header('Location: ' . BASE_URL . '/private/definicoes/website.php');
exit;