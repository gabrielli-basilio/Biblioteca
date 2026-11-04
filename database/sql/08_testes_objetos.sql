SET search_path TO bribriotecadb;

DROP TABLE IF EXISTS pg_temp.resultado_testes;
CREATE TEMP TABLE resultado_testes (
    n        INTEGER,
    teste    TEXT,
    esperado TEXT,
    obtido   TEXT,
    ok       BOOLEAN
);
 
DO $testes$
DECLARE
    v_res     JSONB   := '[]'::JSONB;
    v_cli_a   INTEGER;
    v_cli_b   INTEGER;
    v_livro1  INTEGER;   -- 2 exemplares
    v_livro2  INTEGER;   -- 1 exemplar
    v_emp_b   INTEGER;
    v_emp_a   INTEGER;
    v_obtido  TEXT;
    v_esper   TEXT;
BEGIN
    BEGIN  -- subtransação: tudo o que acontece aqui dentro é desfeito no final
 
        -- ---------- Dados de teste ----------
        INSERT INTO clientes (nome, cpf, telefone, email, cep, logradouro, numero_residencial)
        VALUES ('Cliente Teste A', '99999999901', '99999999901', 'teste.a@objetos.exemplo', '85010000', 'Rua Teste', '1')
        RETURNING id_cliente INTO v_cli_a;
 
        INSERT INTO clientes (nome, cpf, telefone, email, cep, logradouro, numero_residencial)
        VALUES ('Cliente Teste B', '99999999902', '99999999902', 'teste.b@objetos.exemplo', '85010000', 'Rua Teste', '2')
        RETURNING id_cliente INTO v_cli_b;
 
        INSERT INTO livros (titulo, isbn, ano_publicacao, quantidade_exemplar, editora_id, autor_id)
        VALUES ('Livro Teste 1', '9999999999901', 2020, 2,
                (SELECT MIN(id_editora) FROM editoras), (SELECT MIN(id_autor) FROM autores))
        RETURNING id_livro INTO v_livro1;
 
        INSERT INTO livros (titulo, isbn, ano_publicacao, quantidade_exemplar, editora_id, autor_id)
        VALUES ('Livro Teste 2', '9999999999902', 2020, 1,
                (SELECT MIN(id_editora) FROM editoras), (SELECT MIN(id_autor) FROM autores))
        RETURNING id_livro INTO v_livro2;
 
        -- ---------- Function 1 ----------
        v_obtido := fn_gabrielli_exemplares_disponiveis(v_livro1)::TEXT;
        v_res := v_res || jsonb_build_object('n', 1, 'teste', 'Function 1: livro novo com 2 exemplares',
            'esperado', '2', 'obtido', v_obtido, 'ok', v_obtido IS NOT DISTINCT FROM '2');
 
        v_obtido := fn_gabrielli_exemplares_disponiveis(-1)::TEXT;
        v_res := v_res || jsonb_build_object('n', 2, 'teste', 'Function 1: livro inexistente',
            'esperado', NULL, 'obtido', v_obtido, 'ok', v_obtido IS NULL);
 
        -- ---------- Procedure 1: empréstimo válido ----------
        CALL sp_gabrielli_registrar_emprestimo(v_cli_b, ARRAY[v_livro1, v_livro2], 14, v_emp_b);
 
        SELECT COUNT(*)::TEXT || ' itens / ' || e.status
          INTO v_obtido
          FROM emprestimos e
          JOIN itens_emprestimos i ON i.emprestimo_id = e.id_emprestimo
         WHERE e.id_emprestimo = v_emp_b
         GROUP BY e.status;
        v_res := v_res || jsonb_build_object('n', 3, 'teste', 'Procedure 1: empréstimo com 2 livros',
            'esperado', '2 itens / em_andamento', 'obtido', v_obtido, 'ok', v_obtido = '2 itens / em_andamento');
 
        v_obtido := fn_gabrielli_exemplares_disponiveis(v_livro1) || ' / ' || fn_gabrielli_exemplares_disponiveis(v_livro2);
        v_res := v_res || jsonb_build_object('n', 4, 'teste', 'Function 1: disponíveis depois do empréstimo',
            'esperado', '1 / 0', 'obtido', v_obtido, 'ok', v_obtido = '1 / 0');
 
        v_obtido := fn_gabrielli_calcular_multa(v_emp_b)::TEXT;
        v_res := v_res || jsonb_build_object('n', 5, 'teste', 'Function 2: empréstimo no prazo não tem multa',
            'esperado', '0.00', 'obtido', v_obtido, 'ok', v_obtido = '0.00');
 
        -- ---------- Trigger 1: sem exemplar disponível ----------
        v_esper := format('Livro %s sem exemplares disponíveis', v_livro2);
        BEGIN
            CALL sp_gabrielli_registrar_emprestimo(v_cli_a, ARRAY[v_livro2], 14, v_emp_a);
            v_obtido := 'não deu erro';
        EXCEPTION WHEN OTHERS THEN
            v_obtido := SQLERRM;
        END;
        v_res := v_res || jsonb_build_object('n', 6, 'teste', 'Trigger 1: bloqueia livro sem exemplar',
            'esperado', v_esper, 'obtido', v_obtido, 'ok', v_obtido = v_esper);
 
        SELECT COUNT(*)::TEXT INTO v_obtido FROM emprestimos WHERE cliente_id = v_cli_a;
        v_res := v_res || jsonb_build_object('n', 7, 'teste', 'Procedure 1: erro desfaz o empréstimo',
            'esperado', '0', 'obtido', v_obtido, 'ok', v_obtido = '0');
 
        -- ---------- Procedure 1: validações ----------
        v_esper := 'Cliente -1 não encontrado';
        BEGIN
            CALL sp_gabrielli_registrar_emprestimo(-1, ARRAY[v_livro1], 14, v_emp_a);
            v_obtido := 'não deu erro';
        EXCEPTION WHEN OTHERS THEN v_obtido := SQLERRM; END;
        v_res := v_res || jsonb_build_object('n', 8, 'teste', 'Procedure 1: cliente inexistente',
            'esperado', v_esper, 'obtido', v_obtido, 'ok', v_obtido = v_esper);
 
        v_esper := 'O empréstimo deve ter de 1 a 5 livros';
        BEGIN
            CALL sp_gabrielli_registrar_emprestimo(v_cli_a, ARRAY[]::INTEGER[], 14, v_emp_a);
            v_obtido := 'não deu erro';
        EXCEPTION WHEN OTHERS THEN v_obtido := SQLERRM; END;
        v_res := v_res || jsonb_build_object('n', 9, 'teste', 'Procedure 1: nenhum livro',
            'esperado', v_esper, 'obtido', v_obtido, 'ok', v_obtido = v_esper);
 
        BEGIN
            CALL sp_gabrielli_registrar_emprestimo(v_cli_a, ARRAY[1, 2, 3, 4, 5, 6], 14, v_emp_a);
            v_obtido := 'não deu erro';
        EXCEPTION WHEN OTHERS THEN v_obtido := SQLERRM; END;
        v_res := v_res || jsonb_build_object('n', 10, 'teste', 'Procedure 1: 6 livros',
            'esperado', v_esper, 'obtido', v_obtido, 'ok', v_obtido = v_esper);
 
        v_esper := 'O prazo deve ser positivo';
        BEGIN
            CALL sp_gabrielli_registrar_emprestimo(v_cli_a, ARRAY[v_livro1], 0, v_emp_a);
            v_obtido := 'não deu erro';
        EXCEPTION WHEN OTHERS THEN v_obtido := SQLERRM; END;
        v_res := v_res || jsonb_build_object('n', 11, 'teste', 'Procedure 1: prazo zero',
            'esperado', v_esper, 'obtido', v_obtido, 'ok', v_obtido = v_esper);
 
        -- ---------- Procedure 2 + Trigger 2 ----------
        CALL sp_gabrielli_registrar_devolucao(v_emp_b, v_livro1);
        SELECT status || ' / disponíveis ' || fn_gabrielli_exemplares_disponiveis(v_livro1)
          INTO v_obtido FROM emprestimos WHERE id_emprestimo = v_emp_b;
        v_res := v_res || jsonb_build_object('n', 12, 'teste', 'Procedure 2: devolução parcial mantém em andamento',
            'esperado', 'em_andamento / disponíveis 2', 'obtido', v_obtido,
            'ok', v_obtido = 'em_andamento / disponíveis 2');
 
        v_esper := format('Livro %s já foi devolvido no empréstimo %s', v_livro1, v_emp_b);
        BEGIN
            CALL sp_gabrielli_registrar_devolucao(v_emp_b, v_livro1);
            v_obtido := 'não deu erro';
        EXCEPTION WHEN OTHERS THEN v_obtido := SQLERRM; END;
        v_res := v_res || jsonb_build_object('n', 13, 'teste', 'Procedure 2: livro já devolvido',
            'esperado', v_esper, 'obtido', v_obtido, 'ok', v_obtido = v_esper);
 
        CALL sp_gabrielli_registrar_devolucao(v_emp_b);
        SELECT status INTO v_obtido FROM emprestimos WHERE id_emprestimo = v_emp_b;
        v_res := v_res || jsonb_build_object('n', 14, 'teste', 'Trigger 2: último livro devolvido finaliza o empréstimo',
            'esperado', 'devolvido', 'obtido', v_obtido, 'ok', v_obtido = 'devolvido');
 
        v_esper := format('Empréstimo %s não possui livros pendentes', v_emp_b);
        BEGIN
            CALL sp_gabrielli_registrar_devolucao(v_emp_b);
            v_obtido := 'não deu erro';
        EXCEPTION WHEN OTHERS THEN v_obtido := SQLERRM; END;
        v_res := v_res || jsonb_build_object('n', 15, 'teste', 'Procedure 2: nada pendente',
            'esperado', v_esper, 'obtido', v_obtido, 'ok', v_obtido = v_esper);
 
        -- ---------- Atraso: multa, Procedure 3 e bloqueio de novo empréstimo ----------
        CALL sp_gabrielli_registrar_emprestimo(v_cli_a, ARRAY[v_livro1], 14, v_emp_a);
        UPDATE emprestimos
           SET data_emprestimo = CURRENT_DATE - 20,
               data_prevista_devolucao = CURRENT_DATE - 6
         WHERE id_emprestimo = v_emp_a;
 
        v_obtido := fn_gabrielli_calcular_multa(v_emp_a)::TEXT;
        v_res := v_res || jsonb_build_object('n', 16, 'teste', 'Function 2: 1 livro, 6 dias de atraso',
            'esperado', '6.00', 'obtido', v_obtido, 'ok', v_obtido = '6.00');
 
        CALL sp_gabrielli_atualizar_atrasados();
        SELECT status INTO v_obtido FROM emprestimos WHERE id_emprestimo = v_emp_a;
        v_res := v_res || jsonb_build_object('n', 17, 'teste', 'Procedure 3: marca o empréstimo vencido como atrasado',
            'esperado', 'atrasado', 'obtido', v_obtido, 'ok', v_obtido = 'atrasado');
 
        v_esper := format('Cliente %s possui empréstimos em atraso', v_cli_a);
        BEGIN
            CALL sp_gabrielli_registrar_emprestimo(v_cli_a, ARRAY[v_livro1], 14, v_emp_b);
            v_obtido := 'não deu erro';
        EXCEPTION WHEN OTHERS THEN v_obtido := SQLERRM; END;
        v_res := v_res || jsonb_build_object('n', 18, 'teste', 'Procedure 1: cliente com atraso não empresta',
            'esperado', v_esper, 'obtido', v_obtido, 'ok', v_obtido = v_esper);
 
        CALL sp_gabrielli_registrar_devolucao(v_emp_a, v_livro1);
        SELECT status || ' / multa ' || fn_gabrielli_calcular_multa(v_emp_a)
          INTO v_obtido FROM emprestimos WHERE id_emprestimo = v_emp_a;
        v_res := v_res || jsonb_build_object('n', 19, 'teste', 'Devolução do atrasado: finaliza e mantém a multa',
            'esperado', 'devolvido / multa 6.00', 'obtido', v_obtido, 'ok', v_obtido = 'devolvido / multa 6.00');
 
        -- ---------- Constraints ----------
        BEGIN
            UPDATE emprestimos SET status = 'perdido' WHERE id_emprestimo = v_emp_a;
            v_obtido := 'não deu erro';
        EXCEPTION WHEN check_violation THEN v_obtido := 'check_violation'; END;
        v_res := v_res || jsonb_build_object('n', 20, 'teste', 'CHECK: status inválido',
            'esperado', 'check_violation', 'obtido', v_obtido, 'ok', v_obtido = 'check_violation');
 
        BEGIN
            UPDATE emprestimos SET data_prevista_devolucao = data_emprestimo - 1 WHERE id_emprestimo = v_emp_a;
            v_obtido := 'não deu erro';
        EXCEPTION WHEN check_violation THEN v_obtido := 'check_violation'; END;
        v_res := v_res || jsonb_build_object('n', 21, 'teste', 'CHECK: devolução prevista antes do empréstimo',
            'esperado', 'check_violation', 'obtido', v_obtido, 'ok', v_obtido = 'check_violation');
 
        BEGIN
            UPDATE livros SET quantidade_exemplar = -1 WHERE id_livro = v_livro1;
            v_obtido := 'não deu erro';
        EXCEPTION WHEN check_violation THEN v_obtido := 'check_violation'; END;
        v_res := v_res || jsonb_build_object('n', 22, 'teste', 'CHECK: quantidade de exemplares negativa',
            'esperado', 'check_violation', 'obtido', v_obtido, 'ok', v_obtido = 'check_violation');
 
        -- Desfaz tudo o que foi feito acima
        RAISE EXCEPTION 'fim dos testes' USING ERRCODE = 'XT999';
    EXCEPTION
        WHEN SQLSTATE 'XT999' THEN
            NULL;
    END;
 
    INSERT INTO resultado_testes (n, teste, esperado, obtido, ok)
    SELECT (r->>'n')::INTEGER, r->>'teste', r->>'esperado', r->>'obtido', (r->>'ok')::BOOLEAN
      FROM jsonb_array_elements(v_res) AS r;
END;
$testes$;
 
SELECT n, teste, esperado, obtido, ok
  FROM resultado_testes
 ORDER BY n;