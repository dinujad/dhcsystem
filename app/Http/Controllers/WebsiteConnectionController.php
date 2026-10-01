<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class WebsiteConnectionController extends Controller
{
    public function test()
    {
        $url = rtrim((string) config('store_api.website_url'), '/');
        $token = (string) config('store_api.token');

        if ($url === '' || $token === '') {
            return response()->json([
                'success' => false,
                'msg' => 'Set WEBSITE_API_URL and STORE_API_TOKEN in the POS .env file.',
            ]);
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(20)
                ->get($url.'/api/pos/catalog');
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Could not reach the website. '.$e->getMessage(),
            ]);
        }

        if (! $response->successful()) {
            return response()->json([
                'success' => false,
                'msg' => 'Website replied HTTP '.$response->status().'.',
            ]);
        }

        $count = count($response->json('products') ?? []);

        return response()->json([
            'success' => true,
            'msg' => 'Connected to the website. Catalog returned '.$count.' products.',
        ]);
    }
}
