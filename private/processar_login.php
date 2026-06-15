<?php
require_once 'includes/funcoes.php';

// Inicia a sessão
start_session();

// 1. SEGURANÇA: Impede que o utilizador aceda diretamente a este script por URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/login_form.php');
    return;
}

// 2. RECOLHA DE DADOS
$email    = isset($_POST['email']) ? $_POST['email'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// 3. VALIDAÇÃO
$validation_errors = [];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $validation_errors[] = 'O endereço de email fornecido não é válido.';
}

if (strlen($password) < 8) {
    $validation_errors[] = 'A palavra-passe deve ter pelo menos 8 caracteres.';
}

// Se existirem erros, guarda-os na sessão e redireciona de volta para o login
if (!empty($validation_errors)) {
    $_SESSION['validation_errors'] = $validation_errors;
    header('Location: ../public/login_form.php');
    return;
}

// 4. SIMULAÇÃO DE BASE DE DADOS
// Como ainda não ligámos à tua tabela 'utilizador', simulamos que o login está correto
$result['status'] = 1; // 1 = Login Válido

if (!$result['status']) {
    $_SESSION['server_error'] = 'Credenciais incorretas ou utilizador não encontrado.';
    header('Location: ../public/login_form.php');
    return;
}

// 5. SUCESSO: Guardar utilizador na sessão e entrar
$_SESSION['utilizador'] = $email;
header('Location: dashboard.php');
exit;
?>