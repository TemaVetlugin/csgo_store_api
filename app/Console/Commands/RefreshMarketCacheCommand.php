<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Market\Manager\MarketCacheManager;
use Illuminate\Console\Command;
use Illuminate\Log\Logger;

class RefreshMarketCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:refresh-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh cached API response for all market products and currencies.';

    /**
     * Execute the console command.
     */
    public function handle(MarketCacheManager $marketCacheManager, Logger $logger): void
    {
        $marketCacheManager->refreshCache();

        $logger->info('Market cache has been refreshed successfully!');
        $this->info('Market cache has been refreshed successfully!');
    }
}
