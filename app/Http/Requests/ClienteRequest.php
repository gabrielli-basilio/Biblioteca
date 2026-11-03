<?php

namespace App\Http\Requests;

use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf' => preg_replace('/\D/', '', (string) $this->cpf),
            'telefone' => preg_replace('/\D/', '', (string) $this->telefone),
            'cep' => preg_replace('/\D/', '', (string) $this->cep),
        ]);
    }

    public function rules(): array
    {
        $id = $this->route('cliente')?->id_cliente;

        return [
            'nome' => ['required', 'string', 'max:150'],
            'cpf' => ['required', 'digits:11', new Cpf(), Rule::unique('clientes', 'cpf')->ignore($id, 'id_cliente')],
            'telefone' => ['required', 'digits:11', Rule::unique('clientes', 'telefone')->ignore($id, 'id_cliente')],
            'email' => ['required', 'email', 'max:150', Rule::unique('clientes', 'email')->ignore($id, 'id_cliente')],
            'cep' => ['required', 'digits:8'],
            'logradouro' => ['required', 'string', 'max:150'],
            'numero_residencial' => ['required', 'string', 'max:6'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cpf' => 'CPF',
            'cep' => 'CEP',
            'numero_residencial' => 'número',
        ];
    }
}