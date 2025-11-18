<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\MeiProfile;
use Illuminate\Validation\ValidationException;
use Exception;

class ProfileMeiController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('meiProfile');

        $meiProfile = $user->meiProfile->first();
        $hasMeiProfile = !is_null($meiProfile);

        $data = $hasMeiProfile ? [
            'id' => $meiProfile->id,
            'identification' => $meiProfile->identification,
            'cnpj' => $meiProfile->cnpj,
            'email' => $meiProfile->email,
            'phone' => $meiProfile->phone,
            'street' => $meiProfile->street,
            'number' => $meiProfile->number,
            'complement' => $meiProfile->complement,
            'district' => $meiProfile->district,
            'city' => $meiProfile->city,
            'state' => $meiProfile->state,
            'zip_code' => $meiProfile->zip_code,
            'profile_photo' => $meiProfile->profile_photo
        ] : null;

        return Inertia::render('PerfilMei', [
            'data' => $data,
            'isEdit' => $hasMeiProfile,
        ]);
    }

    public function store(Request $request)
    {
        try {

            $request->merge([
                'number' => $request->number !== null ? (string) $request->number : null,
            ]);

            $validatedData = $request->validate([
                'identification' => 'required|string|max:255',
                'cnpj' => 'required|string|max:20|unique:mei_profile',
                'email' => 'required|email|max:255|unique:mei_profile',
                'phone' => 'required|string|max:255',
                'street' => 'nullable|string|max:255',
                'number' => 'nullable|string|max:255',
                'complement' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:2',
                'zip_code' => 'nullable|string|max:10',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($request->hasFile('profile_photo')) {
                $cnpj = preg_replace('/\D/', '', $validatedData['cnpj']);

                $path = $request->file('profile_photo')->store("profile_images/{$cnpj}", 'public');

                $validatedData['profile_photo'] = $path;
            }

            $meiProfile = MeiProfile::create($validatedData);

            $meiProfile->users()->attach(auth()->id());

            // Salva dados do CNPJ
            session(['mei_id' => $meiProfile->id]);
            session(['mei_cnpj' => $meiProfile->cnpj]);

            return redirect()->back()->with('success', 'Perfil MEI salvo com sucesso!');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao salvar o perfil MEI: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $request->merge([
                'number' => $request->number !== null ? (string) $request->number : null,
            ]);

            $meiProfile = MeiProfile::findOrFail($id);

            $validatedData = $request->validate([
                'identification' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:mei_profile,email,' . $meiProfile->id,
                'phone' => 'required|string|max:255',
                'street' => 'nullable|string|max:255',
                'number' => 'nullable|string|max:255',
                'complement' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:2',
                'zip_code' => 'nullable|string|max:10',
            ]);

            // Mantém o CNPJ original
            $validatedData['cnpj'] = $meiProfile->cnpj;

            if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
                $request->validate([
                    'profile_photo' => 'image|mimes:jpeg,png,jpg|max:2048',
                ]);

                $cnpj = preg_replace('/\D/', '', $meiProfile->cnpj);

                // Apaga imagem antiga se existir
                if ($meiProfile->profile_photo && \Storage::disk('public')->exists($meiProfile->profile_photo)) {
                    \Storage::disk('public')->delete($meiProfile->profile_photo);
                }

                // Salva nova imagem
                $path = $request->file('profile_photo')->store("profile_images/{$cnpj}", 'public');
                $validatedData['profile_photo'] = $path;
            }

            $meiProfile->update($validatedData);

            return redirect()->back()->with('success', 'Perfil MEI atualizado com sucesso!');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao atualizar o perfil MEI: ' . $e->getMessage());
        }
    }
}
