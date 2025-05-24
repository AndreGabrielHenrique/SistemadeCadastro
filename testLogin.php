<!-- testLogin.php -->
<?php
    session_start(); // Inicia a sessão
    
    // Verifica se formulário foi enviado e campos preenchidos
    if(isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
        include_once('config.php'); // Conexão com o banco

        // Obtém credenciais
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // Query de autenticação
        $sql = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
        $resultado = $conexao->query($sql);

        // Verifica se encontrou resultados
        if(mysqli_num_rows($resultado) < 1) {
            unset($_SESSION['email']); // Limpa sessão
            unset($_SESSION['senha']);
            header('Location: login.php'); // Redireciona para login
        }
        else {
            $_SESSION['email'] = $email; // Armazena e-mail na sessão
            $_SESSION['senha'] = $senha; // Armazena senha na sessão
            header('Location: sistema.php'); // Redireciona para painel
        }
    }
    else {
        header('Location: login.php'); // Redireciona se acesso direto
    }