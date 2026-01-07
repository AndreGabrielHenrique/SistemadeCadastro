-- scripts.sql
-- Este arquivo contém consultas SQL para operações no banco de dados do sistema de cadastro

-- Seleciona todos os registros da tabela 'usuarios' com todas as colunas
-- Útil para visualizar todos os dados cadastrados
SELECT * FROM usuarios;

-- Seleciona todos os registros da tabela 'usuarios' ordenados pelo ID em ordem decrescente (do mais recente para o mais antigo)
-- Útil para ver os registros mais recentes primeiro
SELECT * FROM usuarios ORDER BY id DESC;

-- Conta o número total de registros na tabela 'usuarios' e ordena pelo ID em ordem decrescente (embora a ordenação não afete a contagem)
-- Útil para saber quantos usuários estão cadastrados no sistema
SELECT COUNT(*) FROM usuarios ORDER BY id DESC;