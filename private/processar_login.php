<?php
require_once __DIR__ . '/includes/funcoes.php';

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

// 4. LIGAÇÃO E VALIDAÇÃO COM A BASE DE DADOS REAL
try {
    // 4.1 Ligar à base de dados (usando as constantes do teu config.php)
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=10464;dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 4.2 Preparar a query de forma segura (evita SQL Injection)
    $stmt = $ligacao->prepare("SELECT * FROM utilizador WHERE email = :email LIMIT 1");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // 4.3 Obter os dados do utilizador
    $utilizador_bd = $stmt->fetch(PDO::FETCH_ASSOC);

    // 4.4 Verificar se o utilizador existe E se a password coincide com o hash
    // A função password_verify compara a password em texto limpo com o hash encriptado
    if ($utilizador_bd && password_verify($password, $utilizador_bd['password'])) {
        
        // 5. SUCESSO: Guardar dados na sessão e entrar
        $_SESSION['utilizador'] = $utilizador_bd['email'];
        $_SESSION['papel'] = $utilizador_bd['papel']; // Guardamos o papel (admin) na sessão, é muito útil!
        
        // Opcional mas recomendado: redirecionar usando o BASE_URL para não haver perdas de caminho
        header('Location: ' . BASE_URL . '/private/dashboard.php');
        exit;
        
    } else {
        // FALHA: Email não encontrado ou password errada
        // Por segurança, damos sempre uma mensagem genérica para não revelar qual dos dois falhou
        $_SESSION['server_error'] = 'Credenciais incorretas ou utilizador não encontrado.';
        header('Location: ../public/login_form.php');
        exit;
    }

} catch (PDOException $e) {
    // FALHA CRÍTICA: Erro na ligação à BD ou erro de SQL
    $_SESSION['server_error'] = 'Erro de sistema: Não foi possível processar o login.';
    // Em ambiente de desenvolvimento podes querer ver o erro real: 
    // $_SESSION['server_error'] = 'Erro: ' . $e->getMessage();
    header('Location: ../public/login_form.php');
    exit;
}
?>