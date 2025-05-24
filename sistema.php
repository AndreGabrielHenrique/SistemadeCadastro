<!-- sistema.php -->
<?php
    session_start(); // Inicia o uso de sessões
    include_once('config.php'); // Inclui arquivo de configuração do banco
    
    // Verifica se o usuário NÃO está logado
    if(!isset($_SESSION['email']) && !isset($_SESSION['senha'])) {
        unset($_SESSION['email']); // Remove variável de e-mail da sessão
        unset($_SESSION['senha']); // Remove variável de senha da sessão
        header('Location: login.php'); // Redireciona para login
    }

    $logado = $_SESSION['email']; // Armazena o e-mail do usuário logado

    // Verifica se há parâmetro de busca na URL
    if(!empty($_GET['busca'])) {
        $busca = $_GET['busca']; // Obtém o valor da busca
        // Cria query com filtro de pesquisa
        $sql = "SELECT * FROM usuarios 
               WHERE id LIKE '%$busca%' 
               OR nome LIKE '%$busca%' 
               OR email LIKE '%$busca%' 
               ORDER BY id DESC"; 
    }
    else {
        // Cria query padrão sem filtros
        $sql = "SELECT * FROM usuarios ORDER BY id DESC";
    }

    $resultado = $conexao->query($sql); // Executa a query no banco

    header('Content-Type: text/html; charset=utf-8'); // Define o tipo de conteúdo como HTML com charset UTF-8
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"> <!-- Define codificação de caracteres -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Configura viewport para dispositivos móveis -->
    
    <!-- Importa CSS do Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    
    <!-- Importa JavaScript do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    
    <title>Painel do Sistema</title> <!-- Título da página -->
    
    <style>
        /* Estilização geral do corpo */
        body {
            background: linear-gradient(to right, rgb(20, 147, 220), rgb(17, 54, 71)) no-repeat fixed;
            color: white;
            overflow: hidden; /* Desabilita scrollbars padrão */
        }
        
        /* Estilização da barra de navegação */
        .navbar-brand{
            position: relative; /* Posição relativa para o título */
            top: 3px; /* Ajusta a posição vertical */
        }

        /* Container da tabela com scroll */
        .table-container {
            height: calc(100vh - 180px); /* Calcula altura dinâmica */
            overflow: auto; /* Habilita scroll quando necessário */
            margin: 20px; /* Margem externa */
        }
        
        /* Estilização do fundo da tabela */
        .table-bg {
            background-color: rgba(0, 0, 0, .3); /* Cor de fundo semi-transparente */
            border-radius: 15px 15px 0 0; /* Bordas arredondadas apenas no topo */
            min-width: 1200px; /* Largura mínima para evitar quebras */
        }
        
        /* Estilização da barra de pesquisa */
        .box-search {
            display: flex; /* Layout flexível */
            justify-content: center; /* Centraliza horizontalmente */
            gap: 2px; /* Espaço entre elementos */
            margin: 40px auto 0; /* Margem superior aumentada */
            width: 70%; /* Largura relativa */
            max-width: 800px; /* Largura máxima */
            align-items: center; /* Alterado para centralização vertical */
            height: 50px; /* Altura fixa para o container */
        }

        /* Campo de entrada da pesquisa */
        .box-search .form-control {
            width: 75% !important; /* Largura do campo */
            min-width: 250px; /* Largura mínima */
            transition: all 0.3s ease; /* Transição suave para hover/focus */
            height: 100%; /* Ocupa 100% da altura do container */
            padding: 10px 15px; /* Padding consistente com o botão */
        }

        /* Botão da pesquisa */
        .box-search .btn {
            padding: 0 25px; /* Aumenta padding vertical */
            font-size: 15px; /* Mantém igual ao input */
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
        }
        
        /* Células da tabela */
        th, td {
            padding: 8px; /* Espaçamento interno das células */
        }
        
        /* Linhas do corpo da tabela */
        tbody tr {
            border: 1px solid white; /* Borda branca */
            border-left: 0; /* Remove borda esquerda */
            border-right: 0; /* Remove borda direita */
        }

        /* Última célula (coluna de ações) */
        td:last-child {
            white-space: nowrap; /* Impede quebra de linha */
            min-width: 120px; /* Largura mínima para os botões */
            gap: 5px; /* Espaço entre botões */
        }

        /* Ajuste de botões */
        .btn {
            padding: 8px 12px !important; /* Tamanho reduzido */
            margin: 2px !important; /* Espaçamento entre botões */
        }

        /* Media query para tablets */
        @media screen and (max-width: 768px) {
            .table-container {
                margin: 10px; /* Reduz margem */
                height: calc(100vh - 160px); /* Ajuste de altura */
            }
            
            .box-search {
                max-width: 500px; /* Largura máxima reduzida */
                width: 95%; /* Ocupa quase toda a largura */
            }
            
            .box-search .form-control {
                width: 100% !important; /* Campo ocupa toda a largura */
            }
            
            .box-search .btn {
                padding: 0 20px; /* Reduz espaçamento interno */
            }
        }

        /* Media query para celulares pequenos */
        @media screen and (max-width: 576px) {
            .table-container {
                height: calc(100vh - 140px); /* Altura ajustada */
            }
            
            .box-search {
                width: 80%; /* Largura maior */
                padding: 0 15px; /* Padding horizontal */
            }
            
            /* Ícones dentro dos botões */
            .btn svg {
                width: 14px; /* Largura reduzida */
                height: 14px; /* Altura reduzida */
            }
            
            /* Título da navbar */
            .navbar-brand {
                font-size: 1.25rem; /* Tamanho da fonte reduzido */
            }
        }
    </style>
