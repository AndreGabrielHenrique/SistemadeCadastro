<!-- testLogin.php -->
<?php
session_start(); // Inicia sessão PHP
    
// Verifica se formulário foi enviado e campos preenchidos
if(isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
    include_once('config.php'); // Conexão com o banco

    // Obtém credenciais do formulário
    $email = trim($_POST['email']); // Remove espaços extras do e-mail
    $senha = $_POST['senha']; // Senha em texto plano (será verificada)

    // Query de autenticação usando prepared statement (prevenção SQL injection)
    $stmt = $conexao->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email); // Vincula e-mail como parâmetro string
    $stmt->execute(); // Executa query
    $resultado = $stmt->get_result(); // Obtém resultado

    // Verifica se encontrou exatamente um usuário com esse e-mail
    if($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc(); // Obtém dados do usuário como array associativo
        
        // Verifica a senha (compatível com hash e texto plano)
        // Primeiro tenta verificar como hash (usuários novos/atualizados)
        if(password_verify($senha, $usuario['senha'])) {
            // Login bem-sucedido com senha hash
            $_SESSION['logado'] = true; // Marca como logado
            $_SESSION['id_usuario'] = $usuario['id']; // Armazena ID do usuário
            $_SESSION['email_usuario'] = $email; // Armazena e-mail
            $_SESSION['nome_usuario'] = $usuario['nome']; // Armazena nome
            $_SESSION['login_sucesso'] = "Login realizado com sucesso!"; // Mensagem de boas-vindas
            
            header('Location: sistema.php'); // Redireciona para painel do sistema
            exit(); // Encerra execução
        } 
        // Se falhar, tenta como texto plano (para usuários antigos - compatibilidade)
        else if($senha === $usuario['senha']) {
            // Login bem-sucedido com senha em texto plano
            // ATUALIZAÇÃO AUTOMÁTICA: converte senha para hash (melhoria de segurança)
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT); // Cria hash da senha
            $update_stmt = $conexao->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            $update_stmt->bind_param("si", $senha_hash, $usuario['id']); // Vincula hash e ID
            $update_stmt->execute(); // Executa atualização (senha agora está hasheada)
            
            // Define variáveis de sessão (mesmo processo acima)
            $_SESSION['logado'] = true;
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['email_usuario'] = $email;
            $_SESSION['nome_usuario'] = $usuario['nome'];
            $_SESSION['login_sucesso'] = "Login realizado com sucesso!";
            
            header('Location: sistema.php');
            exit();
        } else {
            // SENHA INCORRETA - USANDO SESSÃO EM VEZ DE URL (melhor segurança/UX)
            unset($_SESSION['logado']); // Remove variável de sessão (limpeza)
            session_destroy(); // Destrói sessão atual completamente
            session_start(); // Inicia nova sessão para mensagem de erro
            $_SESSION['erro_login'] = "Senha incorreta. Por favor, tente novamente.";
            header('Location: login.php'); // Redireciona para login com mensagem
            exit();
        }
    } else {
        // E-MAIL NÃO CADASTRADO - USANDO SESSÃO EM VEZ DE URL
        unset($_SESSION['logado']);
        session_destroy();
        session_start();
        $_SESSION['erro_login'] = "E-mail não cadastrado. Verifique ou cadastre-se.";
        header('Location: login.php');
        exit();
    }
} else {
    // CAMPOS NÃO PREENCHIDOS - USANDO SESSÃO EM VEZ DE URL
    $_SESSION['erro_login'] = "Por favor, preencha todos os campos.";
    header('Location: login.php');
    exit();
}
?>