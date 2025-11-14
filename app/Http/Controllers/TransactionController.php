<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Carbon\Carbon;

use App\Models\Transaction;
use App\Models\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $meiId = session('mei_id');

        if (!$meiId) {
            return redirect()->route('profile-mei.index')->withErrors([
                'mei' => 'Perfil MEI não associado à sessão.',
            ]);
        }

        $query = Transaction::with('category')
            ->where('user_id', $userId)
            ->where('mei_id', $meiId);

        // FILTROS
        if ($request->filled('descricao')) {
            $query->where('description', 'like', "%{$request->descricao}%");
        }

        if ($request->filled('categoria')) {
            $query->where('category_id', $request->categoria);
        }

        if ($request->filled('tipo')) {
            $query->where('type', $request->tipo);
        }

        if ($request->filled('data')) {
            $query->where('transaction_date', $request->data);
        }

        $lancamentos = $query
            ->orderBy('transaction_date', 'desc')
            ->paginate(10)
            ->withQueryString()
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
            ->orderBy('created_at', 'desc')
            ->get();

        $dashboardValues = $this->getDashboardValues();

        return Inertia::render('Financeiro', [
            'data' => $lancamentos,
            'categories' => $categorias,
            'dashboardValues' => $dashboardValues,
            'filters' => $request->all()
        ]);
    }

    public function getDashboardValues()
    {
        $userId = auth()->id();
        $meiId = session('mei_id');

        $query = Transaction::where('user_id', $userId)
            ->where('mei_id', $meiId);

        // Somar receitas e despesas
        $totalReceitas = (clone $query)->where('type', 2)->sum('amount');
        $totalDespesas = (clone $query)->where('type', 1)->sum('amount');

        // Calcular saldo
        $saldo = $totalReceitas - $totalDespesas;

        return [
            'total_receitas' => 'R$ ' . number_format($totalReceitas, 2, ',', '.'),
            'total_despesas' => 'R$ ' . number_format($totalDespesas, 2, ',', '.'),
            'saldo' => 'R$ ' . number_format($saldo, 2, ',', '.'),
        ];
    }

    public function store(Request $request)
    {
        try {
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

            return redirect()->back()->with('success', 'Registro financeiro salvo com sucesso!');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao salvar registro financeiro: ' . $e->getMessage());
        }

    }

    public function update(Request $request, $id)
    {
        try {
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
            // return Inertia::location(route('financeiro.index'));
            return redirect()->back()->with('success', 'Registro financeiro atualizado com sucesso!');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao atualizar o registro financeiro: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $launche = Transaction::findOrFail($id);

            $userId = auth()->id();
            $meiId = session('mei_id');

            if ($launche->mei_id !== $meiId) {
                abort(403, 'Acesso negado: registro não pertence ao seu MEI.');
            }

            if ($launche->user_id !== $userId) {
                abort(403, 'Acesso negado: você não cadastrou essa registro.');
            }

            $launche->delete();

            return redirect()->back()->with('success', 'Registro financeirto excluido com sucesso!');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao excluir o registro financeirto: ' . $e->getMessage());
        }
    }

}
