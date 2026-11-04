CREATE SCHEMA bribriotecaDB;

SET search_path TO bribriotecaDB;

-- Tabelas 	Independentes

CREATE TABLE clientes(
	id_cliente INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	nome VARCHAR(150) NOT NULL,
	CPF CHAR(11) NOT NULL UNIQUE,
	telefone CHAR(11) NOT NULL UNIQUE,
	email VARCHAR(150) NOT NULL UNIQUE,
	CEP CHAR(8) NOT NULL,
	logradouro VARCHAR(150) NOT NULL,
	numero_residencial VARCHAR(6) NOT NULL
);

CREATE TABLE autores(
	id_autor INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	nome VARCHAR(150) NOT NULL
);

CREATE TABLE editoras(
	id_editora INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	nome VARCHAR(150) NOT NULL
);

CREATE TABLE categorias(
	id_categoria INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	nome VARCHAR(150) NOT NULL UNIQUE
);

-- Tabelas com FK

CREATE TABLE livros(
	id_livro INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	titulo VARCHAR(150) NOT NULL,
	ISBN VARCHAR(13) NOT NULL UNIQUE, 
	ano_publicacao INT NOT NULL,
	quantidade_exemplar INT NOT NULL DEFAULT 1,
	
	categoria_id INT, 
	editora_id INT  NOT NULL,
	autor_id INT  NOT NULL,

	-- FK tabela categorias
	FOREIGN KEY (categoria_id)
	REFERENCES categorias(id_categoria)

	ON DELETE SET NULL
	ON UPDATE CASCADE,

	-- FK tabela editoras
	FOREIGN KEY (editora_id)
	REFERENCES editoras(id_editora)

	ON DELETE RESTRICT
	ON UPDATE CASCADE,

	-- FK tabela autores 
	FOREIGN KEY (autor_id) 
	REFERENCES autores(id_autor) 
	
	ON DELETE RESTRICT 
	ON UPDATE CASCADE
);

CREATE TABLE emprestimos(
	id_emprestimo INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	data_emprestimo DATE NOT NULL,
	data_prevista_devolucao DATE NOT NULL,
	status VARCHAR(50) NOT NULL DEFAULT 'em_andamento',
	
	cliente_id INT  NOT NULL,

	-- FK tabela clientes
	FOREIGN KEY (cliente_id)
	REFERENCES clientes(id_cliente)

	ON DELETE RESTRICT
	ON UPDATE CASCADE
);
 
-- Tabela Associativa

CREATE TABLE itens_emprestimos(
	emprestimo_id INT NOT NULL,
	livro_id INT NOT NULL,
	data_devolucao DATE,

	PRIMARY KEY (emprestimo_id, livro_id),

	-- FK tabela emprestimos
	FOREIGN KEY (emprestimo_id)
	REFERENCES emprestimos(id_emprestimo)
	
	ON DELETE CASCADE
	ON UPDATE CASCADE,

	-- FK tabela livros
	FOREIGN KEY (livro_id)
	REFERENCES livros(id_livro)
	
	ON DELETE RESTRICT
	ON UPDATE CASCADE
);