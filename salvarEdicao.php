<!-- salvarEdicao.php -->
 <?php
    session_start(); // Inicia/retoma sessão PHP
    include_once('config.php'); // Inclui configuração do banco de dados

    // Verifica se o usuário está logado
    if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        header('Location: index.php'); // Redireciona para index se não estiver logado
        exit(); // Encerra execução
    }

    // VERIFICAÇÃO DE EXISTÊNCIA DO USUÁRIO LOGADO
    $id_logado = $_SESSION['id_usuario']; // Obtém ID do usuário logado da sessão
    
    $stmt_verifica = $conexao->prepare("SELECT id FROM usuarios WHERE id = ?"); // Query para verificar existência
    $stmt_verifica->bind_param("i", $id_logado); // Vincula parâmetro inteiro (ID)
    $stmt_verifica->execute(); // Executa query
    $stmt_verifica->store_result(); // Armazena resultado
    
    if($stmt_verifica->num_rows === 0) { // Se usuário não existe mais no banco
        session_destroy(); // Destrói sessão
        $_SESSION['erro_login'] = "Sua sessão expirou porque seu usuário foi excluído."; // Mensagem de erro
        header('Location: index.php'); // Redireciona para index
        exit(); // Encerra execução
    }
    
    $stmt_verifica->close(); // Fecha statement de verificação

    // Verifica se o formulário de atualização foi enviado
    if(isset($_POST['update'])) {
        // Obtém todos os dados do formulário
        $id = $_POST['id']; // ID do usuário sendo editado (campo hidden)
        $nome = $_POST['nome']; // Novo nome
        $email = $_POST['email']; // Novo e-mail
        $senha = $_POST['senha']; // Nova senha (pode estar vazia - opcional)
        $telefone = $_POST['telefone']; // Novo telefone
        $genero = $_POST['genero']; // Novo gênero
        $data_nascimento = $_POST['data_nascimento']; // Nova data de nascimento
        $cidade = $_POST['cidade']; // Nova cidade
        $estado = $_POST['estado']; // Novo estado
        $endereco = $_POST['endereco']; // Novo endereço

        // =================================================================
        // VALIDAÇÕES DOS CAMPOS (IGUAL AO FORMULÁRIO DE CADASTRO)
        // =================================================================
        $erros = array(); // Array para armazenar erros de validação
        
        // Validação do nome (mínimo 3 caracteres)
        if(strlen($nome) < 3) {
            $erros[] = "Nome deve ter pelo menos 3 caracteres";
        }
        
        // Validação do email (formato válido)
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = "E-mail inválido";
        }
        
        // Validação da senha (se foi preenchida - mínimo 6 caracteres)
        if(!empty($senha) && strlen($senha) < 6) {
            $erros[] = "Senha deve ter pelo menos 6 caracteres";
        }
        
        // Validação do telefone (apenas números, mínimo 10 dígitos)
        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone); // Remove não-números
        if(strlen($telefone_limpo) < 10) {
            $erros[] = "Telefone inválido";
        }
        
        // Validação da data de nascimento (não pode ser futura e limite de 120 anos)
        $data_atual = date('Y-m-d'); // Data atual
        $data_minima = date('Y-m-d', strtotime('-120 years')); // 120 anos atrás
        if($data_nascimento > $data_atual) {
            $erros[] = "Data de nascimento não pode ser futura";
        }
        if($data_nascimento < $data_minima) {
            $erros[] = "Data de nascimento inválida";
        }
        
        // =================================================================
        // VERIFICAÇÃO DE EMAIL DUPLICADO (EXCLUINDO O PRÓPRIO USUÁRIO)
        // =================================================================
        if(empty($erros)) { // Só verifica duplicidade se não houver outros erros
            $stmt_verifica = $conexao->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            // Verifica se e-mail já existe, excluindo o próprio usuário (id != ?)
            if($stmt_verifica) {
                $stmt_verifica->bind_param("si", $email, $id); // Vincula e-mail (string) e ID (inteiro)
                $stmt_verifica->execute(); // Executa query
                $stmt_verifica->store_result(); // Armazena resultado
                
                if($stmt_verifica->num_rows > 0) { // Se encontrou outro usuário com mesmo e-mail
                    $erros[] = "E-mail já cadastrado por outro usuário. Por favor, escolha outro e-mail.";
                }
                $stmt_verifica->close(); // Fecha statement
            }
        }

        // Se houver erros, redireciona de volta com os erros
        if(!empty($erros)) {
            $_SESSION['erros_edicao'] = $erros; // Armazena erros na sessão
            header("Location: editar.php?id=$id"); // Redireciona de volta à página de edição
            exit(); // Encerra execução
        }

        // =================================================================
        // PREPARED STATEMENT PARA UPDATE (COM OU SEM SENHA)
        // =================================================================
        if(!empty($senha)) {
            // Se senha foi fornecida, atualiza com hash
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT); // Cria hash da nova senha
            
            // Query de UPDATE com todos os campos incluindo senha
            $sql = "UPDATE usuarios SET 
                    nome = ?, 
                    email = ?, 
                    senha = ?, 
                    telefone = ?, 
                    genero = ?, 
                    data_nascimento = ?, 
                    cidade = ?, 
                    estado = ?, 
                    endereco = ? 
                    WHERE id = ?"; // 10 placeholders (9 strings + 1 inteiro)
            
            $stmt = $conexao->prepare($sql); // Prepara statement
            $stmt->bind_param("sssssssssi", $nome, $email, $senha_hash, $telefone, 
                            $genero, $data_nascimento, $cidade, $estado, $endereco, $id);
            // 9 strings (s) + 1 inteiro (i) = sssssssssi
        } else {
            // Se senha não foi fornecida, mantém a atual (não atualiza campo senha)
            $sql = "UPDATE usuarios SET 
                    nome = ?, 
                    email = ?, 
                    telefone = ?, 
                    genero = ?, 
                    data_nascimento = ?, 
                    cidade = ?, 
                    estado = ?, 
                    endereco = ? 
                    WHERE id = ?"; // 9 placeholders (8 strings + 1 inteiro)
            
            $stmt = $conexao->prepare($sql); // Prepara statement
            $stmt->bind_param("ssssssssi", $nome, $email, $telefone, 
                            $genero, $data_nascimento, $cidade, $estado, $endereco, $id);
            // 8 strings (s) + 1 inteiro (i) = ssssssssi
        }

        // Executa a query de UPDATE
        if($stmt->execute()) { // Se execução foi bem-sucedida
            // Atualiza a sessão se o usuário estiver editando seu próprio perfil
            if($_SESSION['id_usuario'] == $id) { // Verifica se ID editado é do usuário logado
                $_SESSION['email_usuario'] = $email; // Atualiza e-mail na sessão
                $_SESSION['nome_usuario'] = $nome; // Atualiza nome na sessão
            }
            $_SESSION['sucesso_edicao'] = "Registro atualizado com sucesso!"; // Mensagem de sucesso
        } else {
            // Fallback para erro de duplicidade (caso a verificação anterior tenha falhado)
            if($stmt->errno == 1062) { // Código de erro MySQL para duplicidade (unique constraint)
                $_SESSION['erros_edicao'] = array("E-mail já cadastrado por outro usuário");
            } else {
                $_SESSION['erro_edicao'] = "Erro ao atualizar: " . $stmt->error; // Outros erros MySQL
            }
            header("Location: editar.php?id=$id"); // Redireciona de volta em caso de erro
            exit(); // Encerra execução
        }

        $stmt->close(); // Fecha statement
    }
    
    header('Location: sistema.php'); // Redireciona de volta ao sistema após sucesso
    exit(); // Encerra execução
?>