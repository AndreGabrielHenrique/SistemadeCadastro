<!-- excluir.php -->
 <?php
    session_start(); // Inicia a sessão PHP (necessário para acessar variáveis de sessão)
    include_once('config.php'); // Inclui arquivo de configuração do banco de dados

    // Verifica se o usuário está logado
    if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        header('Location: index.php'); // Redireciona para página inicial se não estiver logado
        exit(); // Interrompe execução do script
    }

    // VERIFICAÇÃO DE EXISTÊNCIA DO USUÁRIO LOGADO
    $id_usuario_logado = $_SESSION['id_usuario']; // Obtém ID do usuário logado da sessão
    
    $stmt_verifica = $conexao->prepare("SELECT id FROM usuarios WHERE id = ?"); // Prepara query para verificar se usuário ainda existe no banco
    $stmt_verifica->bind_param("i", $id_usuario_logado); // Vincula parâmetro inteiro (ID do usuário)
    $stmt_verifica->execute(); // Executa a query
    $stmt_verifica->store_result(); // Armazena resultado da query
    
    if($stmt_verifica->num_rows === 0) { // Se não encontrou usuário com esse ID (foi excluído)
        session_destroy(); // Destrói a sessão completamente
        $_SESSION['erro_login'] = "Sua sessão expirou porque seu usuário foi excluído."; // Define mensagem de erro
        header('Location: index.php'); // Redireciona para página inicial
        exit(); // Encerra execução
    }
    
    $stmt_verifica->close(); // Fecha statement de verificação

    // Verifica se o parâmetro ID foi passado na URL (usuário a ser excluído)
    if(!empty($_GET['id'])) {
        $id = $_GET['id']; // Obtém ID do usuário a ser excluído da URL

        // IMPEDIR QUE O USUÁRIO EXCLUA A PRÓPRIA CONTA
        if($id == $id_usuario_logado) { // Compara ID a excluir com ID do usuário logado
            $_SESSION['erro_exclusao'] = "Você não pode excluir sua própria conta!"; // Define mensagem de erro
            header('Location: sistema.php'); // Redireciona para painel do sistema
            exit(); // Encerra execução
        }

        // Query para selecionar o usuário usando prepared statement (MAIS SEGURO)
        $stmt = $conexao->prepare("SELECT * FROM usuarios WHERE id = ?"); // Prepara query para verificar se usuário existe
        $stmt->bind_param("i", $id); // Vincula parâmetro inteiro (ID do usuário a excluir)
        $stmt->execute(); // Executa query
        $resultado = $stmt->get_result(); // Obtém resultado da query

        // Se encontrar registros (usuário existe)
        if($resultado->num_rows > 0) {
            // Query para deletar o usuário usando prepared statement (MAIS SEGURO)
            $excluirStmt = $conexao->prepare("DELETE FROM usuarios WHERE id = ?"); // Prepara query de exclusão
            $excluirStmt->bind_param("i", $id); // Vincula parâmetro inteiro (ID do usuário a excluir)
            $excluirResultado = $excluirStmt->execute(); // Executa exclusão e armazena resultado (true/false)
            
            if($excluirResultado) { // Se exclusão foi bem-sucedida
                $_SESSION['sucesso_exclusao'] = "Usuário excluído com sucesso!"; // Define mensagem de sucesso
            } else { // Se houve erro na exclusão
                $_SESSION['erro_exclusao'] = "Erro ao excluir usuário."; // Define mensagem de erro
            }
            
            $excluirStmt->close(); // Fecha statement de exclusão
        }
        $stmt->close(); // Fecha statement de seleção
    }
    
    // Redireciona de volta para o sistema após exclusão
    header('Location: sistema.php'); // Volta para painel do sistema
    exit(); // Encerra execução
?>