<?php

namespace App\Console\Commands;

use App\Services\WebCatalogImporter;
use Illuminate\Console\Command;

class SyncWebCatalog extends Command
{
    protected $signature = 'dhc:sync-web';

    protected $description = 'Copy published DHC Shoes website products and stock into the POS product list';

    public function handle(WebCatalogImporter $importer): int
    {
        $result = $importer->import();
        if (! empty($result['skipped'])) {
            $this->error('Website catalog was not imported. Check WEBSITE_API_URL, STORE_API_TOKEN, and that the POS business has a location and unit.');

            return self::FAILURE;
        }

        $this->info("Web products synced. Created {$result['created']}, updated {$result['updated']}.");

        return self::SUCCESS;
    }
}
