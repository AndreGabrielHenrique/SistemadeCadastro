<!-- sistema.php: Página principal do sistema após login bem-sucedido -->
<?php
    session_start(); // Inicia o uso de sessões
    include_once('config.php'); // Inclui arquivo de configuração do banco
    
    // Verifica se o usuário NÃO está logado (proteção de acesso não autorizado)
    if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        session_destroy(); // Destrói todos os dados da sessão para segurança
        header('Location: index.php'); // Redireciona para a página inicial
        exit(); // Encerra a execução do script imediatamente
    }

    // Armazena informações do usuário logado para uso na página
    $logado = $_SESSION['email_usuario']; // E-mail do usuário logado (para exibição)
    $nome_usuario = $_SESSION['nome_usuario']; // Nome do usuário logado (para exibição)
    $id_logado = $_SESSION['id_usuario']; // ID do usuário logado (para exclusão da listagem)

    // ============================================================================
    // VERIFICAÇÃO CRÍTICA: SE O USUÁRIO AINDA EXISTE NO BANCO DE DADOS
    // Esta verificação previne que um usuário com sessão ativa, mas que foi excluído
    // por outro usuário, continue acessando o sistema.
    // ============================================================================
    $stmt_verifica = $conexao->prepare("SELECT id FROM usuarios WHERE id = ?");
    $stmt_verifica->bind_param("i", $id_logado);
    $stmt_verifica->execute();
    $stmt_verifica->store_result();
    
    // Se o usuário não existe mais no banco (foi excluído por outro usuário)
    if($stmt_verifica->num_rows === 0) {
        // Destrói a sessão completamente
        session_destroy();
        
        // Redireciona para a página inicial com mensagem de sessão expirada
        $_SESSION['erro_login'] = "Sua sessão expirou porque seu usuário foi excluído.";
        header('Location: index.php');
        exit();
    }
    
    $stmt_verifica->close();
    // ============================================================================

    // Verifica se há parâmetro de busca na URL para filtragem
    $busca = isset($_GET['busca']) ? $_GET['busca'] : '';

    // Lógica de busca com tratamento de erros e segurança
    if(!empty($busca)) {
        // Adiciona wildcards (%) ao termo de busca para pesquisa parcial
        $termo = "%" . $busca . "%";
        
        // Query COM busca usando prepared statement - EXCLUINDO O USUÁRIO LOGADO
        // IMPORTANTE: A cláusula WHERE id != ? impede que o usuário veja seu próprio registro
        $stmt = $conexao->prepare("
            SELECT * FROM usuarios 
            WHERE 
                id != ? AND (  -- EXCLUI O USUÁRIO ATUALMENTE LOGADO DA LISTAGEM
                id LIKE ? OR 
                nome LIKE ? OR 
                email LIKE ? OR 
                telefone LIKE ? OR 
                genero LIKE ? OR
                data_nascimento LIKE ? OR
                cidade LIKE ? OR
                estado LIKE ? OR
                endereco LIKE ?)
            ORDER BY id DESC
        ");
        
        // Vincula os parâmetros corretamente - AGORA COM 10 PARÂMETROS (1 inteiro + 9 strings)
        $stmt->bind_param(
            "isssssssss",  // 'i' para o ID (inteiro) + 9 's' para as strings de busca
            $id_logado,    // ID do usuário logado (para exclusão da listagem)
            $termo, $termo, $termo, 
            $termo, $termo, $termo, 
            $termo, $termo, $termo
        );
    } else {
        // Query SEM busca - EXCLUINDO O USUÁRIO LOGADO
        $stmt = $conexao->prepare("SELECT * FROM usuarios WHERE id != ? ORDER BY id DESC");
        $stmt->bind_param("i", $id_logado); // Exclui o usuário logado da listagem
    }
    
    // Executa a query preparada e verifica erros de execução
    if(!$stmt->execute()) {
        die("Erro na busca: " . $stmt->error); // Exibe erro detalhado em caso de falha
    }
    
    $resultado = $stmt->get_result(); // Obtém resultados da query
    $stmt->close(); // Fecha a statement para liberar recursos

    header('Content-Type: text/html; charset=utf-8'); // Define charset para suporte a acentos
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"> <!-- Define codificação de caracteres para suportar acentuação -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configura viewport para dispositivos móveis -->
    
    <!-- Importa CSS do Bootstrap para estilização responsiva -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    
    <!-- Importa JavaScript do Bootstrap para funcionalidades interativas -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    
    <title>Painel do Sistema</title> <!-- Título da página exibido na aba do navegador -->
    
    <style>
        /* Estilização geral do corpo da página */
        body {
            background: linear-gradient(to right, rgb(20, 147, 220), rgb(17, 54, 71)) no-repeat fixed;
            color: white;
            overflow-x: hidden; /* Desabilita scroll horizontal para melhor UX */
            display: flex; /* Flexbox para layout responsivo */
            flex-direction: column; /* Organiza elementos em coluna */
            min-height: 100vh; /* Garante que o corpo ocupe toda a altura da tela */
        }
        
        /* Estilização da barra de navegação */
        .navbar-brand{
            position: relative; /* Posição relativa para ajustes finos */
            top: 3px; /* Ajusta a posição vertical do título */
        }

        /* Container da tabela com scroll para muitos registros */
        .table-container {
            height: auto; /* Altura automática conforme conteúdo */
            overflow: auto; /* Habilita scroll vertical e horizontal quando necessário */
            max-height: 75vh; /* Altura máxima para manter a responsividade */
            margin: 20px; /* Margem externa para espaçamento */

            /* Esconde a barra de scroll para melhor estética */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE/Edge */
        }

        /* Esconde scrollbar para WebKit (Chrome, Safari, Opera) */
        body::-webkit-scrollbar, .table-container::-webkit-scrollbar {
            display: none;
        }

        /* Estilização do fundo da tabela */
        .table-bg {
            background-color: rgba(0, 0, 0, .3); /* Cor de fundo semi-transparente */
            border-radius: 15px 15px 15px 15px; /* TODOS OS CANTOS ARREDONDADOS (superior e inferior) */
            min-width: 1200px; /* Largura mínima para evitar quebras em telas pequenas */
            overflow: hidden; /* Garante que o conteúdo respeite o border-radius */
        }
        
        /* Estilização da barra de pesquisa */
        .box-search {
            display: flex; /* Layout flexível para alinhar campo e botão */
            justify-content: center; /* Centraliza horizontalmente */
            gap: 2px; /* Espaço entre o campo e o botão */
            margin: 40px auto 0; /* Margem superior aumentada para separação visual */
            width: 70%; /* Largura relativa ao container pai */
            max-width: 800px; /* Largura máxima para não ficar muito largo */
            align-items: center; /* Centraliza verticalmente os elementos */
            height: 50px; /* Altura fixa para consistência */
        }

        /* Campo de entrada da pesquisa */
        .box-search .form-control {
            width: 75% !important; /* Largura do campo (75% do container) */
            min-width: 250px; /* Largura mínima para usabilidade */
            transition: all 0.3s ease; /* Transição suave para efeitos hover/focus */
            height: 100%; /* Ocupa 100% da altura do container pai */
            padding: 10px 15px; /* Padding consistente com o botão */
        }

        /* Botão da pesquisa */
        .box-search .btn {
            padding: 0 25px; /* Padding horizontal para tamanho adequado */
            font-size: 15px; /* Tamanho da fonte igual ao input */
            display: flex;
            align-items: center; /* Centraliza conteúdo verticalmente */
            height: 100%; /* Ocupa altura total do container pai */
            justify-content: center; /* Centraliza conteúdo horizontalmente */
        }
        
        /* Estilização geral da tabela */
        table {
            margin: 0 auto; /* Centraliza tabela horizontalmente */
            width: 100%; /* Largura total do container */
            text-align: center; /* Alinhamento centralizado do texto */
            border-collapse: collapse; /* Mude para collapse para melhor controle de bordas */
        }

        /* Células da tabela (cabeçalho e dados) */
        th, td {
            padding: 8px; /* Espaçamento interno das células para melhor leitura */
            border: 1px solid rgba(255, 255, 255, 0.5); /* Borda branca para todas as células */
            border-left: none; /* Remove borda esquerda */
            border-right: none; /* Remove borda direita */
        }

        /* Estilização do fundo da tabela */
        .table-bg {
            background-color: rgba(0, 0, 0, .3); /* Cor de fundo semi-transparente */
            border-radius: 15px; /* Todos os cantos arredondados */
            min-width: 1200px; /* Largura mínima para evitar quebras em telas pequenas */
            overflow: hidden; /* Garante que o conteúdo respeite o border-radius */
        }

        /* Remove borda superior do cabeçalho */
        .table-bg thead tr:first-child th {
            border-top: none;
        }

        /* Remove borda inferior da última linha */
        .table-bg tbody tr:last-child td {
            border-bottom: none;
        }

        /* Remove bordas laterais das células das extremidades */
        .table-bg th:first-child,
        .table-bg td:first-child {
            border-left: none;
        }

        .table-bg th:last-child,
        .table-bg td:last-child {
            border-right: none;
        }

        /* ADICIONA BORDER-RADIUS NAS CÉLULAS DO CABEÇALHO */
        .table-bg thead tr:first-child th:first-child {
            border-top-left-radius: 15px; /* Arredonda canto superior esquerdo do cabeçalho */
        }

        .table-bg thead tr:first-child th:last-child {
            border-top-right-radius: 15px; /* Arredonda canto superior direito do cabeçalho */
        }

        /* ADICIONA BORDER-RADIUS NAS CÉLULAS DA ÚLTIMA LINHA */
        .table-bg tbody tr:last-child td:first-child {
            border-bottom-left-radius: 15px; /* Arredonda canto inferior esquerdo da última linha */
        }

        .table-bg tbody tr:last-child td:last-child {
            border-bottom-right-radius: 15px; /* Arredonda canto inferior direito da última linha */
        }

        /* Última célula (coluna de ações com botões editar/excluir) */
        td:last-child {
            white-space: nowrap; /* Impede quebra de linha nos botões */
            min-width: 120px; /* Largura mínima para acomodar os botões */
            gap: 5px; /* Espaço entre botões */
        }

        /* Ajuste de botões para tamanho reduzido */
        .btn {
            padding: 8px 12px !important; /* Tamanho reduzido para tabelas */
            margin: 2px !important; /* Pequeno espaçamento entre botões */
        }

        /* ESTILO DO ALERTA FLUTUANTE PARA TODAS AS MENSAGENS */
        .alert-flutuante {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 1000;
            animation: slideDown 0.5s ease-out;
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 400px;
            width: 90%;
            color: white;
        }

        .alert-flutuante.erro {
            background-color: #72040f;
        }

        .alert-flutuante.sucesso {
            background-color: #28a745;
        }

        .alert-flutuante.aviso {
            background-color: #ffc107; /* Amarelo para alertas de aviso */
            color: #000; /* Texto preto para melhor contraste no amarelo */
        }

        .alert-flutuante .close-btn {
            background: none;
            border: none;
            color: inherit; /* Herda a cor do texto do alerta */
            font-size: 20px;
            cursor: pointer;
            margin-left: auto;
            padding: 0;
            line-height: 1;
        }

        .alert-flutuante .close-btn:hover {
            opacity: 0.8; /* Efeito hover mais sutil */
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translate(-50%, -30px); }
            to { opacity: 1; transform: translate(-50%, 0); }
        }

        /* Estilo para mensagem de boas-vindas personalizada */
        .welcome-message {
            text-align: center;
            margin-top: 30px;
            font-size: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        /* Media query para tablets (768px ou menos) */
        @media screen and (max-width: 768px) {
            .table-container {
                margin: 10px; /* Reduz margem em telas menores */
                height: calc(100vh - 160px); /* Ajuste de altura para dispositivos móveis */
                max-height: 65vh; /* Altura máxima menor para dispositivos móveis */
            }
            
            .box-search {
                max-width: 500px; /* Largura máxima reduzida para tablets */
                width: 95%; /* Ocupa quase toda a largura disponível */
            }
            
            .box-search .form-control {
                width: 100% !important; /* Campo ocupa toda a largura em tablets */
            }
            
            .box-search .btn {
                padding: 0 20px; /* Reduz espaçamento interno em tablets */
            }
            
            .welcome-message {
                font-size: 1.2rem;
                margin-top: 20px;
            }
        }

        /* Media query para celulares pequenos (576px ou menos) */
        @media screen and (max-width: 576px) {
            .table-container {
                height: calc(100vh - 140px); /* Altura ajustada para celulares */
            }
            
            .box-search {
                width: 80%; /* Largura maior em celulares */
                padding: 0 15px; /* Padding horizontal para não colar nas bordas */
            }
            
            /* Ícones dentro dos botões (reduz tamanho em celulares) */
            .btn svg {
                width: 14px; /* Largura reduzida para ícones */
                height: 14px; /* Altura reduzida para ícones */
            }
            
            /* Título da navbar (reduz tamanho em celulares) */
            .navbar-brand {
                font-size: 1.25rem; /* Tamanho da fonte reduzido para celulares */
            }
            
            .welcome-message {
                font-size: 1rem;
                margin-top: 15px;
                padding: 0 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Barra de navegação superior -->
    <nav class="navbar bg-dark border-bottom border-body" data-bs-theme="dark">
        <div class="container-fluid">
            <!-- Título do sistema no canto superior esquerdo -->
            <h1 class="navbar-brand">Painel do Sistema</h1>
            <!-- Botão de logout no canto superior direito -->
            <a href="logout.php" class="btn btn-danger me-5">Sair</a>
        </div>
    </nav>

    <!-- ÁREA DE MENSAGENS DE ALERTA FLUTUANTE - TODAS AS MENSAGENS DO SISTEMA -->
    <?php
        // Exibir alertas flutuantes para todas as operações
        
        // 1. ALERTA DE LOGIN COM SUCESSO
        if(isset($_SESSION['login_sucesso'])) {
            echo '<div class="alert-flutuante sucesso" id="alertLoginSucesso">
                    <span>✅</span>
                    <span>' . $_SESSION['login_sucesso'] . '</span>
                    <button class="close-btn" onclick="document.getElementById(\'alertLoginSucesso\').style.display=\'none\'">&times;</button>
                </div>';
            unset($_SESSION['login_sucesso']);
        }
        
        // 2. ALERTA DE CADASTRO COM SUCESSO
        if(isset($_SESSION['cadastro_sucesso'])) {
            echo '<div class="alert-flutuante sucesso" id="alertCadastroSucesso">
                    <span>🎉</span>
                    <span>' . $_SESSION['cadastro_sucesso'] . '</span>
                    <button class="close-btn" onclick="document.getElementById(\'alertCadastroSucesso\').style.display=\'none\'">&times;</button>
                </div>';
            unset($_SESSION['cadastro_sucesso']);
        }
        
        // 3. ALERTA DE EXCLUSÃO COM SUCESSO
        if(isset($_SESSION['sucesso_exclusao'])) {
            echo '<div class="alert-flutuante sucesso" id="alertExclusaoSucesso">
                    <span>✅</span>
                    <span>' . $_SESSION['sucesso_exclusao'] . '</span>
                    <button class="close-btn" onclick="document.getElementById(\'alertExclusaoSucesso\').style.display=\'none\'">&times;</button>
                </div>';
            unset($_SESSION['sucesso_exclusao']);
        }
        
        // 4. ALERTA DE ERRO NA EXCLUSÃO
        if(isset($_SESSION['erro_exclusao'])) {
            echo '<div class="alert-flutuante erro" id="alertExclusaoErro">
                    <span>❌</span>
                    <span>' . $_SESSION['erro_exclusao'] . '</span>
                    <button class="close-btn" onclick="document.getElementById(\'alertExclusaoErro\').style.display=\'none\'">&times;</button>
                </div>';
            unset($_SESSION['erro_exclusao']);
        }
        
        // 5. ALERTA DE EDIÇÃO COM SUCESSO
        if(isset($_SESSION['sucesso_edicao'])) {
            echo '<div class="alert-flutuante sucesso" id="alertEdicaoSucesso">
                    <span>✏️</span>
                    <span>' . $_SESSION['sucesso_edicao'] . '</span>
                    <button class="close-btn" onclick="document.getElementById(\'alertEdicaoSucesso\').style.display=\'none\'">&times;</button>
                </div>';
            unset($_SESSION['sucesso_edicao']);
        }
        
        // 6. ALERTA DE ERRO NA EDIÇÃO
        if(isset($_SESSION['erro_edicao'])) {
            echo '<div class="alert-flutuante erro" id="alertEdicaoErro">
                    <span>❌</span>
                    <span>' . $_SESSION['erro_edicao'] . '</span>
                    <button class="close-btn" onclick="document.getElementById(\'alertEdicaoErro\').style.display=\'none\'">&times;</button>
                </div>';
            unset($_SESSION['erro_edicao']);
        }
        
        // 7. ALERTA DE LOGOUT COM SUCESSO (caso venha do logout.php)
        if(isset($_SESSION['logout_sucesso'])) {
            echo '<div class="alert-flutuante sucesso" id="alertLogoutSucesso">
                    <span>👋</span>
                    <span>' . $_SESSION['logout_sucesso'] . '</span>
                    <button class="close-btn" onclick="document.getElementById(\'alertLogoutSucesso\').style.display=\'none\'">&times;</button>
                </div>';
            unset($_SESSION['logout_sucesso']);
        }
    ?>

    <!-- ALERTA PARA PESQUISA EM BRANCO (OCULTO INICIALMENTE) -->
    <div class="alert-flutuante aviso" id="alertPesquisaVazia" style="display: none;">
        <span>⚠️</span>
        <span>Por favor, digite algo para pesquisar.</span>
        <button class="close-btn" onclick="document.getElementById('alertPesquisaVazia').style.display='none'">&times;</button>
    </div>

    <!-- MENSAGEM DE BOAS-VINDAS PERSONALIZADA -->
    <div class="welcome-message">
        <h1>Bem-vindo(a), <u><?php echo htmlspecialchars($nome_usuario); ?></u>!</h1>
    </div>

    <!-- Container da barra de pesquisa -->
    <div class="box-search">
        <!-- Campo de entrada de texto para pesquisa -->
        <input type="search" class="form-control w-25" placeholder="Digite para pesquisar (nome, e-mail, telefone...)" id="pesquisar" value="<?php echo htmlspecialchars($busca); ?>">
        <!-- Botão de pesquisa com ícone de lupa -->
        <button class="btn btn-primary" onclick="dadosBusca()">
            <!-- Ícone de lupa do Bootstrap Icons -->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
            </svg>
        </button>
        <!-- Botão para limpar pesquisa -->
        <?php if(!empty($busca)): ?>
        <button class="btn btn-secondary" onclick="limparBusca()" title="Limpar pesquisa">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
            </svg>
        </button>
        <?php endif; ?>
    </div>

    <!-- Container principal da tabela -->
    <div class="table-container m-5">
        <!-- Tabela de dados dos usuários -->
        <table class="table-bg">
            <thead>
                <tr>
                    <!-- Cabeçalhos das colunas -->
                    <th scope="col">Nome</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Telefone</th>
                    <th scope="col">Sexo</th>
                    <th scope="col">Data de Nascimento</th>
                    <th scope="col">Cidade</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Endereço</th>
                    <th scope="col">Ações</th> <!-- Coluna de ações (editar/excluir) -->
                </tr>
            </thead>
            <tbody>
                <?php
                    // Verifica se há resultados para exibir
                    if($resultado->num_rows > 0) {
                        // Loop através de cada registro retornado do banco
                        // IMPORTANTE: Esta listagem NÃO inclui o usuário logado (devido à query WHERE id != ?)
                        while($dados_usuario = mysqli_fetch_assoc($resultado)) {
                            echo "<tr>"; // Inicia nova linha na tabela para cada usuário
                            
                            // Exibe cada campo do usuário em células da tabela
                            echo "<td>" . htmlspecialchars($dados_usuario['nome']) . "</td>"; // Nome completo (com escape)
                            echo "<td>" . htmlspecialchars($dados_usuario['email']) . "</td>"; // Endereço de e-mail (com escape)
                            echo "<td>" . htmlspecialchars($dados_usuario['telefone']) . "</td>"; // Número de telefone (com escape)
                            echo "<td>" . htmlspecialchars($dados_usuario['genero']) . "</td>"; // Gênero (feminino/masculino/outro)
                            echo "<td>" . $dados_usuario['data_nascimento'] . "</td>"; // Data de nascimento
                            echo "<td>" . htmlspecialchars($dados_usuario['cidade']) . "</td>"; // Cidade de residência (com escape)
                            echo "<td>" . htmlspecialchars($dados_usuario['estado']) . "</td>"; // Estado de residência (com escape)
                            echo "<td>" . htmlspecialchars($dados_usuario['endereco']) . "</td>"; // Endereço completo (com escape)
                            
                            // Célula com botões de ação (editar e excluir)
                            echo "<td>";
                            // Botão de editar (lápis) - redireciona para editar.php com o ID do usuário
                            echo "<a class='btn btn-sm btn-primary' href='editar.php?id=" . $dados_usuario['id'] . "' title='Editar usuário'>";
                            echo "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>";
                            echo "<path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325'/>";
                            echo "</svg> Editar";
                            echo "</a>";

                            // Botão de excluir (lixeira) - redireciona para excluir.php com o ID do usuário
                            echo "<a class='btn btn-sm btn-danger' href='excluir.php?id=" . $dados_usuario['id'] . "' title='Excluir usuário' onclick='return confirm(\"Tem certeza que deseja excluir o usuário " . addslashes($dados_usuario['nome']) . "?\")'>";
                            echo "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash-fill' viewBox='0 0 16 16'>";
                            echo "<path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0'/>";
                            echo "</svg> Excluir";
                            echo "</a>"; 
                            echo "</td>";
                            
                            echo "</tr>"; // Fecha a linha da tabela
                        }
                    } else {
                        // Mensagem exibida quando não há usuários cadastrados (exceto o logado)
                        echo "<tr><td colspan='10' class='text-center py-4'>";
                        if(!empty($busca)) {
                            echo "Nenhum resultado encontrado para '<strong>" . htmlspecialchars($busca) . "</strong>'";
                        } else {
                            echo "Nenhum usuário cadastrado no sistema.";
                        }
                        echo "</td></tr>";
                    }
                ?>            
            </tbody>
        </table>
    </div>

    <!-- Contador de registros -->
    <div class="text-center mt-3 mb-5">
        <p class="text-white-50">
            <?php 
                $total_usuarios = $resultado->num_rows;
                echo "Total de usuários listados: <strong>" . $total_usuarios . "</strong>";
                if(!empty($busca)) {
                    echo " (filtrados por: <em>" . htmlspecialchars($busca) . "</em>)";
                }
            ?>
        </p>
    </div>

<script>
    // FUNÇÃO: Captura elemento do campo de pesquisa
    var busca = document.getElementById('pesquisar');

    // EVENTO: Adiciona listener para tecla Enter no campo de pesquisa
    busca.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            dadosBusca(); // Chama função de busca quando Enter é pressionado
        }
    });

    // FUNÇÃO: Redireciona para a página com parâmetro de busca
    function dadosBusca() {
        var termo = busca.value.trim();
        if(termo !== '') {
            window.location = 'sistema.php?busca=' + encodeURIComponent(termo); // Atualiza URL com parâmetro de busca codificado
        } else {
            // EXIBE ALERTA SE CAMPO DE PESQUISA ESTIVER EM BRANCO
            mostrarAlertaPesquisaVazia();
        }
    }

    // FUNÇÃO: Exibe alerta quando campo de pesquisa está vazio
    function mostrarAlertaPesquisaVazia() {
        var alerta = document.getElementById('alertPesquisaVazia');
        
        // Mostra o alerta
        alerta.style.display = 'flex';
        alerta.style.opacity = '1';
        alerta.style.transform = 'translate(-50%, 0)';
        
        // Foca no campo de pesquisa
        busca.focus();
        
        // Fecha o alerta automaticamente após 5 segundos
        setTimeout(function() {
            alerta.style.opacity = '0';
            alerta.style.transform = 'translate(-50%, -30px)';
            setTimeout(function() {
                alerta.style.display = 'none';
            }, 500);
        }, 5000);
    }

    // FUNÇÃO: Limpa a pesquisa e recarrega a página
    function limparBusca() {
        window.location = 'sistema.php'; // Recarrega sem parâmetros de busca
    }

    // EVENTO: Ajusta tabela conforme tamanho da tela (responsividade)
    window.addEventListener('resize', function() {
        if (window.innerWidth < 992) {
            document.querySelector('.table-bg').classList.add('scrollable-table'); // Adiciona classe para scroll em telas menores
        } else {
            document.querySelector('.table-bg').classList.remove('scrollable-table'); // Remove classe de scroll em telas maiores
        }
    });

    // Fechar alertas flutuantes automaticamente após 5 segundos
    document.addEventListener('DOMContentLoaded', function() {
        // Fecha todos os alertas flutuantes após 5 segundos
        const alerts = document.querySelectorAll('.alert-flutuante:not(#alertPesquisaVazia)');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                alert.style.transform = 'translate(-50%, -30px)';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            }, 5000); // 5 segundos
        });
        
        // Foca no campo de pesquisa se houver busca anterior
        if(busca.value) {
            busca.focus();
            busca.select();
        }
    });

    // Confirmação antes de excluir (redundante com o onclick, mas extra segurança)
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('a.btn-danger[href*="excluir.php"]');
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                if(!confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
                    e.preventDefault();
                }
            });
        });
    });

    // Fecha alerta de pesquisa vazia quando clicar no botão X
    document.getElementById('alertPesquisaVazia').querySelector('.close-btn').addEventListener('click', function() {
        var alerta = document.getElementById('alertPesquisaVazia');
        alerta.style.opacity = '0';
        alerta.style.transform = 'translate(-50%, -30px)';
        setTimeout(function() {
            alerta.style.display = 'none';
        }, 500);
    });
</script>
</body>
</html>