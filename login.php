<!-- login.php -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- Configurações básicas da página -->
    <meta charset="UTF-8"> <!-- Codificação de caracteres -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsividade -->
    <title>Tela de Login</title> <!-- Título da página -->
    <style>
        /* Estilos gerais */
        body{
            font-family: Arial, Helvetica, sans-serif; /* Fonte padrão */
            background: linear-gradient(to right, rgb(20, 147, 220), rgb(17, 54, 71)); /* Gradiente de fundo */
        }

        /* Container do formulário */
        div{
            background-color: rgba(0, 0, 0, .6); /* Fundo semi-transparente */
            position: absolute; /* Posicionamento absoluto */
            top: 50%; left: 50%; /* Centralização */
            transform: translate(-50%, -50%); /* Ajuste fino de posição */
            padding: 80px; /* Espaçamento interno */
            border-radius: 15px; /* Bordas arredondadas */
            color: #fff; /* Cor do texto */
        }

        /* Estilos dos inputs */
        input{
            padding: 15px; /* Espaçamento interno */
            border: none; /* Remove borda padrão */
            outline: none; /* Remove contorno ao focar */
            font-size: 15px; /* Tamanho da fonte */
        }

        /* Estilo do botão de login */
        .inputSubmit{
            background-color: dodgerblue; /* Cor de fundo */
            border: none; /* Remove borda */
            padding: 15px; /* Espaçamento interno */
            width: 100%; /* Largura total */
            border-radius: 10px; /* Bordas arredondadas */
            color: #fff; /* Cor do texto */
            font-size: 15px; /* Tamanho da fonte */
            transition: background-color 0.3s; /* Transição suave */
        }

        /* Efeito hover do botão */
        .inputSubmit:hover{
            background-color: deepskyblue; /* Cor alterada */
            cursor: pointer; /* Cursor de clique */
        }

        /* Media query para tablets */
        @media screen and (max-width: 768px) {
            div {
                padding: 40px; /* Reduz espaçamento */
                width: 70%; /* Largura maior */
            }
            input {
                width: 100%; /* Largura total */
                box-sizing: border-box; /* Modelo de caixa */
            }
        }

        /* Media query para celulares */
        @media screen and (max-width: 480px) {
            div {
                padding: 30px; /* Espaçamento menor */
                width: 90%; /* Largura quase total */
            }
            h1 {
                font-size: 24px; /* Reduz tamanho do título */
            }
            .inputSubmit {
                padding: 10px; /* Padding reduzido */
            }
        }
    </style>
</head>
<body>
    <!-- Link de retorno -->
    <a href="index.php">Voltar</a>
    
    <!-- Container do formulário -->
    <div>
        <form action="testLogin.php" method="POST">
            <h1>Login</h1> <!-- Título -->
            <!-- Campo E-mail -->
            <input type="text" name="email" placeholder="E-mail">
            <br><br>
            <!-- Campo Senha -->
            <input type="password" name="senha" placeholder="Senha">
            <br><br>
            <!-- Botão de envio -->
            <input class="inputSubmit" type="submit" name="submit" value="Enviar">
        </form>
    </div>
</body>
</html>