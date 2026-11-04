<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmprestimoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_emprestimo,
            'status' => $this->status,
            'data_emprestimo' => $this->data_emprestimo?->toDateString(),
            'data_prevista_devolucao' => $this->data_prevista_devolucao?->toDateString(),
            'total_livros' => $this->whenCounted('livros'),
            'cliente' => $this->whenLoaded('cliente', fn () => [
                'id' => $this->cliente->id_cliente,
                'nome' => $this->cliente->nome,
            ]),
            'livros' => $this->whenLoaded('livros', fn () => $this->livros->map(fn ($livro) => [
                'id' => $livro->id_livro,
                'titulo' => $livro->titulo,
                'data_devolucao' => $livro->pivot->data_devolucao,
            ])),
        ];
    }
}