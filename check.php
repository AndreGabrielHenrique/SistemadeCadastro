<!-- check.php -->
<?php
// Este arquivo verifica a configuração do PHP e extensões necessárias

echo "PHP Version: " . phpversion() . "<br>";  // Exibe a versão do PHP instalada

// Verificar se mysqli está disponível
if (extension_loaded('mysqli')) {
    echo "MySQLi: OK<br>";  // Confirma que a extensão MySQLi está carregada
} else {
    echo "MySQLi: NOT LOADED<br>";  // Alerta que a extensão não está carregada
    echo "Verifique se a extensão está ativada no php.ini<br>";  // Sugere verificar configuração
}

// Verificar se sessions funcionam
session_start();  // Inicia uma sessão PHP
$_SESSION['test'] = 'ok';  // Define uma variável de sessão de teste
if (isset($_SESSION['test'])) {
    echo "Sessions: OK<br>";  // Confirma que sessões estão funcionando
} else {
    echo "Sessions: NOT WORKING<br>";  // Alerta que sessões não funcionam
}

// Verificar configurações de output buffering
echo "output_buffering: " . ini_get('output_buffering') . "<br>";  // Exibe configuração de buffer de saída
echo "Implicit Flush: " . ini_get('implicit_flush') . "<br>";  // Exibe configuração de flush implícito

// Verificar se há espaço antes da tag PHP
echo "Há saída antes do PHP? " . (headers_sent() ? 'SIM' : 'NÃO') . "<br>";  // Verifica se headers já foram enviados
if (headers_sent()) {
    echo "Headers foram enviados em: " . headers_sent($file, $line) . "<br>";  // Mostra onde headers foram enviados
    echo "Arquivo: $file, Linha: $line<br>";  // Exibe arquivo e linha onde ocorreu envio prematuro
}
?>