<?php
require_once __DIR__ . '/funcoes.php';

start_session();

$perfil = perfil_atual();
$urlAtual = $_SERVER['REQUEST_URI'] ?? '';

function sidebar_active($parteUrl) {
    global $urlAtual;
    return strpos($urlAtual, $parteUrl) !== false ? 'active' : 'text-dark';
}
?>

<div class="bg-white p-3 d-flex flex-column border-end h-100" style="width: 260px; min-width: 260px;">
    
    <a href="<?= BASE_URL ?>/private/dashboard.php" class="d-flex align-items-center mb-4 text-decoration-none justify-content-center mt-2">
        <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Logo" style="max-height: 45px;">
    </a>

    <div class="text-center mb-3">
        <?php if ($perfil === 'administrador'): ?>
            <span class="badge bg-primary px-3 py-2">
                Administrador
            </span>
        <?php elseif ($perfil === 'profissional_saude'): ?>
            <span class="badge bg-success px-3 py-2">
                Profissional de Saúde
            </span>
        <?php elseif ($perfil === 'tecnico'): ?>
            <span class="badge bg-warning text-dark px-3 py-2">
                Técnico
            </span>
        <?php else: ?>
            <span class="badge bg-secondary px-3 py-2">
                Utilizador
            </span>
        <?php endif; ?>
    </div>

    <ul class="nav nav-pills flex-column mb-auto gap-1 mt-2">

        <!-- TODOS OS PERFIS -->
        <li class="nav-item">
            <a href="<?= BASE_URL ?>/private/dashboard.php"
               class="nav-link <?= sidebar_active('dashboard.php') ?> fw-semibold shadow-sm px-3 py-2">
                <i class="fa-solid fa-chart-line me-2"></i>
                Visão Geral
            </a>
        </li>

        <!-- ADMINISTRADOR, PROFISSIONAL DE SAÚDE E TÉCNICO -->
        <?php if (tem_perfil(['administrador', 'profissional_saude', 'tecnico'])): ?>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/private/equipamentos/lista.php"
                   class="nav-link <?= sidebar_active('equipamentos') ?> fw-medium px-3 py-2">
                    <i class="fa-solid fa-stethoscope me-2"></i>
                    Equipamentos
                </a>
            </li>
        <?php endif; ?>

        <!-- ADMINISTRADOR, PROFISSIONAL DE SAÚDE E TÉCNICO -->
        <?php if (tem_perfil(['administrador', 'profissional_saude', 'tecnico'])): ?>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/private/localizacoes/lista.php"
                   class="nav-link <?= sidebar_active('localizacoes') ?> fw-medium px-3 py-2">
                    <i class="fa-solid fa-location-dot me-2"></i>
                    Localizações
                </a>
            </li>
        <?php endif; ?>

        <!-- ADMINISTRADOR, PROFISSIONAL DE SAÚDE E TÉCNICO -->
        <?php if (tem_perfil(['administrador', 'profissional_saude', 'tecnico'])): ?>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/private/manutencoes/lista.php"
                   class="nav-link <?= sidebar_active('manutencoes') ?> fw-medium px-3 py-2">
                    <i class="fa-solid fa-screwdriver-wrench me-2"></i>
                    Manutenção
                </a>
            </li>
        <?php endif; ?>

        <!-- ADMINISTRADOR E TÉCNICO -->
        <?php if (tem_perfil(['administrador', 'tecnico'])): ?>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/private/fornecedores/lista.php"
                   class="nav-link <?= sidebar_active('fornecedores') ?> fw-medium px-3 py-2">
                    <i class="fa-solid fa-truck-medical me-2"></i>
                    Fornecedores
                </a>
            </li>
        <?php endif; ?>

        <!-- SÓ ADMINISTRADOR -->
        <?php if (tem_perfil(['administrador'])): ?>
            <li class="nav-item mt-2 pt-2 border-top border-secondary border-opacity-25">
                <a href="<?= BASE_URL ?>/private/definicoes/website.php"
                   class="nav-link <?= sidebar_active('definicoes') ?> fw-medium px-3 py-2">
                    <i class="fa-solid fa-gear me-2"></i>
                    Gestão de Site
                </a>
            </li>
        <?php endif; ?>

    </ul>

    <div class="mt-4 pt-4 border-top border-secondary border-opacity-25">
        <a href="<?= BASE_URL ?>/public/index.php" class="btn btn-outline-primary w-100 fw-bold">
            <i class="fa-solid fa-right-from-bracket me-2"></i>
            Sair do Sistema
        </a>
    </div>
</div>