<?php

namespace App\Support;

class DhcBrand
{
    public const NAME = 'DHC Shoes';

    public static function logoPath(): string
    {
        return public_path('img/dhc-logo.svg');
    }

    public static function logoUrl(): string
    {
        return asset('img/dhc-logo.svg');
    }

    public static function invoiceLogoUrl(): ?string
    {
        $file = public_path('uploads/invoice_logos/dhc-logo.svg');

        return is_file($file) ? asset('uploads/invoice_logos/dhc-logo.svg') : (is_file(self::logoPath()) ? self::logoUrl() : null);
    }
}
