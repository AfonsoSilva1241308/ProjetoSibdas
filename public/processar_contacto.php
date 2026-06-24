<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php#contacto');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$instituicao = trim($_POST['instituicao'] ?? '');
$email = trim($_POST['email'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');

if ($nome === '' || $instituicao === '' || $email === '') {
    header('Location: index.php?erro=campos#contacto');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?erro=email#contacto');
    exit;
}

if (mb_strlen($nome) > 150 || mb_strlen($instituicao) > 150 || mb_strlen($email) > 100) {
    header('Location: index.php?erro=tamanho#contacto');
    exit;
}

try {
    $porta = defined('MYSQL_PORT') ? MYSQL_PORT : 10464;

    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . $porta . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );

    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $ligacao->prepare("\n        INSERT INTO mensagem_contacto\n            (nome, instituicao, email, mensagem, data_envio, lida)\n        VALUES\n            (:nome, :instituicao, :email, :mensagem, NOW(), 0)\n    ");

    $stmt->execute([
        ':nome' => $nome,
        ':instituicao' => $instituicao,
        ':email' => $email,
        ':mensagem' => $mensagem
    ]);

    header('Location: index.php?sucesso=mensagem#contacto');
    exit;

} catch (PDOException $e) {
    header('Location: index.php?erro=sistema#contacto');
    exit;
}