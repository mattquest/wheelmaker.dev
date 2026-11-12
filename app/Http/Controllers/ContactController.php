<?php

namespace App\Http\Controllers;

use App\Services\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function post(Request $request, Recaptcha $recaptcha)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'company' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:5000'],
            // Honeypot: validated leniently on purpose — a filled value
            // is answered with a fake success below, not a 422, so bots
            // can't tell the field is a trap.
            'website' => ['nullable', 'string', 'max:255'],
        ]);

        // Honeypot filled: pretend it worked, send nothing.
        if ($request->filled('website')) {
            return response()->json(['status' => 'ok']);
        }

        if (! $recaptcha->passes($request->input('token'), $request->ip())) {
            return response()->json([
                'status' => 'error',
                'message' => 'The bot check did not pass. Please try again.',
            ], 422);
        }

        $body = 'from: '.$validated['name']."\n"
            .'email: '.$validated['email']."\n"
            .'company: '.($validated['company'] ?? '')."\n"
            .'message: '.$validated['message'];

        Mail::raw($body, function ($message) {
            $message
                ->subject('personal site inquiry')
                ->to('matt.t.rademacher@gmail.com')
                ->from('noreply@wheelmaker.dev');
        });

        return response()->json(['status' => 'ok']);
    }
}
