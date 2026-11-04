SET search_path TO bribriotecadb;

CREATE OR REPLACE PROCEDURE sp_gabrielli_registrar_emprestimo(
    p_cliente_id    INTEGER,
    p_livros        INTEGER[],
    p_dias_prazo    INTEGER,
    INOUT p_emprestimo_id INTEGER DEFAULT NULL
)
LANGUAGE plpgsql
SET search_path = gabrielli_trabalho_bd, public
AS $$
DECLARE
    v_livros INTEGER[];
    v_livro  INTEGER;
BEGIN
    IF NOT EXISTS (SELECT 1 FROM clientes WHERE id_cliente = p_cliente_id) THEN
        RAISE EXCEPTION 'Cliente % não encontrado', p_cliente_id;
    END IF;
 
    v_livros := ARRAY(
        SELECT DISTINCT x
          FROM unnest(COALESCE(p_livros, ARRAY[]::INTEGER[])) AS x
         WHERE x IS NOT NULL
         ORDER BY x
    );
 
    IF COALESCE(array_length(v_livros, 1), 0) NOT BETWEEN 1 AND 5 THEN
        RAISE EXCEPTION 'O empréstimo deve ter de 1 a 5 livros';
    END IF;
 
    IF p_dias_prazo IS NULL OR p_dias_prazo <= 0 THEN
        RAISE EXCEPTION 'O prazo deve ser positivo';
    END IF;
 
    IF EXISTS (
        SELECT 1
          FROM emprestimos
         WHERE cliente_id = p_cliente_id
           AND status <> 'devolvido'
           AND (status = 'atrasado' OR data_prevista_devolucao < CURRENT_DATE)
    ) THEN
        RAISE EXCEPTION 'Cliente % possui empréstimos em atraso', p_cliente_id;
    END IF;
 
    INSERT INTO emprestimos (data_emprestimo, data_prevista_devolucao, status, cliente_id)
    VALUES (CURRENT_DATE, CURRENT_DATE + p_dias_prazo, 'em_andamento', p_cliente_id)
    RETURNING id_emprestimo INTO p_emprestimo_id;
 
    FOREACH v_livro IN ARRAY v_livros LOOP
        INSERT INTO itens_emprestimos (emprestimo_id, livro_id)
        VALUES (p_emprestimo_id, v_livro);
    END LOOP;
END;
$$;