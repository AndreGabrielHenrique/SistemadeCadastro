<!-- home.php  -->
<?php
    session_start(); // Inicia a sessão

    // Verifica se o usuário já está logado
    if(isset($_SESSION['email']) && isset($_SESSION['senha'])) {
        header('Location: sistema.php'); // Redireciona para o sistema
        exit(); // Interrompe a execução do script
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Configurações básicas da página -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Cadastro</title>
    <style>
        /* Estilo geral da página */
        body{
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(to right, rgb(20, 147, 220), rgb(17, 54, 71));
            text-align: center;
            color: white;
        }

        /* Container dos botões */
        .box{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); /* Centralização perfeita */
            background-color: rgba(0, 0, 0, .6);
            padding: 30px;
            border-radius: 10px;
        }

        /* Estilo dos links/buttons */
        a{
            text-decoration: none;
            color: white;
            border: 3px solid dodgerblue;
            border-radius: 10px;
            padding: 10px;
            margin: 0 10px; /* Espaço entre os botões */
            transition: all 0.3s ease; /* Transição suave */
        }

        /* Efeito hover */
        a:hover{
            background-color: dodgerblue;
            transform: scale(1.05); /* Efeito de zoom */
        }

        /* Media queries para responsividade */
        @media screen and (max-width: 768px) {
            .box {
                padding: 20px; /* Reduz padding */
            }
            a {
                padding: 8px;
                font-size: 14px; /* Reduz tamanho da fonte */
                margin: 5px; /* Reduz espaço entre botões */
            }
        }

        @media screen and (max-width: 480px) {
            .box {
                padding: 15px;
                display: flex;
                flex-direction: column; /* Empilha verticalmente */
                gap: 10px; /* Espaço entre elementos */
                width: 90%; /* Largura maior */
            }
            a {
                width: 100%; /* Ocupa toda a largura */
                box-sizing: border-box; /* Considera padding na largura */
                margin: 0; /* Remove margens laterais */
            }
        }
    </style>
</head>
<body>
    <div class="box">
        <!-- Links principais -->
        <a href="login.php">Login</a>
        <a href="formulario.php">Cadastre-se</a>
    </div>
</body>
</html>