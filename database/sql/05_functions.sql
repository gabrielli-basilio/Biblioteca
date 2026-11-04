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

-- Cálculo de multa
CREATE OR REPLACE FUNCTION fn_calcular_multa(p_emprestimo_id INT)
RETURNS NUMERIC
LANGUAGE plpgsql
AS $$
DECLARE
    v_dias_atraso INT;
BEGIN
    SELECT COALESCE(SUM(GREATEST(COALESCE(ie.data_devolucao, CURRENT_DATE) - e.data_prevista_devolucao, 0)), 0)
    INTO v_dias_atraso
    FROM itens_emprestimos ie
    JOIN emprestimos e ON e.id_emprestimo = ie.emprestimo_id
    WHERE e.id_emprestimo = p_emprestimo_id;

    RETURN v_dias_atraso * 1.00;
END $$;

-- Testa a function exemplares_disponiveis
SELECT
	l.id_livro,
	l.titulo,
	l.quantidade_exemplar AS total,
	COUNT(ie.livro_id) AS emprestados_agora,
	fn_exemplares_disponiveis(l.id_livro) AS disponiveis
FROM livros l
LEFT JOIN itens_emprestimos ie
	ON ie.livro_id = l.id_livro AND ie.data_devolucao IS NULL
GROUP BY l.id_livro, l.titulo, l.quantidade_exemplar
ORDER BY emprestados_agora DESC, l.id_livro
LIMIT 5;

-- Testa a function calcular_multa
SELECT 
	e.id_emprestimo,
	e.cliente_id,
	fn_calcular_multa(e.id_emprestimo) AS multa_function,
	(SELECT 
		COALESCE(SUM(GREATEST(COALESCE(ie.data_devolucao, CURRENT_DATE) - e.data_prevista_devolucao, 0)), 0) * 1.00
	FROM itens_emprestimos ie
	WHERE ie.emprestimo_id = e.id_emprestimo) AS multa_conferencia
FROM emprestimos e
ORDER BY multa_function DESC
LIMIT 5;