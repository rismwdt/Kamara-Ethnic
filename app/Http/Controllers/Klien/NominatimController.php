<?php

namespace App\Http\Controllers\Klien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NominatimController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('q');

        if (!$query) {
            return response()->json([]);
        }

        $url = 'https://nominatim.openstreetmap.org/search';

        $response = Http::withHeaders([
            'User-Agent' => 'YourAppName/1.0 (your-email@example.com)' // wajib ada User-Agent sesuai aturan Nominatim
        ])->get($url, [
            'format' => 'json',
            'countrycodes' => 'id',
            'accept-language' => 'id',
            'q' => $query,
            'limit' => 5,
        ]);

        return response($response->body(), $response->status())
            ->header('Content-Type', 'application/json');
    }
}
