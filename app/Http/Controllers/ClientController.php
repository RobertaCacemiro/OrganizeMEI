<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;

use App\Filters\ClientFilters;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $meiId = session('mei_id');

        if (!$meiId) {
            return redirect()->route('profile-mei.index')->withErrors([
                'mei' => 'Perfil MEI não associado à sessão.',
            ]);
        }

        $query = Client::where('mei_id', $meiId);
        $filters = new ClientFilters($request);

        $query = $filters->apply($query);

        $clients = $query->orderBy('name', 'asc')
            ->orderBy('created_at', 'asc')
            ->paginate(10) // quantidade de itens por página
            ->through(function ($client) {
                return [
                    'id' => $client->id,
                    'cpf_cnpj' => $client->cpf_cnpj,
                    'name' => $client->name,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'address' => collect([
                        $client->street,
                        $client->number,
                        $client->complement,
                        $client->district,
                        $client->city,
                        $client->state,
                        $client->zip_code,
                    ])->filter()->implode(', '),
                    "street" => $client->street,
                    "number" => $client->number,
                    "complement" => $client->complement,
                    "district" => $client->district,
                    "city" => $client->city,
                    "state" => $client->state,
                    "zip_code" => $client->zip_code,
                    'observacao' => $client->notes
                ];
            });

        return Inertia::render('Clientes', [
            'data' => $clients,
            'filters' => $request->all()
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cpf_cnpj' => 'required|unique:clients,cpf_cnpj',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email',
            'phone' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'number' => 'nullable|int|max:255',
            'complement' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ], [
            'cpf_cnpj.required' => 'O CPF/CNPJ é obrigatório.',
            'cpf_cnpj.unique' => 'Esse CPF/CNPJ já está cadastrado.',
            'name.required' => 'A identificação da pessoa física/juridica é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail está vinculado a outro usuário.',
        ]);

        // Recupera o mei_id da sessão
        $meiId = session('mei_id');

        if (!$meiId) {
            return redirect()->back()->withErrors(['mei_id' => 'Perfil do MEI não encontrada.']);
        }

        // Cria o cliente usando os dados validados
        $client = Client::create([
            'user_id' => auth()->id(),
            'mei_id' => session('mei_id'),
            'cpf_cnpj' => $validatedData['cpf_cnpj'],
            'name' => $validatedData['name'],
            'email' => $validatedData['email'] ?? null,
            'phone' => $validatedData['phone'] ?? null,
            'street' => $validatedData['street'] ?? null,
            'number' => $validatedData['number'] ?? null,
            'complement' => $validatedData['complement'] ?? null,
            'district' => $validatedData['district'] ?? null,
            'city' => $validatedData['city'] ?? null,
            'state' => $validatedData['state'] ?? null,
            'zip_code' => $validatedData['zip_code'] ?? null,
            'notes' => $validatedData['notes'] ?? null,
        ]);

        return Inertia::location(route('clientes.index'));
    }

    public function update(Request $request, $id)
    {
        $userId = auth()->id();
        $meiId = session('mei_id');

        // Validação com unique ignorando o registro atual
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:clients,email,' . $id,
            'phone' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:255',
            'complement' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ], [
            'name.required' => 'A identificação da pessoa física/juridica é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'E-mail já vinculado a outro cliente.',
        ]);


        // Busca e atualiza apenas o cliente específico
        $client = Client::where('id', $id)
            ->where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->firstOrFail();

        $client->update($validated);

        return Inertia::location(route('clientes.index', $request->all()));
    }

    public function destroy(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $userId = auth()->id();
        $meiId = session('mei_id');

        // Verificações de segurança
        if ($client->mei_id !== $meiId) {
            abort(403, 'Acesso negado: cliente não pertence ao seu MEI.');
        }

        if ($client->user_id !== $userId) {
            abort(403, 'Acesso negado: você não cadastrou esse cliente.');
        }

        // Verificação de pagamentos associados
        $hasPayments = \App\Models\Payment::where('client_id', $client->id)->exists();

        if ($hasPayments) {
            return back()->with('error', 'Não é possível excluir este cliente porque existem pagamentos associados.');
        }

        // Exclusão
        $client->delete();

        return Inertia::location(route('clientes.index', $request->all()));
    }

}
