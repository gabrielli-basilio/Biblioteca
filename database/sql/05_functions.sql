SET search_path TO bribriotecadb;

-- Exemplares disponíveis
CREATE OR REPLACE FUNCTION fn_exemplares_disponiveis(p_livro_id INT)
RETURNS INT
LANGUAGE plpgsql
AS $$
DECLARE
    v_total INT;
    v_emprestados INT;
BEGIN
    SELECT quantidade_exemplar INTO v_total
    FROM livros WHERE id_livro = p_livro_id;

    SELECT COUNT(*) INTO v_emprestados
    FROM itens_emprestimos
    WHERE livro_id = p_livro_id AND data_devolucao IS NULL;

    RETURN COALESCE(v_total, 0) - v_emprestados;
END $$;