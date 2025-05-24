<!-- salvarEdicao.php -->
<?php
    include_once('config.php'); // Conexão com o banco

    // Verifica se o formulário de atualização foi enviado
    if(isset($_POST['update'])) {
        // Obtém todos os dados do formulário
        $id = $_POST['id']; // ID do usuário
        $nome = $_POST['nome']; // Nome completo
        $email = $_POST['email']; // E-mail
        $senha = $_POST['senha']; // Senha
        $telefone = $_POST['telefone']; // Telefone
        $genero = $_POST['genero']; // Gênero
        $data_nascimento = $_POST['data_nascimento']; // Data de nascimento
        $cidade = $_POST['cidade']; // Cidade
        $estado = $_POST['estado']; // Estado
        $endereco = $_POST['endereco']; // Endereço

        // Query de atualização
        $atualizarSql = "UPDATE usuarios SET 
            nome='$nome', 
            email='$email', 
            senha='$senha', 
            telefone='$telefone', 
            genero='$genero', 
            data_nascimento='$data_nascimento', 
            cidade='$cidade', 
            estado='$estado', 
            endereco='$endereco' 
            WHERE id=$id"; // Atualiza registro específico

        $resultado = $conexao->query($atualizarSql); // Executa a query
    }
    header('Location: sistema.php'); // Redireciona de volta