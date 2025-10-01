<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function store(Request $request)
    {

        /* Faz a validação dos dados */
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ], [
            'name.required' => 'A identificação do usuário é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está cadastrado no sistema.',
            'phone.unique' => 'Este telefone já está cadastrado no sistema.',

        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        /* Salva os dados no banco */
        $user = User::create([
            'type' => 3,
            'access_permission' => 3,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return ($user) ? redirect()->route('perfil.empresa') : back()->with('error', 'Erro ao cadastrar o usuário. Tente novamente.');
    }
}
