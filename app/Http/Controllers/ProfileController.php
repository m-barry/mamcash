<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user    = auth()->user();
        $address = $user->address ?? new Address();
        return view('user.profile', compact('user', 'address'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'firstname'    => ['required', 'string', 'max:100'],
            'lastname'     => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone_number' => ['required', 'string', 'max:30', 'unique:users,phone_number,' . $user->id],
            'country'      => ['required', 'string', 'max:10'],
            'city'         => ['nullable', 'string', 'max:100'],
            'gender'       => ['nullable', 'in:male,female,other'],
            'street'       => ['nullable', 'string', 'max:255'],
            'house_number' => ['nullable', 'string', 'max:20'],
            'postal_code'  => ['nullable', 'string', 'max:20'],
            'address_city' => ['nullable', 'string', 'max:100'],
        ]);

        $user->update([
            'firstname'    => $validated['firstname'],
            'lastname'     => $validated['lastname'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'country'      => $validated['country'],
            'city'         => $validated['city'] ?? null,
            'gender'       => $validated['gender'] ?? null,
        ]);

        $user->address()->updateOrCreate(
            ['id_user' => $user->id],
            [
                'street'       => $validated['street'] ?? null,
                'house_number' => $validated['house_number'] ?? null,
                'zip_code'     => $validated['postal_code'] ?? null,
                'city'         => $validated['address_city'] ?? null,
            ]
        );

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Mot de passe modifié.');
    }
}
