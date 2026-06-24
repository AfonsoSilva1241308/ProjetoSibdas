<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/includes/funcoes.php';

start_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/public/login_form.php');
    exit;
}

/*
    Aceita vários nomes possíveis vindos do formulário.
*/
$email = trim(
    $_POST['email']
    ?? $_POST['text_email']
    ?? $_POST['text_username']
    ?? $_POST['username']
    ?? ''
);

$password = trim(
    $_POST['password']
    ?? $_POST['text_password']
    ?? $_POST['pass']
    ?? ''
);

if ($email === '' || $password === '') {
    $_SESSION['server_error'] = 'Preenche o email e a palavra-passe.';
    header('Location: ' . BASE_URL . '/public/login_form.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['server_error'] = 'O endereço de email fornecido não é válido.';
    header('Location: ' . BASE_URL . '/public/login_form.php');
    exit;
}

try {
    $ligacao = db_connect();

    $stmt = $ligacao->prepare("
        SELECT 
            id,
            nome,
            email,
            password,
            papel,
            ativo,
            deleted_at
        FROM utilizador
        WHERE LOWER(TRIM(email)) = LOWER(TRIM(:email))
        LIMIT 1
    ");

    $stmt->execute([
        ':email' => $email
    ]);

    $utilizador_bd = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utilizador_bd) {
        $_SESSION['server_error'] = 'Credenciais incorretas ou utilizador não encontrado.';
        header('Location: ' . BASE_URL . '/public/login_form.php');
        exit;
    }

    if ((int)$utilizador_bd['ativo'] !== 1 || $utilizador_bd['deleted_at'] !== null) {
        $_SESSION['server_error'] = 'Este utilizador está desativado.';
        header('Location: ' . BASE_URL . '/public/login_form.php');
        exit;
    }

    $hash_bd = trim($utilizador_bd['password']);

    if (!password_verify($password, $hash_bd)) {
        $_SESSION['server_error'] = 'Credenciais incorretas ou utilizador não encontrado.';
        header('Location: ' . BASE_URL . '/public/login_form.php');
        exit;
    }

    session_regenerate_id(true);

    $_SESSION['utilizador_id'] = (int)$utilizador_bd['id'];
    $_SESSION['utilizador'] = $utilizador_bd['email'];
    $_SESSION['nome_utilizador'] = $utilizador_bd['nome'] ?: $utilizador_bd['email'];

    $_SESSION['papel'] = $utilizador_bd['papel'];
    $_SESSION['profile'] = $utilizador_bd['papel'];

    $stmtUpdate = $ligacao->prepare("
        UPDATE utilizador
        SET ultimo_acesso = NOW()
        WHERE id = :id
        LIMIT 1
    ");

    $stmtUpdate->execute([
        ':id' => $utilizador_bd['id']
    ]);

    header('Location: ' . BASE_URL . '/private/dashboard.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['server_error'] = 'Erro de sistema: Não foi possível processar o login.';
    header('Location: ' . BASE_URL . '/public/login_form.php');
    exit;
}
?>