<label for="titulo">Título</label>

<input type="text" id="titulo" name="titulo" value="{{ old('titulo', $livro->titulo ?? '') }}" maxlength="150"
    required>

<label for="isbn">ISBN (13 dígitos)</label>

<input type="text" id="isbn" name="isbn" value="{{ old('isbn', $livro->isbn ?? '') }}" maxlength="13" required>

<div class="linha">
    <div>
        <label for="ano_publicacao">Ano de publicação</label>
        <input type="number" id="ano_publicacao" name="ano_publicacao"
            value="{{ old('ano_publicacao', $livro->ano_publicacao ?? '') }}" required>
    </div>
    <div>
        <label for="quantidade_exemplar">Exemplares</label>
        <input type="number" id="quantidade_exemplar" name="quantidade_exemplar" min="0"
            value="{{ old('quantidade_exemplar', $livro->quantidade_exemplar ?? 1) }}" required>
    </div>
</div>

<label for="autor_id">Autor</label>
<select id="autor_id" name="autor_id" required>
    <option value="">Selecione</option>
    @foreach ($autores as $autor)
        <option value="{{ $autor->id_autor }}" @selected(old('autor_id', $livro->autor_id ?? null) == $autor->id_autor)>
            {{ $autor->nome }}
        </option>
    @endforeach
</select>

<label for="editora_id">Editora</label>
<select id="editora_id" name="editora_id" required>
    <option value="">Selecione</option>
    @foreach ($editoras as $editora)
        <option value="{{ $editora->id_editora }}" @selected(old('editora_id', $livro->editora_id ?? null) == $editora->id_editora)>
            {{ $editora->nome }}
        </option>
    @endforeach
</select>

<label for="categoria_id">Categoria</label>
<select id="categoria_id" name="categoria_id">
    <option value="">Sem categoria</option>
    @foreach ($categorias as $categoria)
        <option value="{{ $categoria->id_categoria }}" @selected(old('categoria_id', $livro->categoria_id ?? null) == $categoria->id_categoria)>
            {{ $categoria->nome }}
        </option>
    @endforeach
</select>

<div class="botoes">
    <button type="submit" class="botao">Salvar</button>
    <a href="{{ route('livros.index') }}" class="botao
secundario">Cancelar</a>
</div>
