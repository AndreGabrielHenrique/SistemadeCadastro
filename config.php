<!-- config.php -->
<?php
    // Configurações de conexão com o banco de dados
    $bdhost = 'localhost';        // Host do banco de dados
    $bdusuario = 'GPnet';         // Nome de usuário do banco
    $bdsenha = '';                // Senha do banco (vazia no exemplo)
    $bdnome = 'sistema-de-cadastro'; // Nome do banco de dados

    // Cria uma nova conexão MySQLi
    $conexao = new mysqli($bdhost, $bdusuario, $bdsenha, $bdnome);

    // Verifica se a conexão foi bem-sucedida
    $conexao->set_charset("utf8mb4"); // Define o conjunto de caracteres para a conexão
    $conexao->query("SET NAMES 'utf8mb4'");
    $conexao->query("SET CHARACTER SET utf8mb4");
    $conexao->query("SET SESSION collation_connection = 'utf8mb4_unicode_ci'");

    // Trecho comentado de verificação de conexão (mantido para referência)
    // if ($conexao->connect_error) {
    //     echo "Erro";               // Exibe mensagem de erro se a conexão falhar
    // } else {
    //     echo "Conexão efetuada com sucesso"; // Mensagem de sucesso na conexão
    // }
?>
