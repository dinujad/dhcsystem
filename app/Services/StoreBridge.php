<?php

namespace App\Services;

use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Product;
use App\Support\DhcBrand;
use App\Transaction;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreBridge
{
    public function __construct(
        private TransactionUtil $transactionUtil,
        private ProductUtil $productUtil
    ) {
    }

    public function catalog(): array
    {
        $products = Product::query()
            ->where('is_inactive', 0)
            ->where('not_for_selling', 0)
            ->with([
                'category',
                'variations.product_variation',
                'variations.variation_location_details',
            ])
            ->orderBy('name')
            ->get();

        return [
            'ok' => true,
            'brand' => DhcBrand::NAME,
            'logo' => DhcBrand::logoUrl(),
            'products' => $products->map(fn (Product $product) => $this->productPayload($product))->values(),
        ];
    }

    public function createOrder(array $payload): array
    {
        $reference = trim((string) ($payload['reference'] ?? ''));
        if ($reference === '') {
            abort(422, 'Order reference is required.');
        }

        $existing = Transaction::query()
            ->where('source', 'dhc_website')
            ->where('custom_field_1', $reference)
            ->first();
        if ($existing) {
            return $this->orderPayload($existing, true);
        }

        $business = Business::query()->orderBy('id')->first();
        $location = BusinessLocation::query()->where('business_id', $business?->id)->orderBy('id')->first();
        if (! $business || ! $location) {
            abort(422, 'POS business or location is not set up yet.');
        }

        $lines = [];
        foreach ($payload['items'] ?? [] as $item) {
            $variation = $this->findVariation((string) ($item['sku'] ?? ''), (int) $business->id);
            if (! $variation) {
                abort(422, 'SKU '.($item['sku'] ?? '').' is not in the POS catalog.');
            }
            $qty = (float) ($item['qty'] ?? 0);
            $price = (float) ($item['price'] ?? $variation->sell_price_inc_tax);
            if ($qty <= 0) {
                continue;
            }
            $lines[] = [
                'product_id' => $variation->product_id,
                'variation_id' => $variation->id,
                'quantity' => $qty,
                'unit_price' => $price,
                'unit_price_inc_tax' => $price,
                'item_tax' => 0,
                'tax_id' => null,
                'enable_stock' => (int) $variation->product->enable_stock,
                'sell_line_note' => trim(($item['size'] ?? '').' '.($item['color'] ?? '')),
            ];
        }

        if ($lines === []) {
            abort(422, 'Order has no sellable lines.');
        }

        $customer = $payload['customer'] ?? [];
        $contact = $this->contact($business, $customer);
        $discount = (float) ($payload['discount'] ?? 0);
        $shipping = (float) ($payload['shipping'] ?? 0);
        $total = (float) ($payload['total'] ?? 0);
        if ($total <= 0) {
            $total = max(0, collect($lines)->sum(fn ($line) => $line['quantity'] * $line['unit_price']) - $discount + $shipping);
        }

        $userId = (int) $business->owner_id;
        $input = [
            'location_id' => $location->id,
            'contact_id' => $contact->id,
            'status' => 'final',
            'transaction_date' => now()->toDateTimeString(),
            'discount_type' => 'fixed',
            'discount_amount' => $discount,
            'final_total' => $total,
            'sale_note' => $payload['note'] ?? null,
            'staff_note' => 'Website order '.$reference,
            'source' => 'dhc_website',
            'custom_field_1' => $reference,
            'custom_field_2' => $payload['payment_method'] ?? null,
            'shipping_charges' => $shipping,
            'shipping_details' => $payload['shipping_method'] ?? null,
            'shipping_address' => trim(($customer['address'] ?? '')."\n".($customer['city'] ?? '')),
            'delivered_to' => $customer['name'] ?? null,
            'is_created_from_api' => 1,
            'is_direct_sale' => 1,
            'commission_agent' => null,
            'customer_group_id' => $contact->customer_group_id,
        ];

        $transaction = DB::transaction(function () use ($business, $location, $input, $lines, $userId, $total, $discount) {
            $transaction = $this->transactionUtil->createSellTransaction($business->id, $input, [
                'total_before_tax' => max(0, $total - (float) ($input['shipping_charges'] ?? 0) + $discount),
                'tax' => 0,
            ], $userId, false);

            $this->transactionUtil->createOrUpdateSellLines($transaction, $lines, $location->id, false, null, [], false);

            foreach ($lines as $line) {
                if ($line['enable_stock']) {
                    $this->productUtil->decreaseProductQuantity(
                        $line['product_id'],
                        $line['variation_id'],
                        $location->id,
                        $line['quantity']
                    );
                }
            }

            try {
                $this->transactionUtil->mapPurchaseSell([
                    'id' => $business->id,
                    'accounting_method' => $business->accounting_method,
                    'location_id' => $location->id,
                ], $transaction->sell_lines, 'purchase');
            } catch (\Throwable $e) {
                \Log::warning('DHC website sale saved without purchase mapping: '.$e->getMessage());
            }

            $transaction->payment_status = 'due';
            $transaction->save();

            return $transaction;
        });

        return $this->orderPayload($transaction, false);
    }

    private function productPayload(Product $product): array
    {
        $variations = $product->variations->map(function ($variation) {
            $stock = (float) $variation->variation_location_details->sum('qty_available');
            $attribute = (string) optional($variation->product_variation)->name;

            return [
                'id' => $variation->id,
                'sku' => $variation->sub_sku,
                'name' => $variation->name,
                'attribute' => $attribute,
                'price' => (float) $variation->sell_price_inc_tax,
                'compare_at' => (float) $variation->default_sell_price !== (float) $variation->sell_price_inc_tax
                    ? (float) $variation->default_sell_price
                    : null,
                'stock' => $stock,
                'image' => null,
            ];
        })->values();

        $price = (float) ($variations->min('price') ?? 0);

        return [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'type' => $product->type,
            'description' => $product->product_description,
            'category' => optional($product->category)->name,
            'image' => $product->image_url,
            'price' => $price,
            'stock' => (float) $variations->sum('stock'),
            'variations' => $variations,
        ];
    }

    private function findVariation(string $sku, int $businessId): ?Variation
    {
        $sku = trim($sku);
        if ($sku === '') {
            return null;
        }

        return Variation::query()
            ->where('sub_sku', $sku)
            ->whereHas('product', fn ($query) => $query->where('business_id', $businessId))
            ->with('product')
            ->first()
            ?: Variation::query()
                ->whereHas('product', fn ($query) => $query->where('business_id', $businessId)->where('sku', $sku))
                ->with('product')
                ->first();
    }

    private function contact(Business $business, array $customer): Contact
    {
        $phone = preg_replace('/\s+/', '', (string) ($customer['phone'] ?? '')) ?: null;
        $email = $customer['email'] ?? null;
        $existing = Contact::query()
            ->where('business_id', $business->id)
            ->where(function ($query) use ($phone, $email) {
                if ($phone) {
                    $query->orWhere('mobile', $phone);
                }
                if ($email) {
                    $query->orWhere('email', $email);
                }
            })
            ->first();

        if ($existing) {
            return $existing;
        }

        $name = trim((string) ($customer['name'] ?? 'Website customer'));

        return Contact::query()->create([
            'business_id' => $business->id,
            'type' => 'customer',
            'name' => $name,
            'first_name' => Str::before($name, ' ') ?: $name,
            'last_name' => trim(Str::after($name, ' ')) ?: null,
            'mobile' => $phone,
            'email' => $email,
            'city' => $customer['city'] ?? null,
            'address_line_1' => $customer['address'] ?? null,
            'created_by' => $business->owner_id,
            'contact_id' => 'WEB-'.strtoupper(Str::random(6)),
        ]);
    }

    private function orderPayload(Transaction $transaction, bool $existing): array
    {
        return [
            'ok' => true,
            'existing' => $existing,
            'invoice_no' => $transaction->invoice_no,
            'transaction_id' => $transaction->id,
            'status' => $transaction->status,
            'payment_status' => $transaction->payment_status,
            'total' => (float) $transaction->final_total,
        ];
    }
}
