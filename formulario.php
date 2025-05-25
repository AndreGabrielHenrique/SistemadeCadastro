<!-- formulario.php -->
<?php
    session_start(); // Inicia a sessão no TOPO do arquivo

    // Verifica se o usuário está logado usando o padrão unificado
    if(isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
        header('Location: sistema.php'); // Redireciona para o sistema
        exit(); // Interrompe a execução
    }

    // Verifica se o formulário foi submetido
    if(isset($_POST['submit'])) {
        /*
        Trecho comentado para debug (exibe todos os dados do formulário):
        print_r('Nome: ' . $_POST['nome']);// print_r('<br>');
        print_r('E-mail: ' . $_POST['email']);
        print_r('<br>');
        print_r('Telefone: ' . $_POST['telefone']);
        print_r('<br>');
        print_r('Sexo: ' . $_POST['genero']);
        print_r('<br>');
        print_r('Data de Nascimento: ' . $_POST['data_nascimento']);
        print_r('<br>');
        print_r('Cidade: ' . $_POST['cidade']);
        print_r('<br>');
        print_r('Estado: ' . $_POST['estado']);
        print_r('<br>');
        print_r('Endereço: ' . $_POST['endereco']);
        */

        include_once('config.php'); // Conexão com o banco de dados

        // Atribui cada campo do formulário a variáveis
        $nome = $_POST['nome'];               // Nome completo do usuário
        $email = $_POST['email'];             // E-mail do usuário
        $senha = $_POST['senha'];             // Senha do usuário
        $telefone = $_POST['telefone'];       // Telefone do usuário
        $genero = $_POST['genero'];           // Gênero do usuário
        $data_nascimento = $_POST['data_nascimento']; // Data de nascimento
        $cidade = $_POST['cidade'];           // Cidade do usuário
        $estado = $_POST['estado'];           // Estado do usuário
        $endereco = $_POST['endereco'];       // Endereço completo

        // =================================================================
        // NOVO CÓDIGO COM PREPARED STATEMENT (SEGURO CONTRA SQL INJECTION)
        // =================================================================
        $sql = "INSERT INTO usuarios(
                    nome, 
                    email, 
                    senha, 
                    telefone, 
                    genero, 
                    data_nascimento, 
                    cidade, 
                    estado, 
                    endereco
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"; // 9 placeholders

        // Prepara a declaração
        $stmt = mysqli_prepare($conexao, $sql);
        
        if($stmt) {
            // Vincula os parâmetros (9 strings - 's' repetido 9 vezes)
            mysqli_stmt_bind_param(
                $stmt, 
                "sssssssss", 
                $nome, 
                $email, 
                $senha, 
                $telefone, 
                $genero, 
                $data_nascimento, 
                $cidade, 
                $estado, 
                $endereco
            );

            // Executa a declaração
            $executado = mysqli_stmt_execute($stmt);
            
            if($executado) {
                // Busca o último ID inserido
                $novo_id = mysqli_insert_id($conexao);
                
                // Cria a sessão do usuário
                $_SESSION['logado'] = true;
                $_SESSION['id_usuario'] = $novo_id;
                $_SESSION['email_usuario'] = $email;
                $_SESSION['nome_usuario'] = $nome;
                
                header('Location: sistema.php');
                exit();
            } else {
                $erro = mysqli_stmt_error($stmt);
                $_SESSION['cadastro_erro'] = "Erro na execução: $erro";
            }

            // Fecha a declaração
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['cadastro_erro'] = "Erro na preparação: " . mysqli_error($conexao);
        }

        // Redireciona em caso de erro
        header('Location: formulario.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- Metadados básicos -->
    <meta charset="UTF-8"> <!-- Codificação de caracteres -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configuração de viewport responsiva -->
    <title>Formulário</title> <!-- Título da página -->
    <style>
        /* Estilos gerais da página */
        body {
            font-family: Arial, Helvetica, sans-serif; /* Fonte padrão */
            background-image: linear-gradient(to right, rgb(20, 147, 220), rgb(17, 54, 71)); /* Gradiente de fundo */
        }

        /* Container principal do formulário */
        .box {
            background-color: rgba(0, 0, 0, .6); /* Fundo semi-transparente */
            position: absolute; /* Posicionamento absoluto */
            top: 50%; /* Posiciona no meio vertical */
            left: 50%; /* Posiciona no meio horizontal */
            transform: translate(-50%, -50%); /* Ajuste fino de centralização */
            padding: 15px; /* Espaçamento interno */
            border-radius: 15px; /* Bordas arredondadas */
            width: 20%; /* Largura inicial (desktop) */
            color: #fff; /* Cor do texto */
            min-width: 400px; /* Largura mínima absoluta */
            overflow-y: auto; /* Habilita scroll vertical */
            max-height: 90vh; /* Altura máxima relativa */

            /* Esconde a barra de scroll */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE/Edge */
        }

        /* Esconde scrollbar para WebKit (Chrome, Safari, Opera) */
        .box::-webkit-scrollbar {
            display: none;
        }

        /* Estilização do fieldset */
        fieldset{
            border: 3px solid dodgerblue; /* Borda estilizada */
            padding: 20px; /* Espaçamento interno */
        }

        /* Estilização da legenda */
        legend {
            border: 1px solid dodgerblue; /* Borda da legenda */
            padding: 10px; /* Espaçamento interno */
            text-align: center; /* Centralização do texto */
            background-color: dodgerblue; /* Cor de fundo */
            border-radius: 8px; /* Bordas arredondadas */
        }

        /* Container de cada campo de entrada */
        .inputBox {
            position: relative; /* Permite posicionamento absoluto dos labels */
            margin-bottom: 20px; /* Espaço entre os campos */
        }

        /* Estilização dos inputs */
        .inputUser {
            width: 100%; /* Largura total */
            border: none; /* Remove borda padrão */
            border-bottom: 1px solid #fff; /* Linha inferior branca */
            background: none; /* Fundo transparente */
            color: #fff; /* Cor do texto */
            font-size: 15px; /* Tamanho da fonte */
            letter-spacing: 2px; /* Espaçamento entre letras */
            outline: none; /* Remove contorno ao focar */
            box-sizing: border-box; /* Modelo de caixa inclui padding */
        }

        /* Estilização das labels animadas */
        .labelInput {
            position: absolute; /* Posicionamento absoluto */
            top: 0; /* Alinhamento superior */
            left: 0; /* Alinhamento esquerdo */
            pointer-events: none; /* Permite interação com o input */
            transition: .5s; /* Duração da animação */
            color: #fff; /* Cor inicial */
        }

        /* Efeitos para labels quando o input está ativo */
        .inputUser:focus ~ .labelInput,
        .inputUser:valid ~ .labelInput {
            top: -20px; /* Move a label para cima */
            color: dodgerblue; /* Muda a cor */
            font-size: 12px; /* Reduz o tamanho */
        }

        /* Esconde os radios nativos */
        input[type="radio"] {
            position: absolute; /* Posicionamento absoluto */
            opacity: 0; /* Torna invisível */
            width: 0; /* Remove largura */
            height: 0; /* Remove altura */
        }

        /* Estiliza o label personalizado */
        input[type="radio"] + label {
            position: relative; /* Permite posicionamento do círculo */
            padding-left: 25px; /* Espaço para o círculo */
            cursor: pointer; /* Cursor de clique */
            display: inline-block; /* Permite alinhamento em linha */
            color: white; /* Cor do texto */
        }

        /* Cria o círculo externo */
        input[type="radio"] + label::before {
            content: ""; /* Necessário para criar o círculo */
            position: absolute; /* Posicionamento absoluto */
            left: 0; /* Alinhamento esquerdo */
            top: 2px; /* Alinhamento superior */
            width: 10px; /* Largura do círculo */
            height: 10px; /* Altura do círculo */
            border: 2px solid white; /* Borda branca */
            border-radius: 50%; /* Círculo perfeito */
            background: transparent; /* Fundo transparente */
        }

        /* Cria o ponto interno quando selecionado */
        input[type="radio"]:checked + label::before {
            background: dodgerblue; /* Cor de fundo do círculo */
        }

        /* Efeito de hover */
        input[type="radio"]:hover + label::before {
            border-color: deepskyblue; /* Cor da borda ao passar o mouse */
        }

        /* Estilo específico para o campo de data */
        #data_nascimento {
            padding: 8px; /* Espaçamento interno */
            border: none; /* Remove borda */
            border-radius: 10px; /* Bordas arredondadas */
            outline: none; /* Remove contorno */
            font-size: 15px; /* Tamanho da fonte */
        }

        /* Estilização do botão de envio */
        #submit{
            background-image: linear-gradient(to right, rgb(0, 92, 197), rgb(90, 20, 220)); /* Gradiente de cores */
            border: none; /* Remove borda */
            width: 100%; /* Largura total */
            padding: 15px; /* Espaçamento interno */
            color: #fff; /* Cor do texto */
            font-size: 15px; /* Tamanho da fonte */
            cursor: pointer; /* Cursor de clique */
            border-radius: 10px; /* Bordas arredondadas */
        }

        /* Efeito hover do botão */
        #submit:hover{
            background-image: linear-gradient(to right, rgb(0, 80, 172), rgb(80, 19, 195)); /* Gradiente alterado */
        }
        
        /* Media query para tablets */
        @media screen and (max-width: 992px) {
            .box {
                width: 70%; /* Largura maior */
                position: relative; /* Altera posicionamento */
                top: auto; /* Reseta posição vertical */
                left: auto; /* Reseta posição horizontal */
                transform: none; /* Remove transformação */
                margin: 50px auto; /* Centralização horizontal */
                min-width: 300px; /* Nova largura mínima */
            }
            
            fieldset {
                padding: 15px; /* Reduz espaçamento interno */
            }
        }

        /* Media query para celulares grandes */
        @media screen and (max-width: 768px) {
            .box {
                width: 85%; /* Aumenta largura */
                margin: 30px auto; /* Ajuste de margens */
            }
            
            .inputUser {
                font-size: 16px !important; /* Aumenta tamanho da fonte */
            }
            
            #submit {
                padding: 12px; /* Reduz padding */
                font-size: 16px; /* Aumenta fonte */
            }
        }

        /* Media query para celulares pequenos */
        @media screen and (max-width: 480px) {
            body {
                padding: 15px; /* Adiciona padding no body */
            }
            
            .box {
                width: 100%; /* Ocupa toda a largura */
                margin: 20px 0; /* Ajuste de margens */
                padding: 10px; /* Reduz padding interno */
            }
            
            legend {
                font-size: 1.2rem; /* Aumenta tamanho da fonte */
                padding: 8px; /* Reduz padding */
            }
            
            #data_nascimento {
                width: 100%; /* Largura total */
                font-size: 14px; /* Reduz tamanho da fonte */
            }
            
            .inputBox {
                margin-bottom: 25px; /* Aumenta espaçamento */
            }
            
            p {
                margin: 10px 0; /* Ajuste de margens */
            }
        }
    </style>
