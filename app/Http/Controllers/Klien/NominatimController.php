<?php

namespace App\Http\Controllers\Klien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NominatimController extends Controller
{
    public function search(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        if ($query === '') {
            return response()->json([]);
        }

        try {
            $resp = Http::withHeaders([
                    'User-Agent' => 'KamaraEthnic/1.0 (support@kamara-ethnic.test)',
                    'Accept-Language' => 'id',
                ])
                ->timeout(8)
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $query,
                    'format' => 'json',
                    'addressdetails' => 1,
                    'limit' => 8,
                    'countrycodes' => 'id',
                ]);

            if (!$resp->ok()) {
                return response()->json([
                    'error' => 'Upstream error',
                    'status' => $resp->status(),
                ], 502);
            }

            return response()->json($resp->json());
        } catch (\Throwable $e) {
            return response()->json([
                'error'   => 'Service unavailable',
                'message' => config('app.debug') ? $e->getMessage() : 'Nominatim tidak dapat diakses saat ini',
            ], 503);
        }
    }
}
