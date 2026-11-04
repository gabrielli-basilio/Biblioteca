SET search_path TO bribriotecadb;
 
-- Adequações (3 CHECK)
ALTER TABLE emprestimos DROP CONSTRAINT IF EXISTS ck_emprestimos_status;
ALTER TABLE emprestimos
    ADD CONSTRAINT ck_emprestimos_status
    CHECK (status IN ('em_andamento', 'devolvido', 'atrasado'));
 
ALTER TABLE emprestimos DROP CONSTRAINT IF EXISTS ck_emprestimos_datas;
ALTER TABLE emprestimos
    ADD CONSTRAINT ck_emprestimos_datas
    CHECK (data_prevista_devolucao >= data_emprestimo);
 
ALTER TABLE livros DROP CONSTRAINT IF EXISTS ck_livros_quantidade;
ALTER TABLE livros
    ADD CONSTRAINT ck_livros_quantidade
    CHECK (quantidade_exemplar >= 0);
 
-- Índice
CREATE INDEX IF NOT EXISTS idx_clienteid_emprestimos ON emprestimos(cliente_id);

-- Confere os 3 CHECK e o índice
SELECT 'CHECK' AS tipo, conname AS nome, pg_get_constraintdef(oid) AS regra
FROM pg_constraint
WHERE contype = 'c'
  AND conrelid IN ('bribriotecadb.emprestimos'::regclass, 'bribriotecadb.livros'::regclass)
UNION ALL
SELECT 'ÍNDICE', indexname, indexdef
FROM pg_indexes
WHERE schemaname = 'bribriotecadb' AND indexname = 'idx_clienteid_emprestimos';