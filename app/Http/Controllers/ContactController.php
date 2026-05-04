<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::where('user_id', auth()->id())->get();
        return view('user.contacts.index', compact('contacts'));
    }

    public function create()
    {
        $contact = new Contact();
        return view('user.contacts.form', compact('contact'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname'    => ['required', 'string', 'max:100'],
            'lastname'     => ['required', 'string', 'max:100'],
            'telephone'    => ['required', 'string', 'max:30'],
            'country'      => ['required', 'string', 'max:10'],
            'relationship' => ['nullable', 'string', 'max:50'],
            'email'        => ['nullable', 'email', 'max:255'],
            'iban'         => ['nullable', 'string', 'max:50'],
        ]);

        $validated['user_id'] = auth()->id();

        Contact::create($validated);

        return redirect()->route('user.contacts')
            ->with('success', 'Bénéficiaire ajouté avec succès.');
    }

    public function edit(int $id)
    {
        $contact = Contact::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('user.contacts.form', compact('contact'));
    }

    public function update(Request $request, int $id)
    {
        $contact = Contact::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'firstname'    => ['required', 'string', 'max:100'],
            'lastname'     => ['required', 'string', 'max:100'],
            'telephone'    => ['required', 'string', 'max:30'],
            'country'      => ['required', 'string', 'max:10'],
            'relationship' => ['nullable', 'string', 'max:50'],
            'email'        => ['nullable', 'email', 'max:255'],
            'iban'         => ['nullable', 'string', 'max:50'],
        ]);

        $contact->update($validated);

        return redirect()->route('user.contacts')
            ->with('success', 'Bénéficiaire mis à jour.');
    }

    /**
     * AJAX: création rapide d'un bénéficiaire depuis le dashboard.
     */
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'firstname'    => ['required', 'string', 'max:100'],
            'lastname'     => ['required', 'string', 'max:100'],
            'telephone'    => ['required', 'string', 'max:30'],
            'country'      => ['required', 'string', 'max:10'],
            'relationship' => ['nullable', 'string', 'max:50'],
        ]);

        $validated['user_id'] = auth()->id();
        $contact = Contact::create($validated);

        return response()->json([
            'success' => true,
            'contact' => [
                'id'        => $contact->id,
                'firstname' => $contact->firstname,
                'lastname'  => $contact->lastname,
                'country'   => $contact->country,
                'telephone' => $contact->telephone,
            ],
        ]);
    }

    public function destroy(int $id)
    {
        $contact = Contact::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $contact->delete();

        return redirect()->route('user.contacts')
            ->with('success', 'Bénéficiaire supprimé.');
    }
}
