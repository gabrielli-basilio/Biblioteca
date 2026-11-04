<label for="nome">Nome</label>
<input type="text" id="nome" name="nome" value="{{ old('nome', $editora->nome ?? '') }}" maxlength="150" required>

<div class="botoes">
    <button type="submit" class="botao">Salvar</button>
    <a href="{{ route('editoras.index') }}" class="botao secundario">Cancelar</a>
</div>