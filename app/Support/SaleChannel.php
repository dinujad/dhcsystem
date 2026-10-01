<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class SaleChannel
{
    public static function products($query, ?string $channel): void
    {
        if ($channel === 'web') {
            $query->where('products.dhc_channel', 'web');
        } elseif ($channel === 'store') {
            $query->where(function ($inner) {
                $inner->whereNull('products.dhc_channel')->orWhere('products.dhc_channel', 'store');
            });
        }
    }

    public static function sales($query, ?string $channel, string $column = 'transactions.source'): void
    {
        if ($channel === 'web') {
            $query->where($column, 'dhc_website');
        } elseif ($channel === 'store') {
            $query->where(function ($inner) use ($column) {
                $inner->whereNull($column)->orWhere($column, '!=', 'dhc_website');
            });
        }
    }
}
