<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
    
    <div class="d-flex align-items-center gap-3">
        
        <?php if (isset($link_voltar)): ?>
            <a href="<?= $link_voltar ?>" class="btn btn-sm btn-outline-secondary px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar
            </a>
        <?php endif; ?>
        
        <?php if (isset($titulo_pagina)): ?>
            <div>
                <h2 class="m-0 fw-bold text-primary">
                    <?php if (isset($icone_pagina)): ?>
                        <i class="<?= $icone_pagina ?> me-2"></i>
                    <?php endif; ?>
                    <?= $titulo_pagina ?>
                </h2>
                <?php if (isset($subtitulo_pagina)): ?>
                    <p class="text-muted mb-0 small mt-1"><?= $subtitulo_pagina ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    </div>
    
    <div class="dropdown m-0">
        <button class="btn btn-light border shadow-sm d-flex align-items-center gap-2 dropdown-toggle" type="button" id="dropdownUtilizador" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-user-circle text-secondary"></i> <?= htmlspecialchars($_SESSION['utilizador']) ?>
        </button>
        
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="dropdownUtilizador">
            <li>
                <a class="dropdown-item py-2 text-secondary fw-medium" href="#" data-bs-toggle="modal" data-bs-target="#modalAlterarPassword">
                    <i class="fa-solid fa-key text-primary me-2"></i> Alterar Password
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item py-2 text-secondary fw-medium" href="/sibdas/1241308/medilink-digital/public/logout.php">
                    <i class="fa-solid fa-arrow-right-from-bracket text-primary me-2"></i> Sair da Conta
                </a>
            </li>
        </ul>
    </div>
</div>