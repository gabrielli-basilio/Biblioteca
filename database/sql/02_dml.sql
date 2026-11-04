SET search_path TO bribriotecaDB;

-- Limpeza total das tabelas respeitando as FKs do DDL
TRUNCATE TABLE itens_emprestimos, emprestimos, livros, clientes, autores, editoras, categorias RESTART IDENTITY CASCADE;

DO $$
DECLARE
    -- 30 Categorias Reais (gêneros literários existentes)
    categorias_reais TEXT[] := ARRAY[
        'Romance', 'Ficção Científica', 'Fantasia', 'Terror', 'Suspense', 'Biografia', 'História', 'Filosofia',
        'Autoajuda', 'Poesia', 'Psicologia', 'Religião', 'Aventura', 'Infantil', 'Culinária', 'Arte',
        'Tecnologia', 'Economia', 'Política', 'Sociologia', 'Direito', 'Medicina', 'Engenharia', 'Educação',
        'Matemática', 'Física', 'Quadrinhos', 'Contos', 'Crônicas', 'Ficção Policial'
    ];

    -- ~110 Editoras reais (nacionais e internacionais) -> usaremos as 100 primeiras
    editoras_reais TEXT[] := ARRAY[
        'Companhia das Letras','Intrínseca','Sextante','Rocco','Record','Aleph','DarkSide Books','Arqueiro',
        'Nova Fronteira','Zahar','Globo Livros','Editora 34','Suma de Letras','Leya Brasil','LPM Editores',
        'Martins Fontes','Autêntica Editora','Vozes','Cortez Editora','Ática','Saraiva Educação','Moderna',
        'FTD Educação','Objetiva','Planeta','Bertrand Brasil','Best Seller','Novo Século','Fundamento',
        'Melhoramentos','Callis Editora','Panda Books','Salamandra','Galera Record','Verus Editora',
        'Draco Editora','WMF Martins Fontes','Vestígio Editora','Todavia','Dublinense','Relicário Edições',
        'Editora Fósforo','Tinta-da-China Brasil','Boitempo Editorial','Expressão Popular','Perspectiva',
        'Estação Liberdade','Iluminuras','Companhia Editora Nacional','Ediouro','Nova Cultural',
        'Círculo do Livro','Scipione','IBEP','Editora Positivo','Edelbra','Ciranda Cultural',
        'Editora Girassol','Paulinas Editora','Paulus Editora','Edições Loyola','Mundo Cristão',
        'Thomas Nelson Brasil','Bertrand Editora','Grua Livros','Alta Books','Casa do Código',
        'Novatec Editora','Ciência Moderna','Ediouro Publicações','Editora Gente','Editora Vozes',
        'Editora Vestibular1','Penguin Random House','HarperCollins','Simon & Schuster','Macmillan Publishers',
        'Hachette Book Group','Scholastic','Bloomsbury Publishing','Vintage Books','Alfred A. Knopf',
        'Doubleday','Little, Brown and Company','Faber & Faber','Oxford University Press',
        'Cambridge University Press','Wiley','Pearson Education','McGraw-Hill Education','Springer',
        'Elsevier','O''Reilly Media','Grove Press','Picador','Riverhead Books','Tor Books','Orbit Books',
        'Viz Media','Kodansha','Shueisha','Gallimard','Éditions du Seuil','Flammarion','Actes Sud',
        'Suhrkamp Verlag','Mondadori','Feltrinelli','Einaudi','Alfaguara','Anagrama','Tusquets Editores'
    ];

    -- Autores reais e consagrados (extraídos das obras reais cadastradas abaixo)
    autores_reais TEXT[] := ARRAY[
        'Machado de Assis','João Guimarães Rosa','Graciliano Ramos','Jorge Amado','Clarice Lispector',
        'Aluísio Azevedo','José de Alencar','Itamar Vieira Junior','José Saramago','Gabriel García Márquez',
        'Miguel de Cervantes','Jane Austen','Liev Tolstói','Fiódor Dostoiévski','J.D. Salinger','James Joyce',
        'Gustave Flaubert','Emily Brontë','Charlotte Brontë','George Orwell','Aldous Huxley','Ray Bradbury',
        'Frank Herbert','Isaac Asimov','William Gibson','Douglas Adams','H.G. Wells','Stanislaw Lem',
        'Margaret Atwood','J.R.R. Tolkien','J.K. Rowling','C.S. Lewis','George R.R. Martin','Patrick Rothfuss',
        'Christopher Paolini','Rick Riordan','Stephen King','Bram Stoker','Mary Shelley','H.P. Lovecraft',
        'Robert Louis Stevenson','Dan Brown','Gillian Flynn','Markus Zusak','Stieg Larsson','Agatha Christie',
        'Walter Isaacson','Truman Capote','Anne Frank','Nelson Mandela','Yuval Noah Harari','Laurentino Gomes',
        'Lilia Schwarcz','Sérgio Buarque de Holanda','Jostein Gaarder','Friedrich Nietzsche','Platão',
        'Marco Aurélio','Nicolau Maquiavel','Dale Carnegie','Charles Duhigg','Robert Kiyosaki','Carol Dweck',
        'James Hunter','Carlos Drummond de Andrade','Vinicius de Moraes','Manuel Bandeira','Walt Whitman',
        'Charles Baudelaire','Viktor Frankl','Daniel Kahneman','Sigmund Freud','Daniel Goleman','John Bunyan',
        'Santo Agostinho','Tomás de Kempis','Daniel Defoe','Mark Twain','Júlio Verne','Antoine de Saint-Exupéry',
        'Lewis Carroll','Carlo Collodi','José Mauro de Vasconcelos','Monteiro Lobato','Irmãos Grimm',
        'Julia Child','Rita Lobo','E.H. Gombrich','Irving Stone','Robert C. Martin','Andrew Hunt',
        'Thomas Cormen','Robert Lafore','Adam Smith','Karl Marx','Steven Levitt','Henry Hazlitt',
        'Alexis de Tocqueville','Thomas Hobbes','Émile Durkheim','Max Weber','Gilberto Freyre','Hans Kelsen',
        'Vicente Paulo','Henry Gray','Anthony Smith','Ferdinand Beer','Frank White','Paulo Freire',
        'Euclides','Elon Lages Lima','Stephen Hawking','Paul Hewitt','Alan Moore','Mauricio de Sousa',
        'Art Spiegelman','Rubem Braga','Fernando Sabino','Arthur Conan Doyle','Umberto Eco'
    ];

    -- Obras reais e consagradas (título / autor / categoria) - arrays paralelos
    livros_reais_titulo TEXT[] := ARRAY[
        'Dom Casmurro','Memórias Póstumas de Brás Cubas','O Alienista','Grande Sertão: Veredas','Vidas Secas',
        'Capitães da Areia','Gabriela, Cravo e Canela','A Hora da Estrela','A Paixão Segundo G.H.',
        'Laços de Família','O Cortiço','Iracema','Senhora','Torto Arado','Ensaio sobre a Cegueira',
        'Cem Anos de Solidão','O Amor nos Tempos do Cólera','Dom Quixote','Orgulho e Preconceito',
        'Anna Karenina','Guerra e Paz','Crime e Castigo','Os Irmãos Karamázov','O Apanhador no Campo de Centeio',
        'Ulysses','Madame Bovary','O Morro dos Ventos Uivantes','Jane Eyre','1984','A Revolução dos Bichos',
        'Admirável Mundo Novo','Fahrenheit 451','Duna','Fundação','Eu, Robô','Neuromancer',
        'O Guia do Mochileiro das Galáxias','A Máquina do Tempo','A Guerra dos Mundos','Solaris',
        'O Conto da Aia','O Senhor dos Anéis: A Sociedade do Anel','O Hobbit','Harry Potter e a Pedra Filosofal',
        'As Crônicas de Nárnia: O Leão, a Feiticeira e o Guarda-Roupa','A Guerra dos Tronos','O Nome do Vento',
        'Eragon','Percy Jackson e o Ladrão de Raios','It: A Coisa','O Iluminado','Drácula','Frankenstein',
        'O Chamado de Cthulhu','O Médico e o Monstro','O Código Da Vinci','Garota Exemplar',
        'A Menina que Roubava Livros','Os Homens que Não Amavam as Mulheres','E Não Sobrou Nenhum',
        'Assassinato no Expresso Oriente','O Assassinato de Roger Ackroyd','Steve Jobs',
        'Einstein: Sua Vida, Seu Universo','A Sangue Frio','Diário de Anne Frank','Long Walk to Freedom',
        'Sapiens: Uma Breve História da Humanidade','Homo Deus','1808','Brasil: Uma Biografia',
        'Raízes do Brasil','O Mundo de Sofia','Assim Falou Zaratustra','A República','Meditações','O Príncipe',
        'Como Fazer Amigos e Influenciar Pessoas','O Poder do Hábito','Pai Rico, Pai Pobre','Mindset',
        'O Monge e o Executivo','Antologia Poética','Poemas','Cinquenta Poemas Escolhidos pelo Autor',
        'Folhas de Relva','Flores do Mal','O Homem em Busca de Sentido','Pensando Rápido e Devagar',
        'A Interpretação dos Sonhos','Inteligência Emocional','O Peregrino','As Confissões',
        'A Imitação de Cristo','Robinson Crusoé','A Ilha do Tesouro','As Aventuras de Tom Sawyer',
        'Vinte Mil Léguas Submarinas','A Volta ao Mundo em 80 Dias','O Pequeno Príncipe',
        'Alice no País das Maravilhas','Pinóquio','Meu Pé de Laranja Lima','Reinações de Narizinho',
        'Contos de Grimm','Dominando a Arte da Culinária Francesa','Cozinha Fácil','A História da Arte',
        'Vida e Obra de Vincent van Gogh','Clean Code','O Programador Pragmático','Introdução aos Algoritmos',
        'Estruturas de Dados e Algoritmos','A Riqueza das Nações','O Capital','Freakonomics',
        'Economia em Uma Única Lição','A Democracia na América','O Leviatã','As Regras do Método Sociológico',
        'A Ética Protestante e o Espírito do Capitalismo','Casa Grande & Senzala','Teoria Pura do Direito',
        'Direito Constitucional Descomplicado','Anatomia Humana Básica','O Corpo Humano',
        'Resistência dos Materiais','Mecânica dos Fluidos','Pedagogia do Oprimido','Pedagogia da Autonomia',
        'Os Elementos','Curso de Análise','Uma Breve História do Tempo','O Universo Numa Casca de Noz',
        'Física Conceitual','Watchmen','V de Vingança','Turma da Mônica: Laços','Maus',
        'Primeiras Estórias','A Vida Passada a Limpo','As Melhores Crônicas de Rubem Braga',
        'O Cão dos Baskervilles','Um Estudo em Vermelho','O Nome da Rosa'
    ];
    livros_reais_autor TEXT[] := ARRAY[
        'Machado de Assis','Machado de Assis','Machado de Assis','João Guimarães Rosa','Graciliano Ramos',
        'Jorge Amado','Jorge Amado','Clarice Lispector','Clarice Lispector',
        'Clarice Lispector','Aluísio Azevedo','José de Alencar','José de Alencar','Itamar Vieira Junior','José Saramago',
        'Gabriel García Márquez','Gabriel García Márquez','Miguel de Cervantes','Jane Austen',
        'Liev Tolstói','Liev Tolstói','Fiódor Dostoiévski','Fiódor Dostoiévski','J.D. Salinger',
        'James Joyce','Gustave Flaubert','Emily Brontë','Charlotte Brontë','George Orwell','George Orwell',
        'Aldous Huxley','Ray Bradbury','Frank Herbert','Isaac Asimov','Isaac Asimov','William Gibson',
        'Douglas Adams','H.G. Wells','H.G. Wells','Stanislaw Lem',
        'Margaret Atwood','J.R.R. Tolkien','J.R.R. Tolkien','J.K. Rowling',
        'C.S. Lewis','George R.R. Martin','Patrick Rothfuss',
        'Christopher Paolini','Rick Riordan','Stephen King','Stephen King','Bram Stoker','Mary Shelley',
        'H.P. Lovecraft','Robert Louis Stevenson','Dan Brown','Gillian Flynn',
        'Markus Zusak','Stieg Larsson','Agatha Christie',
        'Agatha Christie','Agatha Christie','Walter Isaacson',
        'Walter Isaacson','Truman Capote','Anne Frank','Nelson Mandela',
        'Yuval Noah Harari','Yuval Noah Harari','Laurentino Gomes','Lilia Schwarcz',
        'Sérgio Buarque de Holanda','Jostein Gaarder','Friedrich Nietzsche','Platão','Marco Aurélio','Nicolau Maquiavel',
        'Dale Carnegie','Charles Duhigg','Robert Kiyosaki','Carol Dweck',
        'James Hunter','Carlos Drummond de Andrade','Vinicius de Moraes','Manuel Bandeira',
        'Walt Whitman','Charles Baudelaire','Viktor Frankl','Daniel Kahneman',
        'Sigmund Freud','Daniel Goleman','John Bunyan','Santo Agostinho',
        'Tomás de Kempis','Daniel Defoe','Robert Louis Stevenson','Mark Twain',
        'Júlio Verne','Júlio Verne','Antoine de Saint-Exupéry',
        'Lewis Carroll','Carlo Collodi','José Mauro de Vasconcelos','Monteiro Lobato',
        'Irmãos Grimm','Julia Child','Rita Lobo','E.H. Gombrich',
        'Irving Stone','Robert C. Martin','Andrew Hunt','Thomas Cormen',
        'Robert Lafore','Adam Smith','Karl Marx','Steven Levitt',
        'Henry Hazlitt','Alexis de Tocqueville','Thomas Hobbes','Émile Durkheim',
        'Max Weber','Gilberto Freyre','Hans Kelsen',
        'Vicente Paulo','Henry Gray','Anthony Smith',
        'Ferdinand Beer','Frank White','Paulo Freire','Paulo Freire',
        'Euclides','Elon Lages Lima','Stephen Hawking','Stephen Hawking',
        'Paul Hewitt','Alan Moore','Alan Moore','Mauricio de Sousa','Art Spiegelman',
        'João Guimarães Rosa','Fernando Sabino','Rubem Braga',
        'Arthur Conan Doyle','Arthur Conan Doyle','Umberto Eco'
    ];
    livros_reais_categoria TEXT[] := ARRAY[
        'Romance','Romance','Contos','Romance','Romance',
        'Romance','Romance','Romance','Romance',
        'Contos','Romance','Romance','Romance','Romance','Romance',
        'Romance','Romance','Romance','Romance',
        'Romance','Romance','Romance','Romance','Romance',
        'Romance','Romance','Romance','Romance','Ficção Científica','Ficção Científica',
        'Ficção Científica','Ficção Científica','Ficção Científica','Ficção Científica','Ficção Científica','Ficção Científica',
        'Ficção Científica','Ficção Científica','Ficção Científica','Ficção Científica',
        'Ficção Científica','Fantasia','Fantasia','Fantasia',
        'Fantasia','Fantasia','Fantasia',
        'Fantasia','Fantasia','Terror','Terror','Terror','Terror',
        'Terror','Terror','Suspense','Suspense',
        'Suspense','Suspense','Ficção Policial',
        'Ficção Policial','Ficção Policial','Biografia',
        'Biografia','Biografia','Biografia','Biografia',
        'História','História','História','História',
        'História','Filosofia','Filosofia','Filosofia','Filosofia','Filosofia',
        'Autoajuda','Autoajuda','Autoajuda','Autoajuda',
        'Autoajuda','Poesia','Poesia','Poesia',
        'Poesia','Poesia','Psicologia','Psicologia',
        'Psicologia','Psicologia','Religião','Religião',
        'Religião','Aventura','Aventura','Aventura',
        'Aventura','Aventura','Infantil',
        'Infantil','Infantil','Infantil','Infantil',
        'Infantil','Culinária','Culinária','Arte',
        'Arte','Tecnologia','Tecnologia','Tecnologia',
        'Tecnologia','Economia','Economia','Economia',
        'Economia','Política','Política','Sociologia',
        'Sociologia','Sociologia','Direito',
        'Direito','Medicina','Medicina',
        'Engenharia','Engenharia','Educação','Educação',
        'Matemática','Matemática','Física','Física',
        'Física','Quadrinhos','Quadrinhos','Quadrinhos','Quadrinhos',
        'Contos','Crônicas','Crônicas',
        'Ficção Policial','Ficção Policial','Ficção Policial'
    ];

    -- Matrizes de nomes reais brasileiros p/ completar clientes e o restante dos autores até as quantidades pedidas
    p_nomes TEXT[] := ARRAY['Gabriel','Lucas','Beatriz','Mariana','Matheus','Guilherme','Fernanda','Camila','Rodrigo','Larissa',
        'Felipe','Amanda','Vinicius','Isabela','Bruno','Letícia','Rafael','Luana','Gustavo','Jéssica',
        'Diego','Vanessa','Thiago','Aline','Leonardo','Bruna','João','Maria','Pedro','Ana',
        'Caio','Lívia','Eduardo','Sofia','Marcelo','Otávio','Renata','Patrícia','Carlos','Juliana',
        'Sérgio','Débora','Ricardo','Cristiane','Fábio','Simone','Alexandre','Priscila','Daniel','Tatiane'];
    sobres TEXT[] := ARRAY['Silva','Santos','Oliveira','Souza','Rodrigues','Ferreira','Alves','Pereira','Lima','Gomes',
        'Costa','Ribeiro','Martins','Carvalho','Almeida','Lopes','Soares','Fernandes','Vieira','Barbosa',
        'Rocha','Dias','Nascimento','Andrade','Moreira','Nunes','Marques','Machado','Mendes','Freitas',
        'Cardoso','Ramos','Gonçalves','Santana','Teixeira','Correia','Pinto','Cavalcante','Monteiro','Miranda',
        'Batista','Araújo','Melo','Castro','Campos','Duarte','Farias','Guimarães','Peixoto','Siqueira'];

    -- Estruturas para geração do restante dos títulos de livros (títulos plausíveis, não atribuídos como obra real)
    pref_livros TEXT[] := ARRAY['O Mistério de','A História de','O Segredo de','Fundamentos de','Princípios de','Manual de',
        'Análise sobre','O Legado de','Crônicas de','O Império de','A Lenda de','O Guia de','Tratado de',
        'A Teoria de','Caminhos de','Ecos de','Sombras de','A Jornada de','Reflexões sobre','Elementos de',
        'O Enigma de','A Face de','O Rastro de','Vozes de','O Chamado de','A Trilha de','O Círculo de',
        'Fragmentos de','A Travessia de','O Despertar de'];
    subf_livros TEXT[] := ARRAY['Sombra','Fogo','Tempo','Espaço','Alma','Mundo','Vida','Verdade','Justiça','Poder',
        'Sabedoria','Destino','Luz','Vento','Terra','Mar','Noite','Sol','Mente','Universo',
        'Memória','Silêncio','Liberdade','Esperança','Origem','Caos','Renascimento','Eternidade',
        'Coragem','Redenção','Solidão','Inocência','Glória'];

    -- Complementos para variar os títulos gerados (subtítulo, adjetivo ou volume), sem sobrenome colado no final
    adjetivos_livro TEXT[] := ARRAY['Eterna','Perdida','Oculta','Renascida','Infinita','Silenciosa','Distante',
        'Sagrada','Selvagem','Esquecida','Sombria','Radiante','Suprema','Ancestral','Fragmentada'];
    subtitulos_livro TEXT[] := ARRAY[': Uma Jornada Além do Tempo',': A Última Fronteira',': Origens',': O Despertar',
        ': Crônicas Perdidas',': Além do Horizonte',': A Travessia Final',': Novos Caminhos',': O Outro Lado'];

    -- Ruas reais de Guarapuava/PR (mesma região do CEP usado)
    ruas TEXT[] := ARRAY['Rua XV de Novembro','Rua Vicente Machado','Rua Padre Chagas','Rua Saldanha Marinho',
        'Rua Capitão Rocha','Avenida Manoel Ribas','Rua Guaíra','Rua Barão do Rio Branco',
        'Rua Marechal Deodoro','Rua Emílio Gomes','Rua Brasílio Ávila'];

    arr_cli INT[]; arr_liv INT[]; arr_liv_emprestaveis INT[]; arr_emp INT[]; arr_edi INT[]; arr_aut INT[]; arr_cat INT[];
    i INT; j INT; k INT; c_count INT; v_num_itens INT;
    v_nome TEXT; v_titulo TEXT; v_isbn TEXT; v_isbn_prefixo TEXT; v_isbn_soma INT; v_isbn_dv INT; v_variante NUMERIC;
    data_emp DATE; data_prev DATE; status_emp VARCHAR(50);

    -- CPF Módulo 11 (dígitos sorteados, sem sequência/repetição) + Telefone realista (DDD 42 - Guarapuava/PR)
    v_cpf TEXT; v_soma INT; v_resto INT; v_d1 INT; v_d2 INT; v_b INT[]; v_tel TEXT; v_tel_corpo TEXT;
    v_repetido BOOLEAN; v_sequencial BOOLEAN;
