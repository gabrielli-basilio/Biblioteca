SET search_path TO bribriotecaDB;

CREATE VIEW vw_disponibilidade_livros AS
SELECT
    l.id_livro,
    l.titulo AS livro,
    l.isbn,
    COALESCE(c.nome, 'Sem Categoria') AS categoria,
    e.nome AS editora,
    a.nome AS autor,
    l.quantidade_exemplar,
    COUNT(ie.livro_id) AS exemplares_emprestados,
    l.quantidade_exemplar - COUNT(ie.livro_id) AS exemplares_disponiveis
FROM livros l
LEFT JOIN categorias c
    ON c.id_categoria = l.categoria_id
INNER JOIN editoras e
    ON l.editora_id = e.id_editora
INNER JOIN autores a
    ON l.autor_id = a.id_autor
LEFT JOIN itens_emprestimos ie
    ON ie.livro_id = l.id_livro
    AND ie.data_devolucao IS NULL
GROUP BY
    l.id_livro, l.titulo, l.isbn, c.nome, e.nome, a.nome, l.quantidade_exemplar
ORDER BY exemplares_disponiveis ASC;

SELECT * FROM vw_disponibilidade_livros;

-------------------------------------------------------------------------------------------------

CREATE VIEW vw_livros_mais_emprestados AS
SELECT
    l.id_livro,
    l.titulo AS livro,
    l.isbn,
    COALESCE(c.nome, 'Sem Categoria') AS categoria,
    e.nome AS editora,
    a.nome AS autor,
    COUNT(ie.livro_id) AS total_emprestimos
FROM livros l
LEFT JOIN categorias c
    ON c.id_categoria = l.categoria_id
INNER JOIN editoras e
    ON l.editora_id = e.id_editora
INNER JOIN autores a
    ON l.autor_id = a.id_autor
LEFT JOIN itens_emprestimos ie
    ON ie.livro_id = l.id_livro
GROUP BY
    l.id_livro, l.titulo, l.isbn, c.nome, e.nome, a.nome
ORDER BY total_emprestimos DESC
LIMIT 30;

SELECT * FROM vw_livros_mais_emprestados;

-------------------------------------------------------------------------------------------------

CREATE VIEW vw_emprestimos_atrasados AS
SELECT
    e.id_emprestimo,
    cl.nome AS cliente,
    cl.telefone,
    l.titulo AS livro,
    e.data_emprestimo,
    e.data_prevista_devolucao,
    CURRENT_DATE - e.data_prevista_devolucao AS dias_atraso,
    (
		SELECT COUNT(*) 
     FROM itens_emprestimos ie2 
     WHERE ie2.emprestimo_id = e.id_emprestimo
	 ) AS total_livros_no_emprestimo
FROM emprestimos e
JOIN clientes cl
    ON cl.id_cliente = e.cliente_id
JOIN itens_emprestimos ie
    ON ie.emprestimo_id = e.id_emprestimo
JOIN livros l
    ON l.id_livro = ie.livro_id
WHERE ie.data_devolucao IS NULL
    AND e.data_prevista_devolucao < CURRENT_DATE
ORDER BY dias_atraso DESC;

SELECT * FROM vw_emprestimos_atrasados;

-------------------------------------------------------------------------------------------------

CREATE MATERIALIZED VIEW vm_estatisticas_por_categoria AS
SELECT
    c.id_categoria,
    c.nome AS categoria,
    COUNT(DISTINCT l.id_livro) AS total_livros,
    SUM(l.quantidade_exemplar) AS total_exemplares,
    COUNT(ie.livro_id) AS total_emprestimos,
    COUNT(DISTINCT e.cliente_id) AS clientes_distintos,
    (
		SELECT COUNT(*)
     FROM livros l2
     WHERE l2.categoria_id = c.id_categoria
     	AND NOT EXISTS (
			SELECT 1 FROM itens_emprestimos ie2
			WHERE ie2.livro_id = l2.id_livro
		)
	) AS livros_nunca_emprestados
FROM categorias c
LEFT JOIN livros l
    ON l.categoria_id = c.id_categoria
LEFT JOIN itens_emprestimos ie
    ON ie.livro_id = l.id_livro
LEFT JOIN emprestimos e
    ON e.id_emprestimo = ie.emprestimo_id
GROUP BY c.id_categoria, c.nome
ORDER BY total_emprestimos DESC;

SELECT * FROM vm_estatisticas_por_categoria;

-------------------------------------------------------------------------------------------------

CREATE MATERIALIZED VIEW vm_ranking_clientes AS
SELECT
    cl.id_cliente,
    cl.nome,
    cl.email,
    COUNT(DISTINCT e.id_emprestimo) AS total_emprestimos,
    COUNT(ie.livro_id) AS total_livros_emprestados,
    SUM(
		CASE WHEN e.status = 'atrasado' 
		THEN 1 
		ELSE 0 END
	) AS total_atrasos,
    (SELECT MAX(e2.data_emprestimo)
     FROM emprestimos e2
     WHERE e2.cliente_id = cl.id_cliente) AS ultimo_emprestimo
FROM clientes cl
JOIN emprestimos e
    ON e.cliente_id = cl.id_cliente
JOIN itens_emprestimos ie
    ON ie.emprestimo_id = e.id_emprestimo
GROUP BY cl.id_cliente, cl.nome, cl.email
ORDER BY total_livros_emprestados DESC;

SELECT * FROM vm_ranking_clientes;

-------------------------------------------------------------------------------------------------

-- Criação dos Index

CREATE INDEX idx_livroid_itensemprestimos_ativos
    ON itens_emprestimos (livro_id)
    WHERE data_devolucao IS NULL;

CREATE INDEX idx_dataprevista_emprestimos
    ON emprestimos (data_prevista_devolucao);