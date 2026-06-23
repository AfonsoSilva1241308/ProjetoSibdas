<?php
header('Content-Type: text/html; charset=UTF-8');
echo "Atualização ção ã õ ç — TESTE";
echo "<hr>Charset enviado: " . (headers_list() ? implode(', ', array_filter(headers_list(), fn($h)=>stripos($h,'content-type')!==false)) : 'nenhum');
?>