</head>
<body>
    <!-- Barra de navegação -->
    <nav class="navbar bg-dark border-bottom border-body" data-bs-theme="dark">
        <div class="container-fluid">
            <!-- Título do sistema -->
            <h1 class="navbar-brand">Painel do Sistema</h1>
            <!-- Botão de logout -->
            <a href="logout.php" class="btn btn-danger me-5">Sair</a>
        </div>
    </nav>

    <?php
        // Exibe saudação personalizada com o e-mail do usuário
        echo "<h1 class='text-center mt-5'>Bem-vindo(a) <u>$logado</u></h1>";
    ?>

    <!-- Container da barra de pesquisa -->
    <div class="box-search">
        <!-- Campo de entrada de texto -->
        <input type="search" class="form-control w-25" placeholder="Pesquisar" id="pesquisar">
        <!-- Botão de pesquisa -->
        <button class="btn btn-primary" onclick="dadosBusca()">
            <!-- Ícone de lupa -->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
            </svg>
        </button>
    </div>

    <!-- Container principal da tabela -->
    <div class="table-container m-5">
        <!-- Tabela de dados -->
        <table class="table-bg">
            <thead>
                <tr>
                    <!-- Cabeçalhos das colunas -->
                    <th scope="col">#</th> <!-- ID -->
                    <th scope="col">Nome</th> 
                    <th scope="col">Senha</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Telefone</th>
                    <th scope="col">Sexo</th>
                    <th scope="col">Data de Nascimento</th>
                    <th scope="col">Cidade</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Endereço</th>
                    <th scope="col">...</th> <!-- Ações -->
                </tr>
            </thead>
            <tbody>
                <?php
                    // Loop através de cada registro retornado do banco
                    while($dados_usuario = mysqli_fetch_assoc($resultado)) {
                        echo "<tr>"; // Inicia nova linha na tabela
                        
                        // Exibe cada campo do usuário
                        echo "<td>" . $dados_usuario['id'] . "</td>"; // ID
                        echo "<td>" . $dados_usuario['nome'] . "</td>"; // Nome
                        echo "<td>" . $dados_usuario['senha'] . "</td>"; // Senha
                        echo "<td>" . $dados_usuario['email'] . "</td>"; // E-mail
                        echo "<td>" . $dados_usuario['telefone'] . "</td>"; // Telefone
                        echo "<td>" . $dados_usuario['genero'] . "</td>"; // Gênero
                        echo "<td>" . $dados_usuario['data_nascimento'] . "</td>"; // Data de nascimento
                        echo "<td>" . $dados_usuario['cidade'] . "</td>"; // Cidade
                        echo "<td>" . $dados_usuario['estado'] . "</td>"; // Estado
                        echo "<td>" . $dados_usuario['endereco'] . "</td>"; // Endereço
                        
                        // Célula com botões de ação
                        echo "<td>";
                        echo "<a class='btn btn-sm btn-primary' href='editar.php?id=$dados_usuario[id]'>";
                        echo "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>";
                        echo "<path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325'/>";
                        echo "</svg>";
                        echo "</a>";
                        echo "<a class='btn btn-sm btn-danger' href='excluir.php?id=$dados_usuario[id]'>";
                        echo "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash-fill' viewBox='0 0 16 16'>";
                        echo "<path d='M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0'/>";
                        echo "</svg>";
                        echo "</a>"; 
                        echo "</td>";
                        
                        echo "</tr>"; // Fecha a linha da tabela
                    }
                ?>            
            </tbody>
        </table>
    </div>
</body>

<script>
    // Captura elemento do campo de pesquisa
    var busca = document.getElementById('pesquisar');

    // Adiciona listener para tecla Enter
    busca.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            dadosBusca(); // Chama função de busca
        }
    });

    // Função que redireciona com parâmetro de busca
    dadosBusca=()=>{
        window.location = 'sistema.php?busca='+busca.value; // Atualiza URL com parâmetro
    }

    // Ajusta tabela conforme tamanho da tela
    window.addEventListener('resize', function() {
        if (window.innerWidth < 992) {
            document.querySelector('.table-bg').classList.add('scrollable-table'); // Adiciona classe para scroll
        } else {
            document.querySelector('.table-bg').classList.remove('scrollable-table'); // Remove classe de scroll
        }
    });
</script>
</html>