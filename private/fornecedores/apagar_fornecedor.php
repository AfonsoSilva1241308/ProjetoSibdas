<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../includes/funcoes.php';

redirect_if_not_logged();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: lista.php');
    exit;
}

$idParam = $_POST['id_fornecedor'] ?? $_POST['id_escondido'] ?? '';

if (empty($idParam)) {
    header('Location: lista.php?erro=id_invalido');
    exit;
}

// Aceita ID encriptado ou ID normal
if (is_numeric($idParam)) {
    $idFornecedor = (int) $idParam;
} else {
    $idFornecedor = aes_decrypt(urldecode($idParam));
}

if (!$idFornecedor || !is_numeric($idFornecedor)) {
    header('Location: lista.php?erro=id_invalido');
    exit;
}

$idFornecedor = (int) $idFornecedor;

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );

    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /*
        SOFT DELETE:
        Não apaga o fornecedor da base de dados.
        Apenas marca deleted_at com a data atual.
    */
    $stmt = $ligacao->prepare("
        UPDATE fornecedor
        SET deleted_at = NOW()
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ':id' => $idFornecedor
    ]);

    header('Location: lista.php?sucesso=fornecedor_apagado');
    exit;

} catch (PDOException $err) {
    $_SESSION['server_error'] = 'Não foi possível remover o fornecedor da lista. Tente novamente.';
    header('Location: lista.php?erro=apagar');
    exit;
}
?>