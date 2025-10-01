<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories;

class CategoriesController extends Controller
{
    public function store(Request $request)
    {
        // Validação com unique ignorando o registro atual
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'A identificação para categorias é obrigatório.'
        ]);

        // Busca e atualiza apenas o cliente específico
        try {
            $categories = Categories::create([
                'user_id' => auth()->id(),
                'mei_id' => session('mei_id'),
                'name' => $validated['name'],
            ]);

            return response()->json([
        'success' => true,
        'newCategory' => $categories
    ]);
        } catch (\Exception $e) {
            // Retorna para a página atual com mensagem de erro
            return redirect()->back()->with('error', 'Erro ao criar categoria: ' . $e->getMessage());
        }
    }
}
