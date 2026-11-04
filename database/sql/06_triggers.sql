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

-- TESTE TRIGGER valida_disponibilidade
-- Deixa o livro 1 sem exemplar livre, tenta emprestar edesfaz tudo no final.
DO $$
DECLARE
	v_resultado TEXT := 'A trigger NÃO bloqueou o empréstimo';
BEGIN
	BEGIN
		UPDATE livros
		SET quantidade_exemplar = (SELECT COUNT(*) FROM itens_emprestimos
		WHERE livro_id = 1 AND data_devolucao IS NULL)
        WHERE id_livro = 1;
		
	INSERT INTO itens_emprestimos (emprestimo_id, livro_id)
		VALUES ((SELECT MIN(id_emprestimo) FROM emprestimos), 1);
		
	RAISE EXCEPTION 'desfazer';
	EXCEPTION WHEN OTHERS THEN
	IF SQLERRM <> 'desfazer' THEN
		v_resultado := 'BLOQUEADO: ' || SQLERRM;
		END IF;
    END;
	
    RAISE NOTICE '%', v_resultado;
END $$;

-- TESTE TRIGGER finaliza_emprestimo
-- O que faz: pega um empréstimo com só 1 livro pendente, devolve esse livro e mostra o status, depois desfaz tudo.

DO $$
DECLARE
    v_emprestimo INT;
    v_antes TEXT;
    v_depois TEXT;
BEGIN
	BEGIN
		INSERT INTO emprestimos (data_emprestimo, data_prevista_devolucao, cliente_id)
			VALUES (CURRENT_DATE, CURRENT_DATE + 7, (SELECT MIN(id_cliente) FROM clientes))
		RETURNING id_emprestimo, status INTO v_emprestimo, v_antes;
		
		-- garante um exemplar livre do livro 1 (desfeito no final)
		UPDATE livros SET quantidade_exemplar = quantidade_exemplar + 1
		WHERE id_livro = 1;
		
		INSERT INTO itens_emprestimos (emprestimo_id, livro_id)
		VALUES (v_emprestimo, 1);
		
		UPDATE itens_emprestimos
		SET data_devolucao = CURRENT_DATE
		WHERE emprestimo_id = v_emprestimo;
		
		SELECT status INTO v_depois FROM emprestimos WHERE id_emprestimo = v_emprestimo;
		
		RAISE EXCEPTION 'desfazer';
		EXCEPTION WHEN OTHERS THEN
		IF SQLERRM <> 'desfazer' THEN
			RAISE;
		END IF;
	END;
 
    RAISE NOTICE 'Empréstimo de teste %: status antes = %, status depois = %', v_emprestimo, v_antes, v_depois;
END $$;