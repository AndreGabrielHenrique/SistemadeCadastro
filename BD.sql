-- BD.sql
-- Arquivo de dump/backup do banco de dados MySQL
-- Gerado automaticamente pelo MySQL Workbench ou utilitário mysqldump

-- Remove a tabela 'usuarios' se ela já existir (para evitar conflitos)
DROP TABLE IF EXISTS `usuarios`;

-- Configurações de conjunto de caracteres (charset) para compatibilidade
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;

-- Criação da tabela principal 'usuarios' com seus campos
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,                    -- ID único autoincrementável (chave primária)
  `nome` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL, -- Nome completo (máx. 45 caracteres)
  `senha` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL, -- Senha (armazenada em texto plano - NÃO RECOMENDADO para produção)
  `email` varchar(110) COLLATE utf8mb4_unicode_ci NOT NULL, -- E-mail (máx. 110 caracteres)
  `telefone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL, -- Telefone (máx. 15 caracteres)
  `genero` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL, -- Gênero (máx. 15 caracteres)
  `data_nascimento` date NOT NULL,                    -- Data de nascimento (tipo DATE)
  `cidade` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL, -- Cidade (máx. 45 caracteres)
  `estado` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL, -- Estado (máx. 45 caracteres)
  `endereco` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL, -- Endereço (máx. 45 caracteres)
  PRIMARY KEY (`id`)                                  -- Define 'id' como chave primária
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Comentário indicando seção de inserção de dados (porém sem dados neste dump)
-- Dumping data for table `usuarios`
--

-- Bloqueia a tabela para operações de inserção em massa (otimização)
LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;       -- Desabilita índices temporariamente para inserção rápida
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;        -- Reabilita índices após inserção
UNLOCK TABLES;                                         -- Libera o bloqueio da tabela

-- Restaura configurações de ambiente SQL originais
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;               -- Restaura configuração de fuso horário
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;                 -- Restaura modo SQL original
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */; -- Restaura verificação de chaves estrangeiras
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;       -- Restaura verificação de unicidade
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */; -- Restaura charset do cliente
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */; -- Restaura charset dos resultados
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */; -- Restaura collation da conexão
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;               -- Restaura configuração de notas SQL

-- Carimbo de data/hora da geração do dump
-- Dump completed on 2025-05-23 23:07:06