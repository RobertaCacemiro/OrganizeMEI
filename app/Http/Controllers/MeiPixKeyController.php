<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MeiPixKey;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Exception;

use Inertia\Inertia;

class MeiPixKeyController extends Controller
{
    /**
     * Mapeamento de tipos de chave PIX para exibição amigável.
     */
    protected $typeMapping = [
        'email' => 'E-mail',
        'cpf' => 'CPF',
        'cnpj' => 'CNPJ',
        'phone' => 'Telefone',
        'random' => 'Aleatória',
    ];

    public function index()
    {
        // Certifica-se de que o usuário está autenticado
        $mei_profile_id = session('mei_id');

        if (!$mei_profile_id) {
            return response()->json(['message' => 'Usuário MEI não autenticado ou encontrado.'], 401);
        }

        $keys = MeiPixKey::where('mei_profile_id', $mei_profile_id)
            ->orderByDesc('is_active')
            ->orderByDesc('created_at')
            ->get(['id', 'mei_profile_id', 'type', 'key_value', 'is_active', 'created_at']);


        $transformedKeys = $keys->map(function ($key) {
            $type = $key->type;

            return [
                'id' => $key->id,
                'mei_profile_id' => $key->mei_profile_id,
                'type' => $this->typeMapping[strtolower($type)] ?? ucfirst($type),
                'key_value' => $key->key_value,
                'is_active' => $key->is_active ? 'Sim' : 'Não',
                'created_at' => $key->created_at,
            ];
        });

        return response()->json([
            'keys' => $transformedKeys,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required',
                'key_value' => 'required',
            ], [
                'type.required' => 'O tipo da chave deve ser informado.',
                'key_value.required' => 'A chave deve ser informada.',
                'key_value.unique' => 'Chave PIX já cadastrada previamente para este usuário.',
            ]);

            // Verifica se já existe alguma chave ativa
            $key_ativa = MeiPixKey::where([
                'mei_profile_id' => session('mei_id'),
                'is_active' => 1
            ])->first();

            // Define is_active da nova chave: ativa se não houver nenhuma ativa
            $is_active = empty($key_ativa) ? 1 : 0;

            $key = MeiPixKey::create([
                'mei_profile_id' => session('mei_id'),
                'type' => $validated['type'],
                'key_value' => $validated['key_value'],
                'is_active' => $is_active,
            ]);

            return redirect()->back()->with('success', 'Chave Pix cadastrada com sucesso!');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao salvar a chave PIX: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $meiId = session('mei_id');

            $key = MeiPixKey::findOrFail($id);


            if ($key->mei_profile_id !== $meiId) {
                throw new \Exception('Acesso negado: registro não pertence ao seu MEI.');
            }

            $key->delete();

            return redirect()->back()->with('success', 'Chave Pix excluída com sucesso!');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao salvar a chave PIX: ' . $e->getMessage());
        }
    }

    public function update($id)
    {
        try {
            $meiId = session('mei_id');

            // Busca a chave específica
            $key = MeiPixKey::where('id', $id)
                ->where('mei_profile_id', $meiId)
                ->firstOrFail();

            // Desativa todas as chaves ativas do mesmo MEI
            MeiPixKey::where('mei_profile_id', $meiId)
                ->where('is_active', 1)
                ->update(['is_active' => 0]);

            // Ativa a chave selecionada
            $key->update(['is_active' => 1]);

            return redirect()->back()->with('success', 'Chave PIX ativada com sucesso!');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao ativar a chave PIX: ' . $e->getMessage());
        }
    }

}
