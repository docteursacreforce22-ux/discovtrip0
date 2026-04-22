<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    private function buildSystemPrompt(): string
    {
        return "Tu es DiscovGuide, assistant IA de DiscovTrip, plateforme de voyage au Bénin (Afrique de l'Ouest). "
             . "Réponds en français, chaleureusement, en 2-3 phrases maximum. "
             . "Tu aides les visiteurs à découvrir le Bénin : destinations, expériences culturelles, nature, gastronomie. "
             . "Infos pratiques : visa à l'arrivée (50 USD), monnaie FCFA (1 EUR = 655 FCFA), meilleure période novembre-mars. "
             . "Si la question ne concerne pas le Bénin ou le voyage, redirige poliment vers DiscovTrip.";
    }

    public function chat(Request $request)
    {
        $request->validate([
            'messages'           => ['required', 'array', 'max:10'],
            'messages.*.role'    => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:1000'],
        ]);

        $apiKey = config('services.groq.key');

        if (!$apiKey) {
            return response()->json(['error' => 'Service indisponible.'], 503);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(20)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'llama-3.1-8b-instant',
                'max_tokens'  => 250,
                'temperature' => 0.7,
                'messages'    => array_merge(
                    [['role' => 'system', 'content' => $this->buildSystemPrompt()]],
                    array_slice($request->messages, -4)
                ),
            ]);

            if ($response->failed()) {
                return response()->json(['error' => 'Erreur ' . $response->status()], 502);
            }

            return response()->json([
                'message' => $response->json('choices.0.message.content') ?? 'Réessayez.'
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json(['error' => 'Timeout: ' . $e->getMessage()], 504);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }
}