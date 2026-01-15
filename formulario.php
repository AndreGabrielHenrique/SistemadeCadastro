<!-- formulario.php -->
<?php
    session_start(); // Inicia/retoma a sessão PHP

    // Verifica se o usuário já está logado (redireciona para sistema se estiver)
    if(isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
        header('Location: sistema.php'); // Redireciona para painel do sistema
        exit(); // Encerra execução do script
    }

    // RECUPERA MENSAGENS DE ERRO DA SESSÃO (SE HOUVER)
    $erros = isset($_SESSION['erros_validacao']) ? $_SESSION['erros_validacao'] : []; // Obtém array de erros ou array vazio
    unset($_SESSION['erros_validacao']); // Remove erros da sessão após obter (evita re-exibição)

    // PROCESSAMENTO DO FORMULÁRIO QUANDO ENVIADO
    if(isset($_POST['submit'])) { // Verifica se formulário foi submetido
        include_once('config.php'); // Inclui configuração do banco de dados

        // CAPTURA DADOS DO FORMULÁRIO
        $nome = $_POST['nome']; // Nome completo do usuário
        $email = $_POST['email']; // Endereço de e-mail
        $senha = $_POST['senha']; // Senha em texto plano (será hasheada)
        $telefone = $_POST['telefone']; // Número de telefone
        $genero = $_POST['genero']; // Gênero (feminino/masculino/outro)
        $data_nascimento = $_POST['data_nascimento']; // Data de nascimento
        $cidade = $_POST['cidade']; // Cidade de residência
        $estado = $_POST['estado']; // Estado de residência
        $endereco = $_POST['endereco']; // Endereço completo

        // INICIALIZA ARRAY PARA ARMAZENAR ERROS DE VALIDAÇÃO
        $erros = array();
        
        // VALIDAÇÃO DO NOME (MÍNIMO 3 CARACTERES)
        if(strlen($nome) < 3) {
            $erros[] = "Nome deve ter pelo menos 3 caracteres"; // Adiciona erro ao array
        }
        
        // VALIDAÇÃO DO E-MAIL (FORMATO VÁLIDO)
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = "E-mail inválido"; // Adiciona erro se formato inválido
        }
        
        // VALIDAÇÃO DA SENHA (MÍNIMO 6 CARACTERES)
        if(strlen($senha) < 6) {
            $erros[] = "Senha deve ter pelo menos 6 caracteres"; // Adiciona erro se senha muito curta
        }
        
        // VALIDAÇÃO DO TELEFONE (APENAS NÚMEROS, MÍNIMO 10 DÍGITOS)
        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone); // Remove caracteres não numéricos
        if(strlen($telefone_limpo) < 10) {
            $erros[] = "Telefone inválido"; // Adiciona erro se telefone inválido
        }
        
        // VALIDAÇÃO DA DATA DE NASCIMENTO
        $data_atual = date('Y-m-d'); // Data atual no formato YYYY-MM-DD
        $data_minima = date('Y-m-d', strtotime('-120 years')); // Data de 120 anos atrás (limite máximo)
        if($data_nascimento > $data_atual) {
            $erros[] = "Data de nascimento não pode ser futura"; // Data não pode ser no futuro
        }
        if($data_nascimento < $data_minima) {
            $erros[] = "Data de nascimento inválida"; // Data muito antiga (mais de 120 anos)
        }
        
        // VERIFICAÇÃO DE E-MAIL DUPLICADO NO BANCO DE DADOS
        if(empty($erros)) { // Só verifica duplicidade se não houver outros erros
            $stmt_verifica = $conexao->prepare("SELECT id FROM usuarios WHERE email = ?"); // Prepara query
            if($stmt_verifica) {
                $stmt_verifica->bind_param("s", $email); // Vincula parâmetro string (e-mail)
                $stmt_verifica->execute(); // Executa query
                $stmt_verifica->store_result(); // Armazena resultado
                
                if($stmt_verifica->num_rows > 0) { // Se encontrou registros com mesmo e-mail
                    $erros[] = "E-mail já cadastrado. Por favor, use outro e-mail."; // Adiciona erro de duplicidade
                }
                $stmt_verifica->close(); // Fecha statement
            }
        }
        
        // SE HOUVER ERROS, ARMAZENA NA SESSÃO E REDIRECIONA
        if(!empty($erros)) {
            $_SESSION['erros_validacao'] = $erros; // Armazena erros na sessão
            header('Location: formulario.php'); // Redireciona de volta ao formulário
            exit(); // Encerra execução
        }

        // CRIA HASH DA SENHA PARA ARMAZENAMENTO SEGURO
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT); // Gera hash usando algoritmo padrão

        // PREPARA QUERY DE INSERÇÃO NO BANCO DE DADOS
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
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"; // Placeholders para prevenção de SQL injection

        $stmt = mysqli_prepare($conexao, $sql); // Prepara statement
        
        if($stmt) {
            // VINCULA PARÂMETROS AO STATEMENT (9 STRINGS)
            mysqli_stmt_bind_param(
                $stmt, 
                "sssssssss", // 9 parâmetros do tipo string
                $nome, 
                $email, 
                $senha_hash, // Senha já hasheada
                $telefone, 
                $genero, 
                $data_nascimento, 
                $cidade, 
                $estado, 
                $endereco
            );

            $executado = mysqli_stmt_execute($stmt); // Executa inserção
            
            if($executado) {
                // CADASTRO BEM-SUCEDIDO - LOGIN AUTOMÁTICO
                $novo_id = mysqli_insert_id($conexao); // Obtém ID do novo usuário
                
                // DEFINE VARIÁVEIS DE SESSÃO PARA LOGIN AUTOMÁTICO
                $_SESSION['logado'] = true; // Marca usuário como logado
                $_SESSION['id_usuario'] = $novo_id; // Armazena ID do usuário
                $_SESSION['email_usuario'] = $email; // Armazena e-mail
                $_SESSION['nome_usuario'] = $nome; // Armazena nome
                $_SESSION['cadastro_sucesso'] = "Cadastro realizado com sucesso!"; // Mensagem de boas-vindas
                
                header('Location: sistema.php'); // Redireciona para painel do sistema
                exit(); // Encerra execução
            } else {
                // TRATAMENTO DE ERROS DE BANCO DE DADOS
                if(mysqli_errno($conexao) == 1062) { // Código de erro para duplicidade
                    $_SESSION['erros_validacao'] = array("E-mail já cadastrado"); // Erro de e-mail duplicado
                } else {
                    $erro = mysqli_stmt_error($stmt); // Obtém mensagem de erro específica
                    $_SESSION['erros_validacao'] = array("Erro ao cadastrar: $erro"); // Erro genérico
                }
            }

            mysqli_stmt_close($stmt); // Fecha statement
        } else {
            // ERRO NA PREPARAÇÃO DA QUERY
            $_SESSION['erros_validacao'] = array("Erro na preparação: " . mysqli_error($conexao)); // Erro de preparação
        }

        header('Location: formulario.php'); // Redireciona de volta em caso de erro
        exit(); // Encerra execução
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8"> <!-- Define codificação de caracteres como UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configura viewport para responsividade -->
    <title>Formulário</title> <!-- Título da página na aba do navegador -->
    <style>
        /* ESTILOS GERAIS DA PÁGINA */
        body {
            font-family: Arial, Helvetica, sans-serif; /* Define fonte padrão */
            background-image: linear-gradient(to right, rgb(20, 147, 220), rgb(17, 54, 71)); /* Gradiente de fundo azul */
            color: white; /* Cor do texto branca */
        }

        /* CONTAINER PRINCIPAL DO FORMULÁRIO */
        .box {
            background-color: rgba(0, 0, 0, .6); /* Fundo preto semi-transparente */
            position: absolute; /* Posicionamento absoluto na página */
            top: 50%; /* Posiciona no meio verticalmente */
            left: 50%; /* Posiciona no meio horizontalmente */
            transform: translate(-50%, -50%); /* Ajusta centro exato */
            padding: 15px; /* Espaçamento interno */
            border-radius: 15px; /* Bordas arredondadas */
            width: 20%; /* Largura relativa */
            min-width: 400px; /* Largura mínima fixa */
            overflow-y: auto; /* Permite scroll vertical se necessário */
            max-height: 90vh; /* Altura máxima de 90% da viewport */
        }

        /* ESCONDE BARRA DE SCROLL NO WEBKIT (CHROME, SAFARI) */
        .box::-webkit-scrollbar {
            display: none; /* Oculta barra de scroll */
        }

        /* ESTILO DO FIELDSET (GRUPO DE CAMPOS) */
        fieldset{
            border: 3px solid dodgerblue; /* Borda azul */
            padding: 20px; /* Espaçamento interno */
        }

        /* ESTILO DA LEGENDA DO FIELDSET */
        legend {
            border: 1px solid dodgerblue; /* Borda azul fina */
            padding: 10px; /* Espaçamento interno */
            text-align: center; /* Centraliza texto */
            background-color: dodgerblue; /* Fundo azul */
            border-radius: 8px; /* Bordas arredondadas */
        }

        /* CONTAINER DE CADA CAMPO DE INPUT */
        .inputBox {
            position: relative; /* Posição relativa para elementos filhos absolutos */
            margin-bottom: 20px; /* Espaço abaixo de cada campo */
        }

        /* ESTILO DOS CAMPOS DE INPUT */
        .inputUser {
            width: 100%; /* Largura total do container */
            border: none; /* Remove borda padrão */
            border-bottom: 1px solid #fff; /* Borda inferior branca */
            background: none; /* Fundo transparente */
            color: #fff; /* Texto branco */
            font-size: 15px; /* Tamanho da fonte */
            letter-spacing: 2px; /* Espaçamento entre letras */
            outline: none; /* Remove contorno ao focar */
            box-sizing: border-box; /* Inclui padding na largura total */
        }

        /* ESTILO DAS LABELS DOS CAMPOS */
        .labelInput {
            position: absolute; /* Posicionamento absoluto sobre o input */
            top: 0; /* Alinha ao topo do input */
            left: 0; /* Alinha à esquerda do input */
            pointer-events: none; /* Impede interação com label (clique passa para input) */
            transition: .5s; /* Transição suave de 0.5 segundos */
            color: #fff; /* Cor branca inicial */
        }

        /* ANIMAÇÃO DA LABEL QUANDO CAMPO ESTÁ FOCADO OU PREENCHIDO */
        .inputUser:focus ~ .labelInput,
        .inputUser:not(:placeholder-shown) ~ .labelInput {
            top: -20px; /* Move label para cima */
            color: dodgerblue; /* Muda cor para azul */
            font-size: 12px; /* Reduz tamanho da fonte */
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
            background: none; /* Fundo transparente */
            border: none; /* Sem borda */
            color: white; /* Ícone branco */
            font-size: 20px; /* Tamanho do ícone */
            cursor: pointer; /* Cursor de ponteiro */
            margin-left: auto; /* Empurra para direita */
            padding: 0; /* Sem padding */
            line-height: 1; /* Altura da linha */
        }

        /* EFEITO HOVER NO BOTÃO DE FECHAR */
        .alert-flutuante .close-btn:hover {
            color: #e6e6e6; /* Cor mais clara ao passar mouse */
        }

        /* ANIMAÇÃO DE ENTRADA DO ALERTA */
        @keyframes slideDown {
            from { opacity: 0; transform: translate(-50%, -30px); } /* Começa invisível e acima */
            to { opacity: 1; transform: translate(-50%, 0); } /* Termina visível e na posição */
        }

        /* ESTILO ESPECÍFICO DO CAMPO DE DATA */
        #data_nascimento {
            padding: 8px; /* Espaçamento interno */
            border: none; /* Remove borda padrão */
            border-radius: 10px; /* Bordas arredondadas */
            outline: none; /* Remove contorno ao focar */
            font-size: 15px; /* Tamanho da fonte */
            width: 100%; /* Largura total */
            box-sizing: border-box; /* Inclui padding na largura */
            margin-top: 5px; /* Espaço acima do campo */
        }

        /* ESTILO DO BOTÃO DE CADASTRO */
        #submit{
            background-image: linear-gradient(to right, rgb(0, 92, 197), rgb(90, 20, 220)); /* Gradiente azul/roxo */
            border: none; /* Remove borda padrão */
            width: 100%; /* Largura total */
            padding: 15px; /* Espaçamento interno */
            color: #fff; /* Texto branco */
            font-size: 15px; /* Tamanho da fonte */
            cursor: pointer; /* Cursor de ponteiro */
            border-radius: 10px; /* Bordas arredondadas */
            transition: all 0.3s; /* Transição suave para hover */
        }

        /* EFEITO HOVER NO BOTÃO DE CADASTRO */
        #submit:hover{
            background-image: linear-gradient(to right, rgb(0, 80, 172), rgb(80, 19, 195)); /* Gradiente mais escuro */
            transform: scale(1.02); /* Leve aumento no tamanho */
        }
        
        /* MEDIA QUERY PARA TABLETS (ATÉ 992px) */
        @media screen and (max-width: 992px) {
            .box {
                width: 70%; /* Largura maior para tablets */
                position: relative; /* Posicionamento relativo */
                top: auto; /* Remove posicionamento top */
                left: auto; /* Remove posicionamento left */
                transform: none; /* Remove transform */
                margin: 50px auto; /* Centraliza com margens */
                min-width: 300px; /* Largura mínima reduzida */
            }
        }

        /* MEDIA QUERY PARA DISPOSITIVOS MÉDIOS (ATÉ 768px) */
        @media screen and (max-width: 768px) {
            .box {
                width: 85%; /* Largura ainda maior */
                margin: 30px auto; /* Margem reduzida */
            }
            
            .inputUser {
                font-size: 16px !important; /* Fonte maior para mobile */
            }
            
            .alert-flutuante {
                top: 10px; /* Posição mais alta */
                padding: 12px 20px; /* Padding reduzido */
                font-size: 14px; /* Fonte menor */
            }
        }

        /* MEDIA QUERY PARA CELULARES PEQUENOS (ATÉ 480px) */
        @media screen and (max-width: 480px) {
            body {
                padding: 15px; /* Padding no body para não colar nas bordas */
            }
            
            .box {
                width: 100%; /* Ocupa toda largura */
                margin: 20px 0; /* Margem vertical apenas */
                padding: 10px; /* Padding reduzido */
            }
            
            legend {
                font-size: 1.2rem; /* Fonte menor na legenda */
                padding: 8px; /* Padding reduzido */
            }
            
            .alert-flutuante {
                top: 5px; /* Posição mais alta ainda */
                width: 95%; /* Quase toda largura */
                padding: 10px 15px; /* Padding mínimo */
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
    
    <!-- ALERTA FLUTUANTE PARA ERROS DE CADASTRO -->
    <?php if(!empty($erros)): ?>  <!-- Verifica se há erros para exibir -->
    <div class="alert-flutuante erro" id="alertForm">  <!-- Container do alerta de erro -->
        <span>❌</span>  <!-- Ícone de erro (X vermelho) -->
        <span><?php echo implode('<br>', $erros); ?></span>  <!-- Exibe erros separados por linha -->
        <button class="close-btn" onclick="document.getElementById('alertForm').style.display='none'">&times;</button>  <!-- Botão para fechar alerta -->
    </div>
    <?php endif; ?>

    <div class="box">
        <!-- DESATIVAR AUTOCOMPLETE NO FORMULÁRIO -->
        <form action="formulario.php" method="POST" id="formCadastro" autocomplete="off">  <!-- Desativa autocomplete do navegador -->
            <fieldset>  <!-- Grupo de campos do formulário -->
                <legend><b>Formulário de Clientes</b></legend>  <!-- Título do formulário -->
                
                <!-- REMOVIDAS AS MENSAGENS INLINE - AGORA SÓ FLUTUANTES -->
                
                <!-- Campo Nome Completo -->
                <div class="inputBox">
                    <input type="text" name="nome" id="nome" class="inputUser" required placeholder=" "
                           value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">  <!-- Mantém valor se formulário foi recarregado com erro -->
                    <label for="nome" class="labelInput">Nome completo</label>  <!-- Label flutuante -->
                </div>
                <br><br>
                
                <!-- Campo Senha - DESATIVAR AUTOCOMPLETE -->
                <div class="inputBox">
                    <input type="password" name="senha" id="senha" class="inputUser" required placeholder=" " autocomplete="new-password">  <!-- Campo obrigatório, autocomplete específico para novas senhas -->
                    <label for="senha" class="labelInput">Senha (mínimo 6 caracteres)</label>  <!-- Label explicativa -->
                </div>
                <br><br>
                
                <!-- Campo E-mail - DESATIVAR AUTOCOMPLETE -->
                <div class="inputBox">
                    <input type="email" name="email" id="email" class="inputUser" required placeholder=" " autocomplete="off"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">  <!-- Mantém valor se formulário foi recarregado com erro -->
                    <label for="email" class="labelInput">E-mail</label>  <!-- Label flutuante -->
                </div>
                <br><br>
                
                <!-- Campo Telefone -->
                <div class="inputBox">
                    <input type="tel" name="telefone" id="telefone" class="inputUser" required placeholder=" "
                           value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>">  <!-- Mantém valor se formulário foi recarregado com erro -->
                    <label for="telefone" class="labelInput">Telefone (com DDD)</label>  <!-- Label flutuante -->
                </div>
                
                <!-- Seção de Gênero -->
                <p>Sexo:</p>  <!-- Título da seção -->
                <input type="radio" name="genero" value="feminino" id="feminino" required
                       <?php echo (isset($_POST['genero']) && $_POST['genero'] == 'feminino') ? 'checked' : ''; ?>>  <!-- Verifica se estava selecionado antes -->
                <label for="feminino">Feminino</label>  <!-- Label do radio feminino -->
                <br>
                <input type="radio" name="genero" value="masculino" id="masculino" required
                       <?php echo (isset($_POST['genero']) && $_POST['genero'] == 'masculino') ? 'checked' : ''; ?>>  <!-- Verifica se estava selecionado antes -->
                <label for="masculino">Masculino</label>  <!-- Label do radio masculino -->
                <br>
                <input type="radio" name="genero" value="outro" id="outro" required
                       <?php echo (isset($_POST['genero']) && $_POST['genero'] == 'outro') ? 'checked' : ''; ?>>  <!-- Verifica se estava selecionado antes -->
                <label for="outro">Outro</label>  <!-- Label do radio outro -->
                <br><br>
                
                <!-- Data de Nascimento - EXATAMENTE IGUAL AO EDITAR.PHP -->
                <label for="data_nascimento"><b>Data de Nascimento:</b></label>  <!-- Label estática (não flutuante) -->
                <input type="date" name="data_nascimento" id="data_nascimento" required
                       max="<?php echo date('Y-m-d'); ?>"
                       value="<?php echo isset($_POST['data_nascimento']) ? $_POST['data_nascimento'] : ''; ?>">  <!-- Mantém valor se formulário foi recarregado com erro -->
                <br><br><br>
                
                <!-- Campo Cidade -->
                <div class="inputBox">
                    <input type="text" name="cidade" id="cidade" class="inputUser" required placeholder=" "
                           value="<?php echo isset($_POST['cidade']) ? htmlspecialchars($_POST['cidade']) : ''; ?>">  <!-- Mantém valor se formulário foi recarregado com erro -->
                    <label for="cidade" class="labelInput">Cidade</label>  <!-- Label flutuante -->
                </div>
                <br><br>
                
                <!-- Campo Estado -->
                <div class="inputBox">
                    <input type="text" name="estado" id="estado" class="inputUser" required placeholder=" "
                           value="<?php echo isset($_POST['estado']) ? htmlspecialchars($_POST['estado']) : ''; ?>">  <!-- Mantém valor se formulário foi recarregado com erro -->
                    <label for="estado" class="labelInput">Estado</label>  <!-- Label flutuante -->
                </div>
                <br><br>
                
                <!-- Campo Endereço -->
                <div class="inputBox">
                    <input type="text" name="endereco" id="endereco" class="inputUser" required placeholder=" "
                           value="<?php echo isset($_POST['endereco']) ? htmlspecialchars($_POST['endereco']) : ''; ?>">  <!-- Mantém valor se formulário foi recarregado com erro -->
                    <label for="endereco" class="labelInput">Endereço</label>  <!-- Label flutuante -->
                </div>
                <br><br>
                
                <!-- Botão de Submissão -->
                <input type="submit" name="submit" id="submit" value="Cadastrar">  <!-- Botão para enviar formulário -->
            </fieldset>
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
                    // Página anterior é a mesma - ir para destino padrão
                    backLink.href = 'login.php';  /* Define destino como login */
                    backLink.title = 'Voltar para login';  /* Tooltip explicativo */
                } else {
                    // Página anterior é diferente - usar history.back()
                    backLink.href = 'javascript:history.back()';  /* Usa JavaScript para voltar */
                    backLink.title = 'Voltar para página anterior';  /* Tooltip explicativo */
                }
            } else {
                // Sem histórico ou primeira página - ir para login
                backLink.href = 'login.php';  /* Define destino padrão */
                backLink.title = 'Voltar para login';  /* Tooltip explicativo */
            }
            
            // Configurar labels flutuantes
            const inputs = document.querySelectorAll('.inputUser');  /* Seleciona todos os inputs */
            inputs.forEach(input => {
                if (input.value && input.value.trim() !== '') {
                    if (!input.hasAttribute('placeholder') || input.getAttribute('placeholder') === '') {
                        input.setAttribute('placeholder', ' ');  /* Adiciona placeholder vazio se necessário */
                    }
                }
            });
            
            // Fechar alerta automaticamente após 5 segundos
            const alert = document.getElementById('alertForm');  /* Obtém referência ao alerta */
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

        // Validação em tempo real do formulário
        document.getElementById('formCadastro').addEventListener('submit', function(event) {  /* Adiciona listener de submit */
            let valid = true;  /* Flag de validação */
            let mensagens = [];  /* Array para armazenar mensagens de erro */
            
            const nome = document.getElementById('nome').value;  /* Obtém valor do nome */
            if(nome.length < 3) {
                valid = false;  /* Invalida formulário */
                mensagens.push('Nome deve ter pelo menos 3 caracteres');  /* Adiciona mensagem de erro */
            }
            
            const senha = document.getElementById('senha').value;  /* Obtém valor da senha */
            if(senha.length < 6) {
                valid = false;  /* Invalida formulário */
                mensagens.push('Senha deve ter pelo menos 6 caracteres');  /* Adiciona mensagem de erro */
            }
            
            const telefone = document.getElementById('telefone').value;  /* Obtém valor do telefone */
            const telefoneNumeros = telefone.replace(/\D/g, '');  /* Remove não-números */
            if(telefoneNumeros.length < 10) {
                valid = false;  /* Invalida formulário */
                mensagens.push('Telefone inválido (mínimo 10 dígitos)');  /* Adiciona mensagem de erro */
            }
            
            const dataNascimento = document.getElementById('data_nascimento').value;  /* Obtém valor da data */
            const hoje = new Date().toISOString().split('T')[0];  /* Obtém data atual no formato YYYY-MM-DD */
            if(dataNascimento > hoje) {
                valid = false;  /* Invalida formulário */
                mensagens.push('Data de nascimento não pode ser futura');  /* Adiciona mensagem de erro */
            }
            
            if(!valid) {
                event.preventDefault();  /* Impede envio do formulário */
                alert('Erros encontrados:\n' + mensagens.join('\n'));  /* Exibe alerta com todos os erros */
            }
        });
    </script>
</body>
</html>