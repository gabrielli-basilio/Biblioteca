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