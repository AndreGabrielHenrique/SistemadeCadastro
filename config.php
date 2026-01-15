<!-- config.php -->
<?php
// Configurações de conexão com o banco de dados
$bdhost = 'localhost';        // Host do banco de dados
$bdusuario = 'GPnet';         // Nome de usuário do banco de dados
$bdsenha = '';                // Senha do banco (vazia no exemplo)
$bdnome = 'sistema-de-cadastro'; // Nome do banco de dados

// Tenta criar uma nova conexão MySQLi
try {
    $conexao = new mysqli($bdhost, $bdusuario, $bdsenha, $bdnome);  // Cria nova instância de conexão MySQLi
    
    // Verifica se a conexão foi bem-sucedida
    if ($conexao->connect_error) {
        throw new Exception("Erro de conexão: " . $conexao->connect_error);  // Lança exceção se houver erro
    }
    
    // Define o conjunto de caracteres para a conexão
    if (!$conexao->set_charset("utf8mb4")) {
        throw new Exception("Erro ao definir charset: " . $conexao->error);  // Lança exceção se falhar ao definir charset
    }
    
    // Query para definir caracteres (compatibilidade extra)
    $conexao->query("SET NAMES 'utf8mb4'");  // Define nomes como utf8mb4
    $conexao->query("SET CHARACTER SET utf8mb4");  // Define charset como utf8mb4
    $conexao->query("SET SESSION collation_connection = 'utf8mb4_unicode_ci'");  // Define collation como unicode
    
} catch (Exception $e) {
    // Em produção, você pode logar o erro em vez de exibir
    error_log("Erro de banco de dados: " . $e->getMessage());  // Registra erro no log do servidor
    die("Erro ao conectar ao banco de dados. Tente novamente mais tarde.");  // Exibe mensagem amigável e encerra
}
?>