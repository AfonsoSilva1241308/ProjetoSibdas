<?php
// Configurações globais da aplicação
define('APP_NAME', 'MediLink Digital');
define('APP_VERSION', '1.0.0');
define('APP_COPYRIGHT', '© 2026 MediLink Digital ISEP');

// Configurações adaptadas para o servidor remoto do ISEP
define('MYSQL_HOST', 'vsgate-s1.dei.isep.ipp.pt');
define('MYSQL_PORT', '10464');
define('MYSQL_DATABASE', 'db1241308'); // Verifica se o nome da BD tem espaço ou se é 'projeto_sibdas'
define('MYSQL_USERNAME', '1241308');
define('MYSQL_PASSWORD', 'silva_308');
define('MYSQL_AES_KEY', 'Vduu47qL51hLn6bkYkY6N101nivsmdfD');
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/sibdas/1241308/projeto-sibdas');

define('OPENSSL_METHOD', 'AES-256-CBC');
define('OPENSSL_KEY', 'HØSDRQZIGqc1X2kbYBk9xspdn9U5f3Wa'); 
define('OPENSSL_IV', 'BzKAbjuREsHgnw56');
?>
