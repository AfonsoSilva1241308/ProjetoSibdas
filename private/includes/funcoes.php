<?php

require_once __DIR__ . '/../../config/config.php';
// Inicia a sessÃo se ainda nÃo estiver iniciada
function start_session() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Verifica se a sessÃo do utilizador estÃo ativa
function check_session() {
    return isset($_SESSION['utilizador']);
}

// Redireciona automaticamente se nÃ£o houver sessÃ£o iniciada
function redirect_if_not_logged($redirect_to = "/projeto_sibdas/public/login_form.php") {
    start_session();
    if (!check_session()) {
        header("Location: " . $redirect_to);
        exit;
    }
}

// Termina a sessÃo e redireciona para o login
function logout_and_redirect($redirect_to = "/projeto_sibdas/public/login_form.php") {
    start_session();
    session_unset();
    session_destroy();
    header("Location: " . $redirect_to);
    exit;
}

// Cria uma ligacao PDO unica e consistente para todas as paginas.
function db_connect() {
    $dsn = "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4";

    return new PDO($dsn, MYSQL_USERNAME, MYSQL_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function format_date_pt($value) {
    if (empty($value)) {
        return 'N/A';
    }

    return date('d/m/Y', strtotime($value));
}

function null_if_empty($value) {
    $value = is_string($value) ? trim($value) : $value;
    return $value === '' ? null : $value;
}
function selected_option($current, $expected) {
    $expected = html_entity_decode($expected, ENT_QUOTES, 'UTF-8');
    return (string)$current === (string)$expected ? 'selected' : '';
}
// EncriptaÃ§Ã£o e desencriptaÃ§Ã£o de valores com OpenSSL

function aes_encrypt($value) {
    return bin2hex(openssl_encrypt(
        (string)$value,
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    ));
}

function aes_decrypt($value) {
    // ValidaÃ§Ã£o bÃ¡sica: se nÃ£o for string ou tiver tamanho Ã­mpar, Ã© invÃ¡lido
    if (!is_string($value) || strlen($value) % 2 !== 0) {
        return false;
    }
    return openssl_decrypt(
        hex2bin($value),
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    );
}
?>