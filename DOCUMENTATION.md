# Sistema de Cadastro - Documentação Completa

## 📋 Visão Geral

O **Sistema de Cadastro** é uma aplicação web desenvolvida em PHP com MySQL que permite o gerenciamento completo de usuários. O sistema oferece funcionalidades de autenticação (login/logout), cadastro de novos usuários, listagem, edição e exclusão de registros com controle de sessão seguro.

**Versão:** 1.0
**Tecnologias:** PHP, MySQL, HTML5, CSS3, JavaScript
**Banco de Dados:** MySQL (InfinityFree)
**Última Atualização:** Janeiro de 2026

---

## 🎯 Funcionalidades Principais

### 1. Autenticação de Usuários
- Login com e-mail e senha
- Validação de credenciais contra banco de dados
- Sistema de sessões PHP para manter usuário logado
- Logout com destruição de sessão
- Mensagens de erro personalizadas para login inválido

### 2. Cadastro de Novos Usuários
- Formulário completo com validação de dados
- Captura de informações pessoais:
  - Nome (mínimo 3 caracteres)
  - E-mail (validação de formato e duplicidade)
  - Senha (mínimo 6 caracteres)
  - Telefone (mínimo 10 dígitos)
  - Gênero (feminino/masculino/outro)
  - Data de Nascimento (validação de data)
  - Cidade, Estado, Endereço
- Validação em tempo real no servidor
- Prevenção de e-mails duplicados

### 3. Painel do Sistema (Dashboard)
- Listagem de todos os usuários (exceto o próprio usuário logado)
- Busca avançada com filtro por múltiplos campos
- Exibição de informações do usuário logado
- Interface responsiva e intuitiva

### 4. Edição de Registros
- Carregamento de dados do usuário em formulário
- Edição de todos os campos cadastrais
- Validação de dados durante edição
- Atualização segura no banco de dados

### 5. Exclusão de Registros
- Funcionalidade para remover usuários
- Proteção contra exclusão do próprio usuário logado
- Confirmação antes da exclusão

### 6. Controle de Sessão
- Verificação crítica: se usuário logado ainda existe no banco
- Sessão expirada automática se usuário foi deletado por outro usuário
- Redirecionamento automático para login se não autenticado
- Prevenção de acesso não autorizado a páginas protegidas

---

## 🗄️ Estrutura do Banco de Dados

### Tabela: usuarios

| Campo | Tipo | Tamanho | Descrição |
|-------|------|---------|-----------|
| id | INT | - | Identificador único (AUTO_INCREMENT, Chave Primária) |
| nome | VARCHAR | 45 | Nome completo do usuário |
| senha | VARCHAR | 255 | Senha do usuário (aumentado para suportar hash) |
| email | VARCHAR | 110 | Endereço de e-mail único |
| telefone | VARCHAR | 15 | Número de telefone |
| genero | VARCHAR | 15 | Gênero do usuário |
| data_nascimento | DATE | - | Data de nascimento |
| cidade | VARCHAR | 45 | Cidade de residência |
| estado | VARCHAR | 45 | Estado de residência |
| endereco | VARCHAR | 45 | Endereço completo |

**Charset:** UTF8MB4 (suporta caracteres especiais e acentuação)
**Collation:** utf8mb4_unicode_ci
**Engine:** InnoDB

---

## 📁 Estrutura de Arquivos

```
Sistema de Cadastro/
├── index.php                 # Página inicial com botões de Login e Cadastro
├── login.php                 # Página de autenticação de usuários
├── formulario.php            # Formulário de cadastro de novos usuários
├── sistema.php               # Dashboard principal (listagem e busca)
├── editar.php                # Formulário de edição de usuários
├── salvarEdicao.php          # Script para processar edições
├── excluir.php               # Script para processar exclusões
├── logout.php                # Script para logout do usuário
├── config.php                # Configurações de conexão com banco de dados
├── check.php                 # Script auxiliar de verificação
├── testLogin.php             # Script de teste de login
├── teste_conexao.php         # Script para testar conexão com banco
├── BD.sql                    # Dump do banco de dados com estrutura e dados iniciais
├── scripts.sql               # Scripts SQL adicionais (se necessário)
├── README.md                 # Guia rápido do projeto
└── DOCUMENTATION.pdf          # Este arquivo (documentação completa)
```

