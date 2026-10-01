<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StoreBridge;
use App\Support\DhcBrand;
use Illuminate\Http\Request;

class StoreBridgeController extends Controller
{
    public function __construct(private StoreBridge $bridge)
    {
    }

    public function ping()
    {
        return response()->json([
            'ok' => true,
            'brand' => DhcBrand::NAME,
            'logo' => DhcBrand::logoUrl(),
        ]);
    }

    public function catalog()
    {
        return response()->json($this->bridge->catalog());
    }

    public function storeOrder(Request $request)
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:80'],
            'customer' => ['required', 'array'],
            'customer.name' => ['required', 'string', 'max:160'],
            'customer.email' => ['nullable', 'email'],
            'customer.phone' => ['nullable', 'string', 'max:40'],
            'customer.city' => ['nullable', 'string', 'max:80'],
            'customer.address' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sku' => ['required', 'string', 'max:120'],
            'items.*.name' => ['nullable', 'string'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.size' => ['nullable', 'string'],
            'items.*.color' => ['nullable', 'string'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'shipping' => ['nullable', 'numeric', 'min:0'],
            'total' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:80'],
            'shipping_method' => ['nullable', 'string', 'max:80'],
            'note' => ['nullable', 'string'],
        ]);

        return response()->json($this->bridge->createOrder($data));
    }
}
