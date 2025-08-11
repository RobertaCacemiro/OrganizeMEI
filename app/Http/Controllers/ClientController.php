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

        // showArray(["filtros" => $request]);

        // exit;

        $query = $filters->apply($query);


        $clients = $query->orderBy('name', 'asc')->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($client) {
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
                ];
            });

        return Inertia::render('Clientes', [
            'data' => $clients,
            'filters' => $request->only(['name', 'cpf_cnpj', 'email']),
        ]);
    }



    public function store(Request $request)
    {
        // showArray(["REQUEST" => $request]);
        // exit;
        // echo "TESTE";
        // exit;
        $validatedData = $request->validate([
            'cpf_cnpj' => 'required|unique:clients,cpf_cnpj',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:clients,email',
            'phone' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:255',
            'complement' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
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

        // return redirect()->back()->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);

        return Inertia::render('clientes.index', [
            'cliente' => $client,
        ]);
    }

    public function show($id)
    {
        $cliente = Client::findOrFail($id);
        // showArray(['TESTE' => $cliente]);
        // exit;
        return response()->json($cliente);
    }


    public function destroy($id)
    {
        $client = Client::findOrFail($id);

        $userId = auth()->id();
        $meiId = session('mei_id');

        // Verifica se o cliente pertence ao MEI do usuário logado
        if ($client->mei_id !== $meiId) {
            abort(403, 'Acesso negado: cliente não pertence ao seu MEI.');
        }

        // Verifica se o cliente foi cadastrado pelo usuário logado
        if ($client->user_id !== $userId) {
            abort(403, 'Acesso negado: você não cadastrou esse cliente.');
        }

        $client->delete();

        return Inertia::location(route('clientes.index'));

    }

}
