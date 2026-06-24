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
// Encriptação e desencriptação de valores com OpenSSL

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
    // Validação básica: se não for string ou tiver tamanho ímpar, é inválido
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