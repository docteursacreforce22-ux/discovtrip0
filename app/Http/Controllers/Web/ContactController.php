<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function show()
    {
        return view('pages.contact');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:60'],
            'last_name'  => ['required', 'string', 'max:60'],
            'email'      => ['required', 'email', 'max:120'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'subject'    => ['required', 'string', 'in:info,booking,custom,partnership,press,other'],
            'message'    => ['required', 'string', 'min:20', 'max:2000'],
        ]);

        $subjects = [
            'info'        => "Demande d'information",
            'booking'     => "Question sur une réservation",
            'custom'      => "Expérience sur mesure",
            'partnership' => "Partenariat / Devenir guide",
            'press'       => "Presse / Médias",
            'other'       => "Autre demande",
        ];

        try {
            // Envoi email à l'équipe
            // Mail::html() est la syntaxe correcte pour envoyer du HTML inline
            $teamHtml = view('emails.contact', [
                'data'     => $validated,
                'subjects' => $subjects,
            ])->render();

            Mail::html($teamHtml, function ($mail) use ($validated, $subjects) {
                $mail->to(config('mail.contact_address', 'contact@discovtrip.com'))
                     ->replyTo($validated['email'], $validated['first_name'] . ' ' . $validated['last_name'])
                     ->subject('[DiscovTrip] ' . $subjects[$validated['subject']] . ' — ' . $validated['first_name'] . ' ' . $validated['last_name']);
            });

            // Email de confirmation au visiteur
            $confirmHtml = view('emails.contact-confirm', [
                'name' => $validated['first_name'],
            ])->render();

            Mail::html($confirmHtml, function ($mail) use ($validated) {
                $mail->to($validated['email'], $validated['first_name'] . ' ' . $validated['last_name'])
                     ->subject('Votre message a bien été reçu — DiscovTrip');
            });

        } catch (\Exception $e) {
            Log::error('Contact form mail error: ' . $e->getMessage());
            // On ne bloque pas l'utilisateur si l'email échoue
        }

        return redirect()
            ->route('contact')
            ->with('success', 'Votre message a bien été envoyé ! Nous vous répondrons sous 24h.');
    }
}