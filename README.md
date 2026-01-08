# Sistema de Cadastro — Resumo

Resumo rápido:
- Projeto em PHP + MySQL para cadastro e gerenciamento de usuários.
- Funcionalidades: cadastro (formulário), login, painel (listar/editar/excluir), sessão e logout.
- Banco de dados: tabela `usuarios` com campos como `nome`, `email`, `senha` (texto plano), `telefone`, `genero`, `data_nascimento`, `cidade`, `estado`, `endereco`.

Como usar:
1. Configure a conexão em `config.php` (host, usuário, senha, nome do DB).
2. Importe `BD.sql` no MySQL para criar a tabela e inserir dados de exemplo.
3. Acesse `index.php` para navegar entre login e cadastro.

Segurança e observações:
- As senhas são armazenadas em texto plano no banco (arquivo `BD.sql` e lógica em `testLogin.php`). Recomendado usar `password_hash`/`password_verify`.
- Algumas queries ainda concatenam parâmetros diretamente (`excluir.php`, `salvarEdicao.php`) — trocar por prepared statements onde necessário.

Gerar o PDF da documentação:
- Tente executar o script PowerShell `gerar_pdf.ps1` na raiz do projeto (Windows):

  .\gerar_pdf.ps1

- O script usa `pandoc` para converter `DOCUMENTATION.md` em `DOCUMENTATION.pdf`. Se não houver `pandoc`, siga as instruções exibidas.

Arquivos principais:
- `index.php`, `login.php`, `formulario.php`, `sistema.php`, `editar.php`, `salvarEdicao.php`, `excluir.php`, `logout.php`, `config.php`, `BD.sql`, `scripts.sql`, `testLogin.php`.

Autor: documentação gerada automaticamente.
