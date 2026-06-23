<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: lista.php');
    exit;
}

$idEncrypted = $_POST['id_equipamento'] ?? $_POST['id_escondido'] ?? '';
$id = aes_decrypt($idEncrypted);
if (!$id || !is_numeric($id)) {
    header('Location: lista.php?erro=id_invalido');
    exit;
}

try {
    $ligacao = db_connect();
    $stmt = $ligacao->prepare("DELETE FROM equipamento WHERE id = :id");
    $stmt->execute([':id' => (int)$id]);

    header('Location: lista.php?sucesso=apagado');
    exit;
} catch (PDOException $err) {
    $_SESSION['server_error'] = 'Nao foi possivel eliminar o equipamento. Verifique se nao existem dados associados que bloqueiem a remocao.';
    header('Location: lista.php?erro=apagar');
    exit;
}
?>
