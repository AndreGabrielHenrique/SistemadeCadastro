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
- Tente executar o script Python `gerar_pdf.py` na raiz do projeto:

  python gerar_pdf.py

- O script converte automaticamente `DOCUMENTATION.md` em `DOCUMENTATION.pdf`. As dependências serão instaladas automaticamente se necessário.

Arquivos principais:
- `index.php`, `login.php`, `formulario.php`, `sistema.php`, `editar.php`, `salvarEdicao.php`, `excluir.php`, `logout.php`, `config.php`, `BD.sql`, `scripts.sql`, `testLogin.php`, `check.php`, `teste_conexao.php`.

## Documentação Completa
Para uma documentação detalhada e completa do sistema, consulte:
- `DOCUMENTATION.pdf` — Documentação em formato PDF

## Informações do Projeto
**Desenvolvido por:** André
**Versão:** 1.0
**Data de Criação:** 2025
**Última Atualização:** Janeiro de 2026

Autor: documentação gerada automaticamente.