</head>
<body>
    <!-- Link de navegação -->
    <a href="index.php">Voltar</a>
    
    <!-- Container do formulário -->
    <div class="box">
        <form action="formulario.php" method="POST">
            <fieldset>
                <!-- Título do formulário -->
                <legend><b>Formulário de Clientes</b></legend>
                <br>
                
                <!-- Campo Nome Completo -->
                <div class="inputBox">
                    <input type="text" name="nome" id="nome" class="inputUser" required>
                    <label for="nome" class="labelInput">Nome completo</label>
                </div>
                <br><br>
                
                <!-- Campo Senha -->
                <div class="inputBox">
                    <input type="password" name="senha" id="senha" class="inputUser" required>
                    <label for="senha" class="labelInput">Senha</label>
                </div>
                <br><br>
                
                <!-- Campo E-mail -->
                <div class="inputBox">
                    <input type="text" name="email" id="email" class="inputUser" required>
                    <label for="email" class="labelInput">E-mail</label>
                </div>
                <br><br>
                
                <!-- Campo Telefone -->
                <div class="inputBox">
                    <input type="tel" name="telefone" id="telefone" class="inputUser" required>
                    <label for="telefone" class="labelInput">Telefone</label>
                </div>
                
                <!-- Seção de Gênero -->
                <p>Sexo:</p>
                <!-- Opção Feminino -->
                <input type="radio" name="genero" value="feminino" id="feminino" required>
                <label for="feminino">Feminino</label>
                <br>
                <!-- Opção Masculino -->
                <input type="radio" name="genero" value="masculino" id="masculino" required>
                <label for="masculino">Masculino</label>
                <br>
                <!-- Opção Outro -->
                <input type="radio" name="genero" value="outro" id="outro" required>
                <label for="outro">Outro</label>
                <br><br>
                
                <!-- Campo Data de Nascimento -->
                <label for="data_nascimento"><b>Data de Nascimento:</b></label>
                <input type="date" name="data_nascimento" id="data_nascimento" required>
                <br><br><br>
                
                <!-- Campo Cidade -->
                <div class="inputBox">
                    <input type="text" name="cidade" id="cidade" class="inputUser" required>
                    <label for="cidade" class="labelInput">Cidade</label>
                </div>
                <br><br>
                
                <!-- Campo Estado -->
                <div class="inputBox">
                    <input type="text" name="estado" id="estado" class="inputUser" required>
                    <label for="estado" class="labelInput">Estado</label>
                </div>
                <br><br>
                
                <!-- Campo Endereço -->
                <div class="inputBox">
                    <input type="text" name="endereco" id="endereco" class="inputUser" required>
                    <label for="endereco" class="labelInput">Endereço</label>
                </div>
                <br><br>
                
                <!-- Botão de Submissão -->
                <input type="submit" name="submit" id="submit">
            </fieldset>
        </form>
    </div>
</body>
</html>