<!-- testLogin.php -->
<?php
    session_start(); // Inicia a sessão
    
    // Verifica se formulário foi enviado e campos preenchidos
    if(isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
        include_once('config.php'); // Conexão com o banco

        // Obtém credenciais
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // Query de autenticação usando prepared statement
        $stmt = $conexao->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        // Verifica se encontrou resultados
        if($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            
            // Comparação direta da senha (TEXTO PURO)
            if($senha === $usuario['senha']) {
                // Define as variáveis de sessão PADRÃO
                $_SESSION['logado'] = true;
                $_SESSION['id_usuario'] = $usuario['id'];
                $_SESSION['email_usuario'] = $email;
                $_SESSION['nome_usuario'] = $usuario['nome'];
                
                header('Location: sistema.php');
                exit();
            }
        }

        // FALHA NA AUTENTICAÇÃO
        unset($_SESSION['logado']);
        session_destroy();
        header('Location: login.php?erro=1');
        exit();
    }
    else {
        header('Location: login.php');
        exit();
    }
?>