<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller
{
    private $apiKey;
    private $baseUrl = 'https://rajaongkir.komerce.id/api/v1';

    public function __construct()
    {
        $this->apiKey = config('rajaongkir.api_key');
    }

    public function checkout()
    {
        return view('checkout_page');
    }

    public function searchDestination(Request $request)
    {
        $search = $request->get('search', '');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $response = Http::withHeaders([
            'key'    => $this->apiKey,
            'Accept' => 'application/json',
        ])->get($this->baseUrl . '/destination/domestic-destination', [
            'search' => $search,
            'limit'  => 10,
            'offset' => 0,
        ]);

        $body    = $response->json();
        $results = $body['data'] ?? [];

        return response()->json($results);
    }

    public function getCost(Request $request)
    {
        $response = Http::withHeaders([
            'key'    => $this->apiKey,
            'Accept' => 'application/json',
        ])->asForm()->post($this->baseUrl . '/calculate/domestic-cost', [
            'origin'      => $request->origin,
            'destination' => $request->destination,
            'weight'      => $request->weight ?? 1000,
            'courier'     => $request->courier,
            'price'       => 'lowest',
        ]);

        $body = $response->json();

        // ✅ FIX: struktur response V2 = { meta: {}, data: [ {}, {} ] }
        $data = $body['data'] ?? [];

        return response()->json($data);
    }
}