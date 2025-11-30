<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

use App\Models\Charge;
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
            'filters' => $request->only(['name', 'cpf_cnpj', 'email'])
        ]);
    }

    public function store(Request $request)
    {
        try {

            $request->merge([
                'number' => $request->number !== null ? (string) $request->number : null,
            ]);

            // Recupera o mei_id da sessão
            $meiId = session('mei_id');

            if (!$meiId) {
                return redirect()->back()->withErrors(['mei_id' => 'Perfil do MEI não encontrada.']);
            }

            $validatedData = $request->validate([
                'cpf_cnpj' => [
                    'required',
                    Rule::unique('clients')->where(fn($q) => $q->where('mei_id', $meiId)),
                ],
                'name' => 'required|string|max:255',
                'email' => [
                    'nullable',
                    'email',
                    Rule::unique('clients')->where(fn($q) => $q->where('mei_id', $meiId)),
                ],
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
                'cpf_cnpj.required' => 'O CPF/CNPJ é obrigatório.',
                'cpf_cnpj.unique' => 'Este CPF/CNPJ já está cadastrado.',

                'email.email' => 'Digite um e-mail válido.',
                'email.unique' => 'Este e-mail já está cadastrado.',

                'name.required' => 'A identificação da pessoa física/jurídica é obrigatória.',
            ]);


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


            return redirect()->back()->with('success', 'Cliente salvo com sucesso!');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao salvar o cliente: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->merge([
                'number' => $request->number !== null ? (string) $request->number : null,
            ]);

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

            return redirect()->back()->with('success', 'Cliente atualizado com sucesso!');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao atualizar dados cadastrais do cliente: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $client = Client::findOrFail($id);

            $hasCharge = Charge::where('client_id', $client->id)->exists();

            if ($hasCharge) {
                $message = "Não é possível excluir o cliente <b>{$client->name}</b> porque existem cobranças associadas a ele.";

                return back()->withErrors([
                    'client_delete' => $message
                ]);
            }

            $userId = auth()->id();
            $meiId = session('mei_id');

            // Verificações de segurança
            if ($client->mei_id !== $meiId) {
                abort(403, 'Acesso negado: cliente não pertence ao seu MEI.');
            }

            if ($client->user_id !== $userId) {
                abort(403, 'Acesso negado: você não cadastrou esse cliente.');
            }

            // Exclusão
            $client->delete();

            return redirect()->back()->with('success', 'Cliente excluido com sucesso!');

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao excluir cliente: ' . $e->getMessage());
        }
    }

}
