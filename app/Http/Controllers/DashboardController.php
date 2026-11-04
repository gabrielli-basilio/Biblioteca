<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Emprestimo;
use App\Models\Livro;
use App\Services\EmprestimoService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totais = [
            'livros' => Livro::count(),
            'clientes' => Cliente::count(),
            'em_andamento' => Emprestimo::where('status', 'em_andamento')->count(),
            'atrasados' => Emprestimo::where('status', 'atrasado')->count(),
        ];

        $maisEmprestados = DB::table('vw_livros_mais_emprestados')->limit(10)->get();
        $atrasados = DB::table('vw_emprestimos_atrasados')->limit(10)->get();

        return view('dashboard', compact('totais', 'maisEmprestados', 'atrasados'));
    }

    public function atualizarAtrasados(EmprestimoService $service)
    {
        $service->atualizarAtrasados();

        return back()->with('sucesso', 'Status de atraso e relatórios atualizados.');
    }
}
