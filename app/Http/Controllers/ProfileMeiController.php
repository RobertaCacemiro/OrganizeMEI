<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\MeiProfile;

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
        $validatedData = $request->validate([
            'identification' => 'required|string|max:255',
            'cnpj' => 'required|string|max:20|unique:mei_profile',
            'email' => 'required|email|max:255|unique:mei_profile',
            'phone' => 'required|string|max:255',
            'street' => 'nullable|string|max:255',
            'number' => 'nullable|int|max:255',
            'complement' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_images', 'public');
            $validatedData['profile_photo'] = $path;
        }

        $meiProfile = MeiProfile::create($validatedData);

        $meiProfile->users()->attach(auth()->id());

        return Inertia::location(route('profile-mei.index'));
    }

    public function update(Request $request, $id)
    {
        $meiProfile = MeiProfile::findOrFail($id);
        $data = $request->except('profile_photo');

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_images', 'public');
            $data['profile_photo'] = $path;
        }

        $meiProfile->update($data);

        return Inertia::location(route('profile-mei.index'));
    }


}
