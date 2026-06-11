<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class RdwController extends Controller
{
    /**
     * B1 — Look up a car's data at the RDW open-data API by licence plate,
     * so the offerer doesn't have to type everything by hand.
     */
    public function lookup(string $plate): JsonResponse
    {
        // Normalise: strip dashes/spaces, uppercase. RDW stores plates without separators.
        $kenteken = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $plate));

        $response = Http::acceptJson()
            ->timeout(10)
            ->get('https://opendata.rdw.nl/resource/m9d7-ebf2.json', [
                'kenteken' => $kenteken,
            ]);

        if ($response->failed() || empty($response->json())) {
            return response()->json([
                'found' => false,
                'message' => 'Geen gegevens gevonden voor dit kenteken.',
            ], 404);
        }

        $data = $response->json()[0];

        return response()->json([
            'found' => true,
            'data' => [
                'brand' => $data['merk'] ?? null,
                'model' => $data['handelsbenaming'] ?? null,
                'seats' => isset($data['aantal_zitplaatsen']) ? (int) $data['aantal_zitplaatsen'] : null,
                'doors' => isset($data['aantal_deuren']) ? (int) $data['aantal_deuren'] : null,
                'production_year' => isset($data['datum_eerste_toelating'])
                    ? (int) substr($data['datum_eerste_toelating'], 0, 4)
                    : null,
                'weight' => isset($data['massa_ledig_voertuig']) ? (int) $data['massa_ledig_voertuig'] : null,
                'color' => isset($data['eerste_kleur']) ? ucfirst(strtolower($data['eerste_kleur'])) : null,
            ],
        ]);
    }
}
