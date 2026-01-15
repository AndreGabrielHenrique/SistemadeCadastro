<!-- login.php -->
<?php
    session_start(); // Inicia/retoma sessão PHP

    // Verifica se o usuário está logado usando o padrão unificado
    if(isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
        header('Location: sistema.php'); // Redireciona para sistema se já estiver logado
        exit(); // Encerra execução
    }

    // Recuperar mensagens da sessão
    $erro_login = isset($_SESSION['erro_login']) ? $_SESSION['erro_login'] : null; // Obtém erro de login ou null
    unset($_SESSION['erro_login']); // Remove erro da sessão após obter (evita re-exibição)
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- Configurações básicas da página -->
    <meta charset="UTF-8"> <!-- Codificação de caracteres UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsividade -->
    <title>Tela de Login</title> <!-- Título da página -->
    <style>
        /* Estilos gerais */
        body{
            font-family: Arial, Helvetica, sans-serif; /* Fonte padrão */
            background: linear-gradient(to right, rgb(20, 147, 220), rgb(17, 54, 71)); /* Gradiente de fundo */
            margin: 0; /* Remove margem padrão */
            padding: 0; /* Remove padding padrão */
            height: 100vh; /* Altura de 100% da viewport */
            display: flex; /* Flexbox para centralização */
            justify-content: center; /* Centraliza horizontalmente */
            align-items: center; /* Centraliza verticalmente */
        }

        /* Container do formulário - AJUSTADO */
        div.form-container{
            background-color: rgba(0, 0, 0, .6); /* Fundo semi-transparente */
            padding: 60px 40px; /* Reduzido padding lateral para caber o link em uma linha */
            border-radius: 15px; /* Bordas arredondadas */
            color: #fff; /* Cor do texto */
            min-width: 300px; /* Largura mínima */
            max-width: 400px; /* Largura máxima */
            width: 100%; /* Largura responsiva */
            box-sizing: border-box; /* Inclui padding na largura total */
        }

        /* Título alinhado à esquerda */
        h1 {
            text-align: left; /* Alinha texto à esquerda */
            margin-bottom: 30px; /* Espaço abaixo do título */
            margin-top: 0; /* Remove margem superior padrão */
        }

        /* Container dos inputs */
        .input-group {
            margin-bottom: 15px; /* Espaço abaixo de cada grupo */
            width: 100%; /* Largura total */
        }

        /* Estilos dos inputs */
        input[type="text"],
        input[type="password"] {
            padding: 15px; /* Espaçamento interno */
            border: none; /* Remove borda padrão */
            outline: none; /* Remove contorno ao focar */
            font-size: 15px; /* Tamanho da fonte */
            width: 100%; /* Largura total */
            box-sizing: border-box; /* Inclui padding na largura */
            border-radius: 5px; /* Bordas levemente arredondadas */
        }

        /* Estilo do botão de login */
        .inputSubmit{
            background-color: dodgerblue; /* Cor de fundo azul */
            border: none; /* Remove borda */
            padding: 15px; /* Espaçamento interno */
            width: 100%; /* LARGURA TOTAL IGUAL AOS CAMPOS */
            border-radius: 10px; /* Bordas arredondadas */
            color: #fff; /* Cor do texto */
            font-size: 15px; /* Tamanho da fonte */
            transition: background-color 0.3s; /* Transição suave */
            margin-top: 10px; /* Espaço acima do botão */
            cursor: pointer; /* Cursor de clique */
            box-sizing: border-box; /* Garante largura consistente */
        }

        /* Efeito hover do botão */
        .inputSubmit:hover{
            background-color: deepskyblue; /* Cor alterada para azul mais claro */
        }

        /* ESTILO DO ALERTA FLUTUANTE PARA TODAS AS MENSAGENS */
        .alert-flutuante {
            position: fixed; /* Posição fixa na tela */
            top: 20px; /* Distância do topo */
            left: 50%; /* Centraliza horizontalmente */
            transform: translateX(-50%); /* Ajusta centralização exata */
            padding: 15px 25px; /* Espaçamento interno */
            border-radius: 10px; /* Bordas arredondadas */
            box-shadow: 0 4px 12px rgba(0,0,0,0.3); /* Sombra suave */
            z-index: 1000; /* Garante que fique acima de outros elementos */
            animation: slideDown 0.5s ease-out; /* Animação de entrada */
            display: flex; /* Layout flex para alinhar conteúdo */
            align-items: center; /* Centraliza verticalmente */
            gap: 10px; /* Espaço entre elementos */
            max-width: 400px; /* Largura máxima */
            width: 90%; /* Largura responsiva */
            color: white; /* Texto branco */
        }

        /* VARIANTE DE ALERTA PARA ERROS */
        .alert-flutuante.erro {
            background-color: #72040f; /* Vermelho escuro para erro */
        }

        /* VARIANTE DE ALERTA PARA SUCESSO */
        .alert-flutuante.sucesso {
            background-color: #28a745; /* Verde para sucesso */
        }

        /* BOTÃO DE FECHAR DO ALERTA */
        .alert-flutuante .close-btn {
            background: none; /* Remove fundo padrão do botão */
            border: none; /* Remove borda padrão */
            color: white; /* Cor do ícone branca */
            font-size: 20px; /* Tamanho do ícone "X" */
            cursor: pointer; /* Cursor de ponteiro */
            margin-left: auto; /* Empurra botão para direita (flexbox) */
            padding: 0; /* Remove padding padrão */
            line-height: 1; /* Altura da linha igual ao tamanho da fonte */
        }

        /* EFEITO HOVER NO BOTÃO DE FECHAR */
        .alert-flutuante .close-btn:hover {
            color: #e6e6e6; /* Cor mais clara ao passar mouse */
        }

        /* ANIMAÇÃO DE ENTRADA DO ALERTA */
        @keyframes slideDown {
            from { opacity: 0; transform: translate(-50%, -30px); } /* Começa invisível e 30px acima */
            to { opacity: 1; transform: translate(-50%, 0); } /* Termina visível e na posição */
        }

        /* Estilo do link de cadastro - AJUSTADO PARA UMA LINHA */
        .link-cadastro {
            text-align: center; /* Centraliza o texto */
            margin-top: 20px; /* Espaço acima do link */
            font-size: 14px; /* Tamanho da fonte menor */
            color: #ccc; /* Cor mais suave */
            white-space: nowrap; /* Impede quebra de linha */
        }

        .link-cadastro a {
            color: #4dabf7; /* Cor do link */
            text-decoration: none; /* Remove sublinhado */
            transition: color 0.3s; /* Transição suave */
        }

        .link-cadastro a:hover {
            color: #1c7ed6; /* Cor mais forte no hover */
            text-decoration: underline; /* Adiciona sublinhado no hover */
        }

        /* Media query para tablets */
        @media screen and (max-width: 768px) {
            div.form-container {
                padding: 40px; /* Reduz espaçamento */
                width: 70%; /* Largura maior */
                margin: 20px auto; /* Centraliza */
            }
            
            .alert-flutuante {
                top: 10px; /* Posição mais alta */
                padding: 12px 20px; /* Padding reduzido */
                font-size: 14px; /* Fonte menor */
            }
        }

        /* Media query para celulares */
        @media screen and (max-width: 480px) {
            body {
                padding: 20px; /* Padding no body */
                align-items: flex-start; /* Alinha ao topo em mobile */
                margin-top: 50px; /* Margem superior */
            }
            
            div.form-container {
                padding: 30px; /* Espaçamento menor */
                width: 90%; /* Largura quase total */
                margin: 0 auto; /* Centraliza */
            }
            
            h1 {
                font-size: 24px; /* Reduz tamanho do título */
            }
            
            .inputSubmit {
                padding: 10px; /* Padding reduzido */
            }
            
            .alert-flutuante {
                top: 5px; /* Posição mais alta */
                width: 95%; /* Quase toda largura */
                padding: 10px 15px; /* Padding mínimo */
            }
            
            .link-cadastro {
                white-space: normal; /* Permite quebra em mobile */
            }
        }
    </style>
