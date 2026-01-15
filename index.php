<!-- index.php -->
<?php
session_start(); // Inicia a sessão PHP

// Verifica se o usuário está logado usando o padrão unificado
if(isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header('Location: sistema.php'); // Redireciona para o sistema
    exit(); // Interrompe a execução
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Configurações básicas da página -->
    <meta charset="UTF-8"> <!-- Define codificação de caracteres como UTF-8 (suporta acentos) -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configura viewport para responsividade -->
    <title>Sistema de Cadastro</title> <!-- Título exibido na aba do navegador -->
    <style>
        /* Estilo geral da página */
        body{
            font-family: Arial, Helvetica, sans-serif; /* Fonte padrão sem serifa */
            background: linear-gradient(to right, rgb(20, 147, 220), rgb(17, 54, 71)); /* Gradiente azul diagonal */
            text-align: center; /* Centraliza todo o texto */
            color: white; /* Cor do texto branca */
        }

        /* Container dos botões */
        .box{
            position: absolute; /* Posicionamento absoluto na página */
            top: 50%; /* Posiciona 50% do topo */
            left: 50%; /* Posiciona 50% da esquerda */
            transform: translate(-50%, -50%); /* Ajusta centro exato (compensa dimensões do elemento) */
            background-color: rgba(0, 0, 0, .6); /* Fundo preto semi-transparente (60% opacidade) */
            padding: 30px; /* Espaçamento interno de 30px em todas as direções */
            border-radius: 10px; /* Bordas arredondadas */
        }

        /* Estilo dos links/buttons */
        a{
            text-decoration: none; /* Remove sublinhado padrão dos links */
            color: white; /* Cor do texto branca */
            border: 3px solid dodgerblue; /* Borda azul de 3px */
            border-radius: 10px; /* Bordas arredondadas nos botões */
            padding: 10px; /* Espaçamento interno dos botões */
            margin: 0 10px; /* Margem horizontal de 10px entre botões */
            transition: all 0.3s ease; /* Transição suave de 0.3s para todas as propriedades */
        }

        /* Efeito hover nos botões */
        a:hover{
            background-color: dodgerblue; /* Muda fundo para azul ao passar mouse */
            transform: scale(1.05); /* Aumenta tamanho em 5% */
        }

        /* Estilo do alerta de sucesso (para logout) */
        .alert-success {
            position: fixed; /* Posição fixa na tela (não rola com a página) */
            top: 20px; /* Distância do topo da tela */
            left: 50%; /* Centraliza horizontalmente */
            transform: translateX(-50%); /* Ajusta centralização exata */
            background-color: #28a745; /* Verde do Bootstrap para sucesso */
            color: white; /* Texto branco */
            padding: 15px 25px; /* Padding: 15px vertical, 25px horizontal */
            border-radius: 10px; /* Bordas arredondadas */
            box-shadow: 0 4px 12px rgba(0,0,0,0.3); /* Sombra com 4px de deslocamento, 12px de blur */
            z-index: 1000; /* Garante que fique acima de outros elementos */
            animation: slideDown 0.5s ease-out; /* Animação de entrada de 0.5s com easing out */
            display: flex; /* Layout flex para alinhar conteúdo */
            align-items: center; /* Centraliza verticalmente */
            gap: 10px; /* Espaço de 10px entre elementos filhos */
            max-width: 400px; /* Largura máxima de 400px */
            width: 90%; /* Largura de 90% da tela (responsivo) */
        }

        .alert-success .close-btn {
            background: none; /* Remove fundo padrão do botão */
            border: none; /* Remove borda padrão */
            color: white; /* Cor do ícone branca */
            font-size: 20px; /* Tamanho do ícone "X" */
            cursor: pointer; /* Cursor de ponteiro ao passar mouse */
            margin-left: auto; /* Empurra botão para direita (flexbox) */
            padding: 0; /* Remove padding padrão */
            line-height: 1; /* Altura da linha igual ao tamanho da fonte */
        }

        .alert-success .close-btn:hover {
            color: #e6e6e6; /* Cor mais clara ao passar mouse */
        }

        @keyframes slideDown {
            from {
                opacity: 0; /* Inicia invisível */
                transform: translate(-50%, -30px); /* Começa 30px acima da posição final */
            }
            to {
                opacity: 1; /* Termina totalmente visível */
                transform: translate(-50%, 0); /* Posição final */
            }
        }

        /* Media queries para responsividade */

        /* Para tablets (até 768px de largura) */
        @media screen and (max-width: 768px) {
            .box {
                padding: 20px; /* Reduz padding para 20px */
            }
            a {
                padding: 8px; /* Reduz padding dos botões */
                font-size: 14px; /* Reduz tamanho da fonte */
                margin: 5px; /* Reduz margem entre botões */
            }
            
            .alert-success {
                top: 10px; /* Posição mais alta */
                padding: 12px 20px; /* Padding reduzido */
                font-size: 14px; /* Fonte menor */
            }
        }

        /* Para celulares (até 480px de largura) */
        @media screen and (max-width: 480px) {
            .box {
                padding: 15px; /* Padding ainda menor */
                display: flex; /* Ativa flexbox */
                flex-direction: column; /* Organiza filhos em coluna (empilha verticalmente) */
                gap: 10px; /* Espaço de 10px entre elementos filhos */
                width: 90%; /* Ocupa 90% da largura disponível */
            }
            a {
                width: 100%; /* Botões ocupam toda a largura do container */
                box-sizing: border-box; /* Inclui padding e border na largura total */
                margin: 0; /* Remove margens laterais */
            }
            
            .alert-success {
                top: 5px; /* Posição mais próxima do topo */
                width: 95%; /* Ocupa quase toda a largura */
                padding: 10px 15px; /* Padding mínimo */
            }
        }
    </style>
</head>
<body>
    <!-- Alerta de sucesso do logout -->
    <?php if(isset($_GET['logout']) && $_GET['logout'] == 'success'): ?> <!-- Verifica parâmetro na URL -->
    <div class="alert-success" id="logoutAlert"> <!-- Container do alerta com ID para referência JavaScript -->
        <span>✅</span> <!-- Ícone de check (sucesso) -->
        <span>Logout realizado com sucesso! Até logo!</span> <!-- Mensagem de sucesso -->
        <button class="close-btn" onclick="document.getElementById('logoutAlert').style.display='none'">&times;</button> <!-- Botão X para fechar alerta -->
    </div>
    <?php endif; ?>

    <!-- Container principal com botões -->
    <div class="box">
        <!-- Links principais de navegação -->
        <a href="login.php">Login</a> <!-- Link para página de login -->
        <a href="formulario.php">Cadastre-se</a> <!-- Link para página de cadastro -->
    </div>

    <script>
        // Fechar alerta automaticamente após 5 segundos
        document.addEventListener('DOMContentLoaded', function() { // Aguarda carregamento completo do DOM
            const alert = document.getElementById('logoutAlert'); // Obtém referência ao alerta
            if (alert) { // Verifica se alerta existe na página
                setTimeout(function() { // Define timeout de 5 segundos
                    alert.style.opacity = '0'; // Inicia fade out (opacidade 0)
                    alert.style.transform = 'translate(-50%, -30px)'; // Move para cima durante fade out
                    setTimeout(function() {
                        alert.style.display = 'none'; // Esconde completamente após animação
                    }, 500); // Aguarda 500ms (meio segundo) antes de esconder
                }, 5000); // 5000ms = 5 segundos
            }
            
            // Remover parâmetro da URL para não aparecer no refresh
            if (window.history.replaceState && window.location.search.includes('logout=success')) {
                // Verifica se browser suporta replaceState e se URL contém parâmetro de logout
                const cleanUrl = window.location.pathname; // Obtém apenas o caminho (ex: "/index.php")
                window.history.replaceState({}, document.title, cleanUrl); // Substitui URL sem recarregar página
            }
        });
    </script>
</body>
</html>