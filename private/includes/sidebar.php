<div class="bg-white p-3 d-flex flex-column border-end h-100" style="width: 260px; min-width: 260px;">
    <a href="/projeto_sibdas/private/dashboard.php" class="d-flex align-items-center mb-4 text-decoration-none justify-content-center mt-2">
        <img src="/projeto_sibdas/assets/img/logo.png" alt="Logo" style="max-height: 45px;">
    </a>

    <ul class="nav nav-pills flex-column mb-auto gap-1 mt-2">
        
        <li class="nav-item">
            <a href="/projeto_sibdas/private/dashboard.php" 
               class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'dashboard.php')) ? 'active' : 'text-dark'; ?> fw-semibold shadow-sm px-3 py-2">
               Visão Geral
            </a>
        </li>

        <li class="nav-item">
            <a href="/projeto_sibdas/private/equipamentos/lista.php" 
               class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'equipamentos')) ? 'active' : 'text-dark'; ?> fw-medium px-3 py-2">
               Equipamentos
            </a>
        </li>

        <li class="nav-item">
            <a href="/projeto_sibdas/private/localizacoes/lista.php" 
               class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'localizacoes')) ? 'active' : 'text-dark'; ?> fw-medium px-3 py-2">
               Localizações
            </a>
        </li>

        <li class="nav-item">
            <a href="/projeto_sibdas/private/manutencoes/lista.php" 
               class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'manutencoes')) ? 'active' : 'text-dark'; ?> fw-medium px-3 py-2">
               Manutenção
            </a>
        </li>

        <li class="nav-item">
            <a href="/projeto_sibdas/private/fornecedores/lista.php" 
               class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'fornecedores')) ? 'active' : 'text-dark'; ?> fw-medium px-3 py-2">
               Fornecedores
            </a>
        </li>

        <li class="nav-item mt-2 pt-2 border-top border-secondary border-opacity-25">
            <a href="/projeto_sibdas/private/definicoes/website.php" 
               class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'definicoes')) ? 'active' : 'text-dark'; ?> fw-medium px-3 py-2">
               Gestão de Site
            </a>
        </li>
    </ul>

    <div class="mt-4 pt-4 border-top border-secondary border-opacity-25">
        <a href="/projeto_sibdas/public/index.php" class="btn btn-outline-primary w-100 fw-bold">Sair do Sistema</a>
    </div>
</div>