---

## 🔧 Configuração e Instalação

### Pré-requisitos
- PHP 7.4+ com suporte a MySQLi
- Servidor MySQL (local ou remoto)
- Navegador web moderno
- Servidor web (Apache, Nginx, etc.)

### Passo 1: Configurar Banco de Dados
1. Abra o arquivo `BD.sql` no editor
2. Identifique o nome do banco de dados na primeira linha
3. Crie o banco no seu servidor MySQL ou importe o arquivo SQL

### Passo 2: Configurar Conexão
Edite o arquivo `config.php` com as credenciais do seu servidor:

```php
$bdhost = 'seu_host_aqui';              // Host do banco de dados
$bdusuario = 'seu_usuario_aqui';        // Usuário do MySQL
$bdsenha = 'sua_senha_aqui';            // Senha do MySQL
$bdnome = 'seu_banco_aqui';             // Nome do banco de dados
```

### Passo 3: Carregar no Servidor
1. Copie todos os arquivos para o servidor web
2. Acesse `http://localhost/caminho/do/projeto/index.php`
3. Clique em "Cadastro" para criar primeira conta ou use dados de exemplo

---

## 🔐 Segurança

### Implementado
✅ Uso de MySQLi com Prepared Statements (prevenção de SQL Injection)
✅ Validação de dados no servidor
✅ Controle de sessão com verificação de usuário no banco
✅ Prevenção de acesso não autorizado a páginas protegidas
✅ Charset UTF8MB4 para prevenir ataques de codificação
✅ Destruição de sessão após logout

### Recomendações Futuras
⚠️ Senhas: Atualmente armazenadas em texto plano. Implementar password_hash() e password_verify()
⚠️ HTTPS: Usar conexão segura em produção
⚠️ CSRF: Implementar tokens CSRF em formulários
⚠️ Rate Limiting: Limitar tentativas de login
⚠️ Logs: Implementar sistema de auditoria de ações
⚠️ Criptografia: Criptografar dados sensíveis no banco

---

## 📖 Fluxo de Usuário

### Primeiro Acesso (Novo Usuário)
index.php → Cadastro → formulario.php → validação → salvar no banco → login.php

### Usuário Existente
index.php → Login → login.php → validação → criar sessão → sistema.php

### Edição de Usuário
sistema.php → Editar → editar.php → carrega dados → formulário → salvarEdicao.php → volta para sistema.php

### Exclusão de Usuário
sistema.php → Deletar → excluir.php → remove do banco → volta para sistema.php

---

## 🚀 Como Usar

### Login
1. Acesse a página inicial
2. Clique no botão "LOGIN"
3. Insira seu e-mail e senha
4. Clique em "Login"
5. Se os dados forem válidos, você será redirecionado para o dashboard

### Cadastro
1. Acesse a página inicial
2. Clique no botão "CADASTRO"
3. Preencha todos os campos do formulário
4. Clique em "Registrar"
5. Se todos os dados forem válidos, sua conta será criada

### Buscar Usuários
1. No dashboard, use o campo de busca
2. Insira parte do nome, e-mail, cidade, etc.
3. Os resultados serão filtrados em tempo real

### Editar Usuário
1. Clique no botão "Editar" ao lado do usuário desejado
2. Modifique os dados
3. Clique em "Salvar"

### Excluir Usuário
1. Clique no botão "Deletar" ao lado do usuário
2. Confirme a exclusão
3. O usuário será removido do sistema

### Logout
1. Clique no botão "Logout"
2. Sua sessão será encerrada e você voltará para a página inicial

---

## 📞 Informações do Projeto

**Desenvolvido por:** André
**Data de Criação:** 2025
**Última Modificação:** Janeiro de 2026
**Tecnologias:** PHP 7.4+, MySQL, HTML5, CSS3, JavaScript

---

## 📝 Histórico de Versões

### v1.0 - Janeiro de 2026
- Versão inicial do sistema
- Implementação completa de cadastro, login, edição e exclusão
- Validação de dados no servidor
- Controle de sessão com verificação crítica
- Documentação em português

---

**Nota Final:** Este documento foi gerado para ser compreensivo e servir como guia completo para desenvolvedores e usuários do sistema.