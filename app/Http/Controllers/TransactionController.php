<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Carbon\Carbon;

use App\Models\Transaction;
use App\Models\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class TransactionController extends Controller
{

    public function index(Request $request)
{
    $userId = auth()->id();
    $meiId = session('mei_id');

    $query = Transaction::with('category')
        ->where('user_id', $userId)
        ->where('mei_id', $meiId);

    // FILTROS
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    if ($request->filled('tipo')) {
        $query->where('type', $request->tipo);
    }

    if ($request->filled('date_start') && $request->filled('date_end')) {
        $query->whereBetween('transaction_date', [
            $request->date_start,
            $request->date_end
        ]);
    }

    if ($request->filled('descricao')) {
        $query->where('description', 'like', "%{$request->descricao}%");
    }

    $lancamentos = $query
        ->orderBy('transaction_date', 'desc')
        ->paginate(10)
        ->withQueryString() // ← mantém filtros ao trocar de página
        ->through(function ($lancamento) {
            return [
                'id' => $lancamento->id,
                'category' => $lancamento->category->name ?? 'Sem categoria',
                'date' => Carbon::parse($lancamento->transaction_date)->format('d/m/Y'),
                'valor' => $lancamento->amount,
                'tipo' => $lancamento->type == 1 ? 'Despesa' : 'Receita',
                'descricao' => $lancamento->description,
                'observation' => $lancamento->observation,
            ];
        });

    $categorias = Category::where(function ($query) use ($userId, $meiId) {
        $query->where('user_id', $userId)
            ->where('mei_id', $meiId);
    })
    ->orWhere(function ($query) {
        $query->whereNull('user_id')
            ->whereNull('mei_id');
    })
    ->orderBy('name')
    ->get();

    return Inertia::render('Financeiro', [
        'data' => $lancamentos,
        'categories' => $categorias,
        'filters' => $request->only([
            'category_id',
            'tipo',
            'date_start',
            'date_end',
            'descricao'
        ])
    ]);
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:1,2',
            'description' => 'nullable|string|max:255',
            'observation' => 'nullable|string',
        ]);

        $transaction = Transaction::create([
            'user_id' => auth()->id(), // pega o ID do usuário logado
            'mei_id' => session('mei_id'),
            'category_id' => $validated['category_id'] ?? null,
            'transaction_date' => $validated['transaction_date'],
            'amount' => $validated['amount'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'observation' => $validated['observation'] ?? null,
        ]);

        return Inertia::location(route('financeiro.index'));

    }

    public function edit($id)
    {

        $launch = Transaction::findOrFail($id);

        return response()->json($launch);
    }

    public function update(Request $request, $id)
    {
        $launch = Transaction::findOrFail($id);

        // Valide os dados recebidos (opcional, mas recomendado)
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric',
            'type' => 'required|integer',
            'description' => 'nullable|string|max:255',
            'observation' => 'nullable|string',
        ]);

        // Atualiza o registro com os dados validados
        $launch->update($validated);

        // Redireciona para a rota, você pode usar redirect() também
        return Inertia::location(route('financeiro.index'));
    }


    public function destroy($id)
    {
        $launch = Transaction::findOrFail($id);

        $userId = auth()->id();
        $meiId = session('mei_id');

        if ($launch->mei_id !== $meiId) {
            abort(403, 'Acesso negado: lançamento não pertence ao seu MEI.');
        }

        if ($launch->user_id !== $userId) {
            abort(403, 'Acesso negado: você não cadastrou esse lançamento.');
        }

        $launch->delete();

        return Inertia::location(route('financeiro.index'));

    }

}
