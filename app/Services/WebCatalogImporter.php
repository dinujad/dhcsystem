<?php

namespace App\Services;

use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Product;
use App\Unit;
use App\Variation;
use App\VariationLocationDetails;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class WebCatalogImporter
{
    public function import(): array
    {
        $this->ensureChannelColumn();

        $url = rtrim((string) config('store_api.website_url'), '/');
        $token = (string) config('store_api.token');
        if ($url === '' || $token === '') {
            return ['created' => 0, 'updated' => 0, 'skipped' => true];
        }

        $response = Http::withToken($token)->acceptJson()->timeout(30)->get($url.'/api/pos/catalog');
        $response->throw();
        $rows = $response->json('products') ?? [];

        $business = Business::query()->orderBy('id')->first();
        $location = BusinessLocation::query()->where('business_id', $business?->id)->orderBy('id')->first();
        $unit = Unit::query()->where('business_id', $business?->id)->orderBy('id')->first();
        if (! $business || ! $location || ! $unit) {
            return ['created' => 0, 'updated' => 0, 'skipped' => true];
        }

        $created = 0;
        $updated = 0;
        foreach ($rows as $row) {
            $sku = trim((string) ($row['sku'] ?? ''));
            if ($sku === '') {
                continue;
            }
            $existing = Product::query()->where('business_id', $business->id)->where('sku', $sku)->first();
            if ($existing) {
                $this->refresh($existing, $row, $location->id);
                $updated++;
                continue;
            }
            $this->create($business, $location, $unit, $row, $sku);
            $created++;
        }

        return compact('created', 'updated') + ['skipped' => false];
    }

    private function create(Business $business, BusinessLocation $location, Unit $unit, array $row, string $sku): void
    {
        $variations = collect($row['variations'] ?? [])->filter(fn ($item) => ! empty($item['sku']))->values();
        $type = $variations->count() > 1 ? 'variable' : 'single';
        $categoryId = $this->categoryId($business->id, $row['category'] ?? null);

        $product = Product::query()->create([
            'name' => $row['name'] ?: $sku,
            'business_id' => $business->id,
            'type' => $type,
            'unit_id' => $unit->id,
            'category_id' => $categoryId,
            'sku' => $sku,
            'barcode_type' => 'C128',
            'enable_stock' => 1,
            'alert_quantity' => 0,
            'tax_type' => 'exclusive',
            'product_description' => $row['description'] ?? null,
            'created_by' => $business->owner_id,
            'dhc_channel' => 'web',
            'not_for_selling' => 0,
        ]);
        $product->product_locations()->syncWithoutDetaching([$location->id]);

        if ($type === 'single') {
            $variation = $variations->first();
            $price = (float) ($variation['price'] ?? $row['price'] ?? 0);
            $stock = (int) ($variation['stock'] ?? $row['stock'] ?? 0);
            $subSku = $variation['sku'] ?? $sku;
            $productVariation = $product->product_variations()->create(['name' => 'DUMMY', 'is_dummy' => 1]);
            $created = $productVariation->variations()->create([
                'name' => 'DUMMY',
                'product_id' => $product->id,
                'sub_sku' => $subSku,
                'default_purchase_price' => $price,
                'dpp_inc_tax' => $price,
                'profit_percent' => 0,
                'default_sell_price' => $price,
                'sell_price_inc_tax' => $price,
            ]);
            $this->stock($product, $created, $location->id, $stock);
            return;
        }

        $productVariation = $product->product_variations()->create(['name' => 'Option', 'is_dummy' => 0]);
        foreach ($variations as $variation) {
            $label = trim(($variation['color'] ?? '').' '.($variation['size'] ?? '')) ?: $variation['sku'];
            $price = (float) ($variation['price'] ?? $row['price'] ?? 0);
            $created = $productVariation->variations()->create([
                'name' => $label,
                'product_id' => $product->id,
                'sub_sku' => $variation['sku'],
                'default_purchase_price' => $price,
                'dpp_inc_tax' => $price,
                'profit_percent' => 0,
                'default_sell_price' => $price,
                'sell_price_inc_tax' => $price,
            ]);
            $this->stock($product, $created, $location->id, (int) ($variation['stock'] ?? 0));
        }
    }

    private function refresh(Product $product, array $row, int $locationId): void
    {
        $product->update([
            'name' => $row['name'] ?: $product->name,
            'dhc_channel' => 'web',
            'product_description' => $row['description'] ?? $product->product_description,
        ]);
        $product->product_locations()->syncWithoutDetaching([$locationId]);

        $variations = collect($row['variations'] ?? []);
        if ($variations->isEmpty()) {
            $variation = $product->variations()->first();
            if ($variation) {
                $variation->update([
                    'default_sell_price' => $row['price'] ?? $variation->default_sell_price,
                    'sell_price_inc_tax' => $row['price'] ?? $variation->sell_price_inc_tax,
                ]);
                $this->stock($product, $variation, $locationId, (int) ($row['stock'] ?? 0));
            }
            return;
        }

        foreach ($variations as $item) {
            $variation = Variation::query()->where('sub_sku', $item['sku'])->where('product_id', $product->id)->first();
            if (! $variation) {
                continue;
            }
            $variation->update([
                'default_sell_price' => $item['price'] ?? $variation->default_sell_price,
                'sell_price_inc_tax' => $item['price'] ?? $variation->sell_price_inc_tax,
            ]);
            $this->stock($product, $variation, $locationId, (int) ($item['stock'] ?? 0));
        }
    }

    private function stock(Product $product, Variation $variation, int $locationId, int $qty): void
    {
        VariationLocationDetails::query()->updateOrCreate(
            [
                'product_id' => $product->id,
                'variation_id' => $variation->id,
                'location_id' => $locationId,
            ],
            [
                'product_variation_id' => $variation->product_variation_id,
                'qty_available' => $qty,
            ]
        );
    }

    private function categoryId(int $businessId, ?string $name): ?int
    {
        $name = trim((string) $name);
        if ($name === '') {
            return null;
        }
        $category = Category::query()->firstOrCreate(
            ['business_id' => $businessId, 'name' => $name, 'category_type' => 'product'],
            ['parent_id' => 0, 'created_by' => Business::query()->find($businessId)?->owner_id]
        );

        return $category->id;
    }

    private function ensureChannelColumn(): void
    {
        if (! Schema::hasColumn('products', 'dhc_channel')) {
            Schema::table('products', function ($table) {
                $table->string('dhc_channel', 20)->nullable()->index();
            });
        }
    }
}
