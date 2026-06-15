<?php 
// 1. Iniciar sessão e recolher erros guardados (Ficha 10 - Pág. 11 e 12)
session_start();

$validation_errors = [];
if (!empty($_SESSION['validation_errors'])) {
    $validation_errors = $_SESSION['validation_errors'];
    unset($_SESSION['validation_errors']); // Apaga da sessão após ler
}

$server_error = '';
if (!empty($_SESSION['server_error'])) {
    $server_error = $_SESSION['server_error'];
    unset($_SESSION['server_error']); // Apaga da sessão após ler
}

// 2. Incluir o Header do teu sistema
include '../private/includes/header.php'; 
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
    
    <div class="card shadow border-0" style="width: 100%; max-width: 400px;">
        <div class="card-body p-4 p-sm-5">
            
            <div class="text-center mb-4">
                <img src="../assets/img/logo.png" alt="Logo MediLink Digital" style="max-height: 50px;">
            </div>

            <h4 class="text-center mb-4 fw-bold text-primary">Área Cliente</h4>

            <form action="../private/processar_login.php" method="POST">
                
                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" id="emailInput" placeholder="nome@hospital.pt" 
                           required 
                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
                           title="Por favor, insira um endereço de email institucional válido.">
                    <label for="emailInput">Endereço de Email</label>
                </div>
                
                <div class="form-floating mb-4">
                    <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Password" 
                           required 
                           minlength="8" 
                           title="A palavra-passe deve conter um mínimo de 8 caracteres por motivos de segurança.">
                    <label for="passwordInput">Palavra-passe</label>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold">Login</button>
                </div>

                <?php if (!empty($validation_errors)): ?>
                    <div class="alert alert-danger p-2 text-center mt-3" role="alert">
                        <?php foreach ($validation_errors as $error): ?>
                            <div><i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($server_error)): ?>
                    <div class="alert alert-danger p-2 text-center mt-3" role="alert">
                        <div><i class="fa-solid fa-triangle-exclamation me-1"></i> <?= htmlspecialchars($server_error) ?></div>
                    </div>
                <?php endif; ?>

            </form>

            <div class="text-center mt-3">
                <a href="index.php" class="text-decoration-none text-muted small">← Voltar à página inicial</a>
            </div>

        </div>
    </div>
</div>

<?php include '../private/includes/footer.php'; ?>