<!-- logout.php -->
<?php
    session_start(); // Inicia a sessão PHP (necessário para acessar/destruir sessão)
    
    // Armazena mensagem de sucesso ANTES de destruir a sessão
    $_SESSION['sucesso_logout'] = "Logout realizado com sucesso! Até logo!"; // Mensagem armazenada na sessão
    
    session_unset(); // Remove todas as variáveis de sessão (dados do usuário)
    session_destroy(); // Destrói a sessão completamente (ID da sessão)
    
    // Redireciona para a página inicial COM a mensagem na URL
    header('Location: index.php?logout=success'); // Redireciona com parâmetro para exibir alerta
    exit(); // Encerra o script imediatamente após redirecionamento
?>