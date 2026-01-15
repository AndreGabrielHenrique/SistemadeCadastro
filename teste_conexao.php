<!-- teste_conexao.php -->
<?php
// Teste simples de conexão (arquivo de diagnóstico)
$bdhost = 'localhost';        // Host do banco de dados
$bdusuario = 'GPnet';         // Nome de usuário do banco de dados
$bdsenha = '';                // Senha do banco (vazia no exemplo)
$bdnome = 'sistema-de-cadastro'; // Nome do banco de dados

echo "Testando conexão MySQLi...<br>"; // Mensagem inicial do teste

if (extension_loaded('mysqli')) { // Verifica se extensão MySQLi está carregada no PHP
    $conn = new mysqli($bdhost, $bdusuario, $bdsenha, $bdnome); // Tenta criar conexão
    
    if ($conn->connect_error) { // Se houve erro na conexão
        echo "Erro MySQLi: " . $conn->connect_error . "<br>"; // Exibe erro detalhado
    } else {
        echo "MySQLi: Conexão bem-sucedida!<br>"; // Mensagem de sucesso
        $conn->close(); // Fecha conexão (boas práticas)
    }
} else {
    echo "Extensão MySQLi não carregada.<br>"; // Alerta se extensão não disponível
    // Sugestão: ativar extension=mysqli no php.ini
}

echo "<br>Testando sessão...<br>"; // Início do teste de sessões
session_start(); // Inicia sessão PHP
$_SESSION['test'] = time(); // Armazena timestamp atual na sessão
echo "Session ID: " . session_id() . "<br>"; // Exibe ID único da sessão
echo "Session test: " . $_SESSION['test'] . "<br>"; // Exibe valor armazenado

echo "<br>Teste completo!"; // Mensagem final
?>