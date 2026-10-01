<?php

namespace App\Console\Commands;

use App\Business;
use App\Support\DhcBrand;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BrandDhcShoes extends Command
{
    protected $signature = 'dhc:brand';

    protected $description = 'Set the POS business name and invoice logo to DHC Shoes';

    public function handle(): int
    {
        $updated = Business::query()->update(['name' => DhcBrand::NAME]);
        $this->info("Business name set to DHC Shoes on {$updated} row(s).");

        if (Schema::hasTable('invoice_layouts') && Schema::hasColumn('invoice_layouts', 'logo')) {
            DB::table('invoice_layouts')->update([
                'show_logo' => 1,
                'logo' => 'dhc-logo.svg',
            ]);
            $this->info('Invoice layouts now use the DHC Shoes logo.');
        }

        return self::SUCCESS;
    }
}
