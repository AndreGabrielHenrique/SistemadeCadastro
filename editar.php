<!-- editar.php -->
<?php
    // Verifica se o parâmetro 'id' foi enviado via GET e não está vazio
    if(!empty($_GET['id'])) {
        // Inclui o arquivo de configuração do banco de dados
        include_once('config.php');

        // Armazena o ID recebido da URL
        $id = $_GET['id'];

        // Query SQL segura usando prepared statement
        $stmt = $conexao->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        // Linha comentada para debug (exibir resultado bruto da query)
        // print_r($resultado);

        // Verifica se a query retornou resultados
        if($resultado->num_rows > 0) {
            // Loop para processar cada linha de resultado
            while($dados_usuario = mysqli_fetch_assoc($resultado)) {
                // Atribui cada campo do banco a variáveis PHP
                $nome = $dados_usuario['nome'];          // Nome do usuário
                $email = $dados_usuario['email'];        // E-mail do usuário
                $senha = $dados_usuario['senha'];        // Senha do usuário
                $telefone = $dados_usuario['telefone'];  // Telefone do usuário
                $genero = $dados_usuario['genero'];      // Gênero do usuário
                $data_nascimento = $dados_usuario['data_nascimento']; // Data de nascimento
                $cidade = $dados_usuario['cidade'];      // Cidade do usuário
                $estado = $dados_usuario['estado'];      // Estado do usuário
                $endereco = $dados_usuario['endereco'];  // Endereço do usuário
            }
        }
        else {
            // Redireciona se nenhum usuário for encontrado
            header('Location: sistema.php');
        }
    }
    else {
        // Redireciona se nenhum ID foi fornecido
        header('Location: sistema.php');
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- Define o conjunto de caracteres -->
    <meta charset="UTF-8">
    <!-- Configura viewport para responsividade -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Título da página -->
    <title>Editar</title>
    <style>
        /* Estilização do corpo da página */
        body {
            font-family: Arial, Helvetica, sans-serif; /* Fonte padrão */
            background-image: linear-gradient(to right, rgb(20, 147, 220), rgb(17, 54, 71)); /* Gradiente de fundo */
        }

        /* Container principal do formulário */
        .box {
            background-color: rgba(0, 0, 0, .6); /* Fundo semi-transparente */
            position: absolute; /* Posicionamento absoluto */
            top: 50%; /* Alinha ao centro vertical */
            left: 50%; /* Alinha ao centro horizontal */
            transform: translate(-50%, -50%); /* Ajuste fino de centralização */
            padding: 15px; /* Espaçamento interno */
            border-radius: 15px; /* Bordas arredondadas */
            width: 20%; /* Largura relativa */
            color: #fff; /* Cor do texto */
            min-width: 400px; /* Largura mínima absoluta */
            overflow-y: auto; /* Scroll vertical quando necessário */
            max-height: 90vh; /* Altura máxima relativa à tela */

            /* Esconde a barra de scroll */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE/Edge */
        }

        /* Esconde scrollbar para WebKit (Chrome, Safari, Opera) */
        .box::-webkit-scrollbar {
            display: none;
        }

        /* Estilização do elemento fieldset */
        fieldset{
            border: 3px solid dodgerblue; /* Borda estilizada */
            padding: 20px; /* Espaçamento interno */
        }

        /* Estilização da legenda do fieldset */
        legend {
            border: 1px solid dodgerblue; /* Borda da legenda */
            padding: 10px; /* Espaçamento interno */
            text-align: center; /* Alinhamento centralizado */
            background-color: dodgerblue; /* Cor de fundo */
            border-radius: 8px; /* Bordas arredondadas */
        }

        /* Container de cada campo de entrada */
        .inputBox {
            position: relative; /* Permite posicionamento absoluto dos filhos */
            margin-bottom: 20px; /* Espaço entre os campos */
        }

        /* Estilização dos inputs de usuário */
        .inputUser {
            width: 100%; /* Largura total */
            border: none; /* Remove borda padrão */
            border-bottom: 1px solid #fff; /* Linha inferior branca */
            background: none; /* Fundo transparente */
            color: #fff; /* Cor do texto */
            font-size: 15px; /* Tamanho da fonte */
            letter-spacing: 2px; /* Espaçamento entre caracteres */
            outline: none; /* Remove contorno ao focar */
            box-sizing: border-box; /* Modelo de caixa inclui padding */
        }

        /* Estilização das labels */
        .labelInput {
            position: absolute; /* Posicionamento absoluto relativo ao container */
            top: 0; /* Alinhamento superior */
            left: 0; /* Alinhamento esquerdo */
            pointer-events: none; /* Permite interação com o input abaixo */
            transition: .5s; /* Animação de transição */
            color: #fff; /* Cor inicial do texto */
        }

        /* Efeitos quando o input está focado ou preenchido */
        .inputUser:focus ~ .labelInput,
        .inputUser:valid ~ .labelInput {
            top: -20px; /* Move a label para cima */
            color: dodgerblue; /* Muda cor do texto */
            font-size: 12px; /* Reduz tamanho da fonte */
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

        /* Estilização específica para o campo de data */
        #data_nascimento {
            padding: 8px; /* Espaçamento interno */
            border: none; /* Remove borda padrão */
            border-radius: 10px; /* Bordas arredondadas */
            outline: none; /* Remove contorno ao focar */
            font-size: 15px; /* Tamanho da fonte */
        }

        /* Estilização do botão de atualização */
        #update{
            background-image: linear-gradient(to right, rgb(0, 92, 197), rgb(90, 20, 220)); /* Gradiente de fundo */
            border: none; /* Remove borda */
            width: 100%; /* Largura total */
            padding: 15px; /* Espaçamento interno */
            color: #fff; /* Cor do texto */
            font-size: 15px; /* Tamanho da fonte */
            cursor: pointer; /* Cursor tipo ponteiro */
            border-radius: 10px; /* Bordas arredondadas */
        }

        /* Efeito hover do botão */
        #update:hover{
            background-image: linear-gradient(to right, rgb(0, 80, 172), rgb(80, 19, 195)); /* Gradiente alterado */
        }
        
        /* Media query para telas até 992px */
        @media screen and (max-width: 992px) {
            .box {
                width: 70%; /* Largura maior */
                position: relative; /* Altera posicionamento */
                top: auto; /* Reseta posição vertical */
                left: auto; /* Reseta posição horizontal */
                transform: none; /* Remove transformação anterior */
                margin: 50px auto; /* Centraliza horizontalmente */
                min-width: 300px; /* Nova largura mínima */
            }
            
            fieldset {
                padding: 15px; /* Reduz espaçamento interno */
            }
        }

        /* Media query para telas até 768px */
        @media screen and (max-width: 768px) {
            .box {
                width: 85%; /* Largura ainda maior */
                margin: 30px auto; /* Ajuste de margens */
            }
            
            .inputUser {
                font-size: 16px !important; /* Aumenta tamanho da fonte */
            }
            
            #update {
                padding: 12px; /* Reduz espaçamento interno */
                font-size: 16px; /* Aumenta tamanho da fonte */
            }
        }

        /* Media query para telas até 480px */
        @media screen and (max-width: 480px) {
            body {
                padding: 15px; /* Adiciona padding ao corpo */
            }
            
            .box {
                width: 100%; /* Ocupa toda a largura */
                margin: 20px 0; /* Ajuste de margens */
                padding: 10px; /* Reduz espaçamento interno */
            }
            
            legend {
                font-size: 1.2rem; /* Aumenta tamanho da fonte */
                padding: 8px; /* Reduz espaçamento interno */
            }
            
            #data_nascimento {
                width: 100%; /* Ocupa largura total */
                font-size: 14px; /* Reduz tamanho da fonte */
            }
            
            .inputBox {
                margin-bottom: 25px; /* Aumenta espaçamento entre campos */
            }
            
            p {
                margin: 10px 0; /* Ajuste de margens */
            }
        }
    </style>