</head>
<body>
    <!-- Link de retorno INTELIGENTE -->
    <a href="javascript:void(0)" id="backLink"
        style="color: white; text-decoration: none; position: absolute; top: 20px; left: 20px; font-size: 16px; transition: color 0.3s; display: block;"
        onmouseover="this.style.color='#4dabf7'"
        onmouseout="this.style.color='white'" 
        title="Voltar">← Voltar</a>  <!-- Ícone e texto do link -->

    <!-- ALERTA FLUTUANTE PARA ERROS DE LOGIN -->
    <?php if($erro_login): ?>  <!-- Verifica se há erro de login para exibir -->
    <div class="alert-flutuante erro" id="alertLogin">  <!-- Container do alerta de erro -->
        <span>❌</span>  <!-- Ícone de erro (X vermelho) -->
        <span><?php echo $erro_login; ?></span>  <!-- Exibe mensagem de erro -->
        <button class="close-btn" onclick="document.getElementById('alertLogin').style.display='none'">&times;</button>  <!-- Botão para fechar alerta -->
    </div>
    <?php endif; ?>
    
    <!-- Container do formulário -->
    <div class="form-container">
        <!-- DESATIVAR AUTOCOMPLETE NO FORMULÁRIO DE LOGIN -->
        <form action="testLogin.php" method="POST" id="formLogin" autocomplete="off">  <!-- Desativa autocomplete do navegador -->
            <h1>Login</h1> <!-- Título alinhado à esquerda -->
            
            <!-- REMOVIDAS AS MENSAGENS INLINE - AGORA SÓ FLUTUANTES -->
            
            <!-- Campo E-mail - DESATIVAR AUTOCOMPLETE -->
            <div class="input-group">
                <input type="text" name="email" id="loginEmail" placeholder="E-mail" autocomplete="email" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">  <!-- Mantém valor se formulário foi recarregado com erro -->
            </div>
            
            <!-- Campo Senha - DESATIVAR AUTOCOMPLETE -->
            <div class="input-group">
                <input type="password" name="senha" id="loginSenha" placeholder="Senha" autocomplete="current-password">  <!-- Autocomplete específico para senhas -->
            </div>
            
            <!-- Botão de envio -->
            <input class="inputSubmit" type="submit" name="submit" value="Entrar">  <!-- Botão para enviar formulário -->
            
            <!-- Link para cadastro - AGORA EM UMA LINHA -->
            <p class="link-cadastro">
                Não tem uma conta? <a href="formulario.php">Cadastre-se aqui</a>  <!-- Link para página de cadastro -->
            </p>
        </form>
    </div>

    <script>
        // SISTEMA DE ALERTAS E LINK VOLTAR - FUNCIONA PARA TODOS
        document.addEventListener('DOMContentLoaded', function() {  /* Espera DOM carregar completamente */
            const backLink = document.getElementById('backLink');  /* Obtém referência ao link voltar */
            
            // LÓGICA INTELIGENTE PARA O LINK VOLTAR
            // Sempre mostrar o link
            backLink.style.display = 'block';  /* Garante que link está visível */
            
            // Se houver histórico e não estivermos na primeira página
            if (window.history.length > 1 && document.referrer) {  /* Verifica se há páginas no histórico e referenciador */
                // Verificar se a página anterior é diferente da atual
                const currentUrl = window.location.href;  /* URL atual */
                const referrerUrl = document.referrer;  /* URL da página anterior */
                
                // Se for a mesma página (com ou sem parâmetros), não usar history.back()
                if (referrerUrl.includes(window.location.pathname)) {  /* Verifica se veio da mesma página */
                    // Página anterior é a mesma - ir para index.php
                    backLink.href = 'index.php';  /* Define destino como index */
                    backLink.title = 'Voltar para página inicial';  /* Tooltip explicativo */
                } else {
                    // Página anterior é diferente - usar history.back()
                    backLink.href = 'javascript:history.back()';  /* Usa JavaScript para voltar */
                    backLink.title = 'Voltar para página anterior';  /* Tooltip explicativo */
                }
            } else {
                // Sem histórico ou primeira página - ir para index.php
                backLink.href = 'index.php';  /* Define destino padrão */
                backLink.title = 'Voltar para página inicial';  /* Tooltip explicativo */
            }
            
            // Fechar alerta automaticamente após 5 segundos
            const alert = document.getElementById('alertLogin');  /* Obtém referência ao alerta */
            if (alert) {
                setTimeout(function() {  /* Define timeout de 5 segundos */
                    alert.style.opacity = '0';  /* Inicia fade out */
                    alert.style.transform = 'translate(-50%, -30px)';  /* Move para cima */
                    setTimeout(function() {
                        alert.style.display = 'none';  /* Esconde completamente após animação */
                    }, 500);
                }, 5000);
            }
        });

        // Prevenir autocomplete do navegador
        document.getElementById('formLogin').addEventListener('submit', function() {
            // Limpar qualquer cache de autocomplete
            document.getElementById('loginEmail').setAttribute('autocomplete', 'email');  /* Define autocomplete específico */
            document.getElementById('loginSenha').setAttribute('autocomplete', 'current-password');  /* Define autocomplete específico */
        });
    </script>
</body>
</html>