BEGIN

    -- 1. Categorias (Exatamente 30, nomes de gêneros reais)
    FOR i IN 1..30 LOOP
        INSERT INTO categorias (nome) VALUES (categorias_reais[i]);
    END LOOP;
    SELECT array_agg(id_categoria) INTO arr_cat FROM categorias;

    -- 2. Editoras (100 editoras reais, nacionais e internacionais)
    FOR i IN 1..100 LOOP
        INSERT INTO editoras (nome) VALUES (editoras_reais[i]) ON CONFLICT DO NOTHING;
    END LOOP;
    SELECT array_agg(id_editora) INTO arr_edi FROM editoras;

    -- 3. Autores: primeiro os autores reais e consagrados usados nas obras abaixo
    FOR i IN 1..array_length(autores_reais, 1) LOOP
        INSERT INTO autores (nome) VALUES (autores_reais[i]) ON CONFLICT DO NOTHING;
    END LOOP;

    -- Completa até 1.300 autores com nomes reais de pessoas (combinações de nomes/sobrenomes brasileiros comuns)
    SELECT count(*) INTO c_count FROM autores;
    WHILE c_count < 1300 LOOP
        v_nome := p_nomes[floor(random() * array_length(p_nomes, 1)) + 1] || ' ' ||
                  sobres[floor(random() * array_length(sobres, 1)) + 1] || ' ' ||
                  sobres[floor(random() * array_length(sobres, 1)) + 1];
        INSERT INTO autores (nome) VALUES (v_nome) ON CONFLICT DO NOTHING;
        SELECT count(*) INTO c_count FROM autores;
    END LOOP;
    SELECT array_agg(id_autor) INTO arr_aut FROM autores;

    -- 4. Clientes (Exatamente 1.000 Únicos, com CPF e telefone realistas, não sequenciais)
    c_count := 0;
    WHILE c_count < 1000 LOOP

        -- Gera 9 dígitos base do CPF rejeitando padrões óbvios (todos iguais ou sequência 0-1-2-3...)
        LOOP
            v_b := ARRAY[
                floor(random() * 9 + 1)::int, floor(random() * 10)::int, floor(random() * 10)::int,
                floor(random() * 10)::int, floor(random() * 10)::int, floor(random() * 10)::int,
                floor(random() * 10)::int, floor(random() * 10)::int, floor(random() * 10)::int
            ];
            v_repetido := (v_b[1]=v_b[2] AND v_b[2]=v_b[3] AND v_b[3]=v_b[4] AND v_b[4]=v_b[5] AND
                           v_b[5]=v_b[6] AND v_b[6]=v_b[7] AND v_b[7]=v_b[8] AND v_b[8]=v_b[9]);
            v_sequencial := (v_b[2]=v_b[1]+1 AND v_b[3]=v_b[2]+1 AND v_b[4]=v_b[3]+1 AND v_b[5]=v_b[4]+1 AND
                             v_b[6]=v_b[5]+1 AND v_b[7]=v_b[6]+1 AND v_b[8]=v_b[7]+1 AND v_b[9]=v_b[8]+1);
            EXIT WHEN NOT (v_repetido OR v_sequencial);
        END LOOP;

        v_soma := v_b[1]*10 + v_b[2]*9 + v_b[3]*8 + v_b[4]*7 + v_b[5]*6 + v_b[6]*5 + v_b[7]*4 + v_b[8]*3 + v_b[9]*2;
        v_resto := (v_soma * 10) % 11;
        IF v_resto = 10 OR v_resto = 11 THEN v_d1 := 0; ELSE v_d1 := v_resto; END IF;

        v_soma := v_b[1]*11 + v_b[2]*10 + v_b[3]*9 + v_b[4]*8 + v_b[5]*7 + v_b[6]*6 + v_b[7]*5 + v_b[8]*4 + v_b[9]*3 + v_d1*2;
        v_resto := (v_soma * 10) % 11;
        IF v_resto = 10 OR v_resto = 11 THEN v_d2 := 0; ELSE v_d2 := v_resto; END IF;

        v_cpf := v_b[1]::text || v_b[2]::text || v_b[3]::text || v_b[4]::text || v_b[5]::text ||
                 v_b[6]::text || v_b[7]::text || v_b[8]::text || v_b[9]::text || v_d1::text || v_d2::text;

        -- Telefone celular real (DDD 42 = Guarapuava/PR) rejeitando dígitos repetidos ou sequenciais no corpo do número
        LOOP
            v_tel_corpo := lpad(floor(random() * 99999999)::text, 8, '0');
            EXIT WHEN v_tel_corpo !~ '(\d)\1{5,}' AND v_tel_corpo NOT IN ('01234567','12345678','23456789','87654321','98765432');
        END LOOP;
        v_tel := '429' || v_tel_corpo;

        v_nome := p_nomes[floor(random() * array_length(p_nomes, 1)) + 1] || ' ' ||
                  sobres[floor(random() * array_length(sobres, 1)) + 1] || ' ' ||
                  sobres[floor(random() * array_length(sobres, 1)) + 1];

        INSERT INTO clientes (nome, CPF, telefone, email, CEP, logradouro, numero_residencial)
        VALUES (
            v_nome, v_cpf, v_tel,
            lower(replace(v_nome, ' ', '.')) || (c_count + 1) || '@email.com',
            '85010000', ruas[floor(random() * array_length(ruas, 1)) + 1], (floor(random() * 3000) + 1)::text
        ) ON CONFLICT DO NOTHING;

        SELECT count(*) INTO c_count FROM clientes;
    END LOOP;
    SELECT array_agg(id_cliente) INTO arr_cli FROM clientes;

    -- 5. Livros: primeiro as obras reais e consagradas (título, autor e categoria corretos)
    FOR i IN 1..array_length(livros_reais_titulo, 1) LOOP
        v_isbn_prefixo := '97885' || lpad(i::text, 7, '0');
        v_isbn_soma := 0;
        FOR k IN 1..12 LOOP
            v_isbn_soma := v_isbn_soma + (substr(v_isbn_prefixo, k, 1)::int) * (CASE WHEN k % 2 = 0 THEN 3 ELSE 1 END);
        END LOOP;
        v_isbn_dv := (10 - (v_isbn_soma % 10)) % 10;
        v_isbn := v_isbn_prefixo || v_isbn_dv::text;

        INSERT INTO livros (titulo, ISBN, ano_publicacao, quantidade_exemplar, categoria_id, editora_id, autor_id)
        VALUES (
            livros_reais_titulo[i], v_isbn,
            floor(random() * (2023 - 1850 + 1) + 1850)::int,
            floor(random() * 5 + 1)::int,
            (SELECT id_categoria FROM categorias WHERE nome = livros_reais_categoria[i]),
            arr_edi[floor(random() * array_length(arr_edi, 1)) + 1],
            (SELECT id_autor FROM autores WHERE nome = livros_reais_autor[i])
        ) ON CONFLICT DO NOTHING;
    END LOOP;

    -- Completa até 3.000 livros com títulos plausíveis (autor/editora/categoria reais, título gerado)
    SELECT count(*) INTO c_count FROM livros;
    WHILE c_count < 3000 LOOP
        v_titulo := pref_livros[floor(random() * array_length(pref_livros, 1)) + 1] || ' ' ||
                    subf_livros[floor(random() * array_length(subf_livros, 1)) + 1];

        -- Varia o título com adjetivo, subtítulo ou volume (ou mantém simples) - sem sobrenome colado no final
        v_variante := random();
        IF v_variante < 0.30 THEN
            v_titulo := v_titulo || ' ' || adjetivos_livro[floor(random() * array_length(adjetivos_livro, 1)) + 1];
        ELSIF v_variante < 0.55 THEN
            v_titulo := v_titulo || subtitulos_livro[floor(random() * array_length(subtitulos_livro, 1)) + 1];
        ELSIF v_variante < 0.70 THEN
            v_titulo := v_titulo || ' - Volume ' || (floor(random() * 5) + 1)::text;
        END IF;

        v_isbn_prefixo := '97885' || lpad((c_count + 1000)::text, 7, '0');
        v_isbn_soma := 0;
        FOR k IN 1..12 LOOP
            v_isbn_soma := v_isbn_soma + (substr(v_isbn_prefixo, k, 1)::int) * (CASE WHEN k % 2 = 0 THEN 3 ELSE 1 END);
        END LOOP;
        v_isbn_dv := (10 - (v_isbn_soma % 10)) % 10;
        v_isbn := v_isbn_prefixo || v_isbn_dv::text;

        INSERT INTO livros (titulo, ISBN, ano_publicacao, quantidade_exemplar, categoria_id, editora_id, autor_id)
        VALUES (
            v_titulo, v_isbn,
            floor(random() * (2024 - 1970 + 1) + 1970)::int,
            floor(random() * 5 + 1)::int,
            arr_cat[floor(random() * array_length(arr_cat, 1)) + 1],
            arr_edi[floor(random() * array_length(arr_edi, 1)) + 1],
            arr_aut[floor(random() * array_length(arr_aut, 1)) + 1]
        ) ON CONFLICT DO NOTHING;

        SELECT count(*) INTO c_count FROM livros;
    END LOOP;
    SELECT array_agg(id_livro) INTO arr_liv FROM livros;
    -- Reserva ~20% dos livros fora do sorteio de empréstimos, garantindo que existam livros nunca emprestados
    arr_liv_emprestaveis := arr_liv[1 : floor(array_length(arr_liv, 1) * 0.8)::int];

    -- 6. Empréstimos (Exatamente 10.000)
    FOR i IN 1..10000 LOOP
        IF random() > 0.8 THEN
            status_emp := 'atrasado'; data_emp := CURRENT_DATE - (floor(random() * 60) + 15)::int; data_prev := data_emp + 7;
        ELSIF random() > 0.3 THEN
            status_emp := 'devolvido'; data_emp := '2023-01-01'::date + (random() * 1000)::int; data_prev := data_emp + 14;
        ELSE
            status_emp := 'em_andamento'; data_emp := CURRENT_DATE - (floor(random() * 10))::int; data_prev := CURRENT_DATE + 14;
        END IF;

        INSERT INTO emprestimos (cliente_id, data_emprestimo, data_prevista_devolucao, status)
        VALUES (arr_cli[floor(random() * array_length(arr_cli, 1)) + 1], data_emp, data_prev, status_emp);
    END LOOP;

    -- 7. Itens dos Empréstimos (~20.000 Itens, quantidade de livros por empréstimo variando de 1 a 4)
    SELECT array_agg(id_emprestimo) INTO arr_emp FROM emprestimos;

    FOR i IN 1..array_length(arr_emp, 1) LOOP
        v_num_itens := floor(random() * 4 + 1)::int; -- entre 1 e 4 livros neste empréstimo
        FOR j IN 1..v_num_itens LOOP
            INSERT INTO itens_emprestimos (emprestimo_id, livro_id, data_devolucao)
            VALUES (arr_emp[i], arr_liv_emprestaveis[floor(random() * array_length(arr_liv_emprestaveis, 1)) + 1], NULL)
            ON CONFLICT (emprestimo_id, livro_id) DO NOTHING;
        END LOOP;
    END LOOP;

    -- Ajustes finais de datas e estoque
    UPDATE itens_emprestimos ie SET data_devolucao = e.data_prevista_devolucao - 2
        FROM emprestimos e WHERE ie.emprestimo_id = e.id_emprestimo AND e.status = 'devolvido';
    UPDATE livros l SET quantidade_exemplar = COALESCE(
        (SELECT COUNT(*) FROM itens_emprestimos ie WHERE ie.livro_id = l.id_livro AND ie.data_devolucao IS NULL), 0
    ) + floor(random() * 8 + 1)::int;

END $$;
