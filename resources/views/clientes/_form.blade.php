<label for="nome">Nome</label>
<input type="text" id="nome" name="nome" value="{{ old('nome', $cliente->nome ?? '') }}" maxlength="150" required>
<div class="linha">
    <div>
        <label for="cpf">CPF</label>
        <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $cliente->cpf ?? '') }}" maxlength="14"
            required>
    </div>
    <div>
        <label for="telefone">Telefone</label>
        <input type="text" id="telefone" name="telefone" value="{{ old('telefone', $cliente->telefone ?? '') }}"
            maxlength="15" required>
    </div>
</div>
<label for="email">E-mail</label>
<input type="email" id="email" name="email" value="{{ old('email', $cliente->email ?? '') }}" maxlength="150"
    required>
<div class="linha">
    <div>
        <label for="cep">CEP</label>
        <input type="text" id="cep" name="cep" value="{{ old('cep', $cliente->cep ?? '') }}" maxlength="9"
            required>
    </div>
    <div>
        <label for="numero_residencial">Número</label>
        <input type="text" id="numero_residencial" name="numero_residencial"
            value="{{ old('numero_residencial', $cliente->numero_residencial ?? '') }}" maxlength="6" required>
    </div>
</div>
<label for="logradouro">Logradouro</label>
<input type="text" id="logradouro" name="logradouro" value="{{ old('logradouro', $cliente->logradouro ?? '') }}"
    maxlength="150" required>
<div class="botoes">
    <button type="submit" class="botao">Salvar</button>
    <a href="{{ route('clientes.index') }}" class="botao
secundario">Cancelar</a>
</div>