</head>
<body>
    <!-- Link de retorno -->
    <a href="sistema.php">Voltar</a>
    <!-- Container principal -->
    <div class="box">
        <!-- Formulário de edição -->
        <form action="salvarEdicao.php" method="POST">
            <fieldset>
                <!-- Legenda/título do formulário -->
                <legend><b>Editar Cliente</b></legend>
                <br>
                <!-- Campo Nome -->
                <div class="inputBox">
                    <input type="text" name="nome" id="nome" class="inputUser" value="<?php echo $nome; ?>" required>
                    <label for="nome" class="labelInput">Nome completo</label>
                </div>
                <br><br>
                <!-- Campo Senha -->
                <div class="inputBox">
                    <input type="password" name="senha" id="senha" class="inputUser" value="<?php echo $senha; ?>" required>
                    <label for="senha" class="labelInput">Senha</label>
                </div>
                <br><br>
                <!-- Campo E-mail -->
                <div class="inputBox">
                    <input type="text" name="email" id="email" class="inputUser" value="<?php echo $email; ?>" required>
                    <label for="email" class="labelInput">E-mail</label>
                </div>
                <br><br>
                <!-- Campo Telefone -->
                <div class="inputBox">
                    <input type="tel" name="telefone" id="telefone" class="inputUser" value="<?php echo $telefone; ?>" required>
                    <label for="telefone" class="labelInput">Telefone</label>
                </div>
                <!-- Seção de Gênero -->
                <p>Sexo:</p>
                <!-- Opção Feminino -->
                <input type="radio" name="genero" value="feminino" id="feminino" <?php echo ($genero == 'feminino') ? 'checked' : ''; ?> required>
                <label for="feminino">Feminino</label>
                <br>
                <!-- Opção Masculino -->
                <input type="radio" name="genero" value="masculino" id="masculino" <?php echo ($genero == 'masculino') ? 'checked' : ''; ?> required>
                <label for="masculino">Masculino</label>
                <br>
                <!-- Opção Outro -->
                <input type="radio" name="genero" value="outro" id="outro" <?php echo ($genero == 'outro') ? 'checked' : ''; ?> required>
                <label for="outro">Outro</label>
                <br><br>
                <!-- Campo Data de Nascimento -->
                <label for="data_nascimento"><b>Data de Nascimento:</b></label>
                <input type="date" name="data_nascimento" id="data_nascimento"  value="<?php echo $data_nascimento; ?>"required>
                <br><br><br>
                <!-- Campo Cidade -->
                <div class="inputBox">
                    <input type="text" name="cidade" id="cidade" class="inputUser" value="<?php echo $cidade; ?>" required>
                    <label for="cidade" class="labelInput">Cidade</label>
                </div>
                <br><br>
                <!-- Campo Estado -->
                <div class="inputBox">
                    <input type="text" name="estado" id="estado" class="inputUser" value="<?php echo $estado; ?>" required>
                    <label for="estado" class="labelInput">Estado</label>
                </div>
                <br><br>
                <!-- Campo Endereço -->
                <div class="inputBox">
                    <input type="text" name="endereco" id="endereco" class="inputUser" value="<?php echo $endereco; ?>" required>
                    <label for="endereco" class="labelInput">Endereço</label>
                </div>
                <br><br>
                <!-- Campo hidden para envio do ID -->
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <!-- Botão de submissão -->
                <input type="submit" name="update" id="update">
            </fieldset>
        </form>
    </div>
</body>
</html>