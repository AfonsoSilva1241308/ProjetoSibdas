<?php

require_once __DIR__ . '/../../config/config.php';

/*
    ==============================
    SESSÃO
    ==============================
*/

if (!function_exists('start_session')) {
    function start_session() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}

if (!function_exists('check_session')) {
    function check_session() {
        start_session();
        return isset($_SESSION['utilizador']);
    }
}

if (!function_exists('redirect_if_not_logged')) {
    function redirect_if_not_logged($redirect_to = null) {
        start_session();

        if ($redirect_to === null) {
            $redirect_to = BASE_URL . '/public/login_form.php';
        }

        if (!check_session()) {
            header("Location: " . $redirect_to);
            exit;
        }
    }
}

if (!function_exists('logout_and_redirect')) {
    function logout_and_redirect($redirect_to = null) {
        start_session();

        if ($redirect_to === null) {
            $redirect_to = BASE_URL . '/public/login_form.php';
        }

        session_unset();
        session_destroy();

        header("Location: " . $redirect_to);
        exit;
    }
}

/*
    ==============================
    PERMISSÕES — FICHA 14
    ==============================

    Perfis do projeto:
    - administrador
    - profissional_saude
    - tecnico
*/

if (!function_exists('perfil_atual')) {
    function perfil_atual() {
        start_session();

        if (isset($_SESSION['profile'])) {
            return $_SESSION['profile'];
        }

        if (isset($_SESSION['papel'])) {
            return $_SESSION['papel'];
        }

        return null;
    }
}

if (!function_exists('is_admin')) {
    function is_admin() {
        return perfil_atual() === 'administrador';
    }
}

if (!function_exists('is_profissional_saude')) {
    function is_profissional_saude() {
        return perfil_atual() === 'profissional_saude';
    }
}

if (!function_exists('is_tecnico')) {
    function is_tecnico() {
        return perfil_atual() === 'tecnico';
    }
}

if (!function_exists('tem_perfil')) {
    function tem_perfil($perfis_permitidos) {
        start_session();

        $perfil = perfil_atual();

        if (!$perfil) {
            return false;
        }

        if (!is_array($perfis_permitidos)) {
            $perfis_permitidos = [$perfis_permitidos];
        }

        return in_array($perfil, $perfis_permitidos, true);
    }
}

if (!function_exists('bloquear_se_nao_tiver_perfil')) {
    function bloquear_se_nao_tiver_perfil($perfis_permitidos) {
        redirect_if_not_logged();

        if (!tem_perfil($perfis_permitidos)) {
            $_SESSION['server_error'] = 'Não tens permissão para aceder a esta página.';
            header('Location: ' . BASE_URL . '/private/dashboard.php?erro=sem_permissao');
            exit;
        }
    }
}

/*
    ==============================
    BASE DE DADOS
    ==============================
*/

if (!function_exists('db_connect')) {
    function db_connect() {
        $porta = defined('MYSQL_PORT') ? MYSQL_PORT : 10464;

        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . $porta . ";dbname=" . MYSQL_DATABASE . ";charset=utf8mb4",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );

        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $ligacao;
    }
}

/*
    ==============================
    SEGURANÇA / HTML
    ==============================
*/

if (!function_exists('h')) {
    function h($valor) {
        return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('e')) {
    function e($valor) {
        return h($valor);
    }
}
if (!function_exists('registar_alteracao')) {
    function registar_alteracao($acao, $modulo, $tabela_afetada, $registo_id, $descricao, $dados_anteriores = null, $dados_novos = null) {
        try {
            start_session();

            $ligacao = db_connect();

            $utilizador_id = $_SESSION['utilizador_id'] ?? null;
            $utilizador_email = $_SESSION['utilizador'] ?? null;
            $perfil_utilizador = $_SESSION['profile'] ?? $_SESSION['papel'] ?? null;

            $ip_utilizador = $_SERVER['REMOTE_ADDR'] ?? null;
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

            $stmt = $ligacao->prepare("
                INSERT INTO registo_alteracoes
                    (
                        utilizador_id,
                        utilizador_email,
                        perfil_utilizador,
                        acao,
                        modulo,
                        tabela_afetada,
                        registo_id,
                        descricao,
                        dados_anteriores,
                        dados_novos,
                        ip_utilizador,
                        user_agent,
                        criado_em
                    )
                VALUES
                    (
                        :utilizador_id,
                        :utilizador_email,
                        :perfil_utilizador,
                        :acao,
                        :modulo,
                        :tabela_afetada,
                        :registo_id,
                        :descricao,
                        :dados_anteriores,
                        :dados_novos,
                        :ip_utilizador,
                        :user_agent,
                        NOW()
                    )
            ");

            $stmt->execute([
                ':utilizador_id' => $utilizador_id,
                ':utilizador_email' => $utilizador_email,
                ':perfil_utilizador' => $perfil_utilizador,
                ':acao' => $acao,
                ':modulo' => $modulo,
                ':tabela_afetada' => $tabela_afetada,
                ':registo_id' => $registo_id,
                ':descricao' => $descricao,
                ':dados_anteriores' => $dados_anteriores ? json_encode($dados_anteriores, JSON_UNESCAPED_UNICODE) : null,
                ':dados_novos' => $dados_novos ? json_encode($dados_novos, JSON_UNESCAPED_UNICODE) : null,
                ':ip_utilizador' => $ip_utilizador,
                ':user_agent' => $user_agent
            ]);

        } catch (Exception $e) {
            // Não bloqueia o site se o registo de auditoria falhar.
            return false;
        }

        return true;
    }
}
/*
    ==============================
    ENCRIPTAÇÃO OPENSSL
    ==============================
*/

if (!function_exists('aes_encrypt')) {
    function aes_encrypt($value) {
        return bin2hex(openssl_encrypt(
            (string) $value,
            OPENSSL_METHOD,
            OPENSSL_KEY,
            OPENSSL_RAW_DATA,
            OPENSSL_IV
        ));
    }
}

if (!function_exists('aes_decrypt')) {
    function aes_decrypt($value) {
        if (!is_string($value) || $value === '' || strlen($value) % 2 !== 0) {
            return false;
        }

        $binario = @hex2bin($value);

        if ($binario === false) {
            return false;
        }

        return openssl_decrypt(
            $binario,
            OPENSSL_METHOD,
            OPENSSL_KEY,
            OPENSSL_RAW_DATA,
            OPENSSL_IV
        );
    }
}

?>