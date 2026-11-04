<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LivroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $livro = $this->route('livro');
        $obrigatorio = $this->isMethod('PATCH') ? 'sometimes' : 'required';

        return [
            'titulo' => [$obrigatorio, 'string', 'max:150'],
            'isbn' => [
                $obrigatorio, 'digits:13',
                Rule::unique('livros', 'isbn')->ignore($livro?->id_livro, 'id_livro'),
            ],
            'ano_publicacao' => [$obrigatorio, 'integer', 'min:1000', 'max:' . (date('Y') + 1)],
            'quantidade_exemplar' => [$obrigatorio, 'integer', 'min:0'],
            'categoria_id' => ['nullable', 'integer', 'exists:categorias,id_categoria'],
            'editora_id' => [$obrigatorio, 'integer', 'exists:editoras,id_editora'],
            'autor_id' => [$obrigatorio, 'integer', 'exists:autores,id_autor'],
        ];
    }

    public function attributes(): array
    {
        return [
            'titulo' => 'título',
            'isbn' => 'ISBN',
            'ano_publicacao' => 'ano de publicação',
            'quantidade_exemplar' => 'quantidade de exemplares',
            'categoria_id' => 'categoria',
            'editora_id' => 'editora',
            'autor_id' => 'autor',
        ];
    }
}
