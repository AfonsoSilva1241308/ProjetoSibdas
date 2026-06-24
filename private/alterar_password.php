<?php
require_once __DIR__ . '/includes/funcoes.php';

redirect_if_not_logged();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/private/dashboard.php');
    exit;
}

$password_atual = trim($_POST['password_atual'] ?? '');
$nova_password = trim($_POST['nova_password'] ?? '');
$confirmar_password = trim($_POST['confirmar_password'] ?? '');

$utilizador_id = $_SESSION['utilizador_id'] ?? null;

if (!$utilizador_id) {
    $_SESSION['server_error'] = 'Sessão inválida. Faça login novamente.';
    header('Location: ' . BASE_URL . '/public/login_form.php');
    exit;
}

if ($password_atual === '' || $nova_password === '' || $confirmar_password === '') {
    $_SESSION['server_error'] = 'Preenche todos os campos da palavra-passe.';
    header('Location: ' . BASE_URL . '/private/dashboard.php');
    exit;
}

if (strlen($nova_password) < 8) {
    $_SESSION['server_error'] = 'A nova palavra-passe deve ter pelo menos 8 caracteres.';
    header('Location: ' . BASE_URL . '/private/dashboard.php');
    exit;
}

if ($nova_password !== $confirmar_password) {
    $_SESSION['server_error'] = 'A confirmação da nova palavra-passe não coincide.';
    header('Location: ' . BASE_URL . '/private/dashboard.php');
    exit;
}

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        SELECT id, email, password
        FROM utilizador
        WHERE id = :id
          AND ativo = 1
          AND deleted_at IS NULL
        LIMIT 1
    ");

    $stmt->execute([
        ':id' => $utilizador_id
    ]);

    $utilizador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utilizador) {
        $_SESSION['server_error'] = 'Utilizador não encontrado.';
        header('Location: ' . BASE_URL . '/private/dashboard.php');
        exit;
    }

    if (!password_verify($password_atual, $utilizador['password'])) {
        $_SESSION['server_error'] = 'A palavra-passe atual está incorreta.';
        header('Location: ' . BASE_URL . '/private/dashboard.php');
        exit;
    }

    $nova_password_hash = password_hash($nova_password, PASSWORD_DEFAULT);

    $stmtUpdate = $ligacao->prepare("
        UPDATE utilizador
        SET 
            password = :password,
            ultimo_acesso = NOW()
        WHERE id = :id
        LIMIT 1
    ");

    $stmtUpdate->execute([
        ':password' => $nova_password_hash,
        ':id' => $utilizador_id
    ]);

    if (function_exists('registar_alteracao')) {
        registar_alteracao(
            'ALTERAR_PASSWORD',
            'Utilizadores',
            'utilizador',
            $utilizador_id,
            'O utilizador alterou a sua palavra-passe.',
            null,
            ['email' => $utilizador['email']]
        );
    }

    $_SESSION['server_success'] = 'Palavra-passe alterada com sucesso.';
    header('Location: ' . BASE_URL . '/private/dashboard.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['server_error'] = 'Erro ao alterar a palavra-passe.';
    header('Location: ' . BASE_URL . '/private/dashboard.php');
    exit;}
    