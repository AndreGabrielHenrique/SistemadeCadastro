<!-- excluir.php -->
<?php
    // Verifica se o parâmetro ID foi passado
    if(!empty($_GET['id'])) {
        include_once('config.php'); // Inclui configurações do banco

        $id = $_GET['id']; // Obtém ID da URL

        // Query para selecionar o usuário
        $selecionarSql = "SELECT * FROM usuarios WHERE id = $id";
        $resultado = $conexao->query($selecionarSql);

        // Se encontrar registros
        if($resultado->num_rows > 0) {
            // Query para deletar o usuário
            $excluirSql = "DELETE FROM usuarios WHERE id = $id";
            $excluirResultado = $conexao->query($excluirSql);
        }        
    }
    // Redireciona de volta para o sistema após exclusão
    header('Location: sistema.php');