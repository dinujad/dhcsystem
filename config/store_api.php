<?php

return [
    'token' => env('STORE_API_TOKEN', ''),
    'website_url' => rtrim((string) env('WEBSITE_API_URL', 'http://127.0.0.1:8001'), '/'),
    'brand' => 'DHC Shoes',
];
