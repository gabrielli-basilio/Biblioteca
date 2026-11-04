SET search_path TO bribriotecadb;

-- Valida disponibilidade dos livros
CREATE OR REPLACE FUNCTION fn_valida_disponibilidade()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_total INT;
    v_emprestados INT;
BEGIN
    SELECT quantidade_exemplar INTO v_total
    FROM livros WHERE id_livro = NEW.livro_id
    FOR UPDATE;

    IF v_total IS NULL THEN
        RAISE EXCEPTION 'Livro não encontrado.';
    END IF;

    SELECT COUNT(*) INTO v_emprestados
    FROM itens_emprestimos
    WHERE livro_id = NEW.livro_id AND data_devolucao IS NULL;

    IF v_total - v_emprestados <= 0 THEN
        RAISE EXCEPTION 'O livro de código % não tem exemplares disponíveis.', NEW.livro_id;
    END IF;

    RETURN NEW;
END $$;

CREATE TRIGGER trg_valida_disponibilidade
BEFORE INSERT ON itens_emprestimos
FOR EACH ROW
EXECUTE FUNCTION fn_valida_disponibilidade();

-- Finaliza empréstimo devolvido
CREATE OR REPLACE FUNCTION fn_finaliza_emprestimo()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    IF NEW.data_devolucao IS NOT NULL THEN
        IF NOT EXISTS (
            SELECT 1 FROM itens_emprestimos
            WHERE emprestimo_id = NEW.emprestimo_id AND data_devolucao IS NULL
        ) THEN
            UPDATE emprestimos SET status = 'devolvido'
            WHERE id_emprestimo = NEW.emprestimo_id;
        END IF;
    END IF;

    RETURN NEW;
END $$;

CREATE TRIGGER trg_finaliza_emprestimo
AFTER UPDATE OF data_devolucao ON itens_emprestimos
FOR EACH ROW
EXECUTE FUNCTION fn_finaliza_emprestimo();