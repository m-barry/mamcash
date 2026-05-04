<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class ReclamationController extends Controller
{
    public function show()
    {
        return view('user.reclamation');
    }

    public function send(Request $request)
    {
        $request->validate([
            'to'      => 'required|email',
            'subject' => 'required|string|max:255',
            'body'    => 'required|string|max:5000',
        ]);

        $to      = $request->input('to');
        $subject = $request->input('subject');
        $body    = $request->input('body');
        $user    = Auth::user();
        $from    = $user->email ?? $to;
        $name    = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? $user->name ?? ''));

        Mail::raw($body, function ($message) use ($to, $subject, $from, $name) {
            $message->to($to)
                    ->from($from, $name ?: 'MAMCash')
                    ->subject($subject);
        });

        return back()->with('success', 'Votre message a été envoyé avec succès.');
    }
}
