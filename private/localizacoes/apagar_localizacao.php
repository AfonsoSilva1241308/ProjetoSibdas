<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../includes/funcoes.php';

redirect_if_not_logged();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: lista.php');
    exit;
}

$idParam = $_POST['id_localizacao'] ?? $_POST['localizacao_id'] ?? $_POST['id_escondido'] ?? '';

if (empty($idParam)) {
    header('Location: lista.php?erro=id_invalido');
    exit;
}

// Aceita ID encriptado ou ID normal.
if (is_numeric($idParam)) {
    $idLocalizacao = (int) $idParam;
} else {
    $idLocalizacao = aes_decrypt(urldecode($idParam));
}

if (!$idLocalizacao || !is_numeric($idLocalizacao)) {
    header('Location: lista.php?erro=id_invalido');
    exit;
}

$idLocalizacao = (int) $idLocalizacao;

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /*
        SOFT DELETE:
        Não apaga a localização da base de dados.
        Apenas marca deleted_at com a data atual.
    */
    $stmt = $ligacao->prepare("
        UPDATE localizacao
        SET deleted_at = NOW()
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ':id' => $idLocalizacao
    ]);

    header('Location: lista.php?sucesso=removida');
    exit;

} catch (PDOException $err) {
    $_SESSION['server_error'] = 'Não foi possível remover a localização da lista. Tente novamente.';
    header('Location: lista.php?erro=apagar');
    exit;
}
?>