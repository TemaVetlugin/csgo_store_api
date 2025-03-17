<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Market\Manager\MarketCacheManager;
use Illuminate\Bus\Queueable;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Log\Logger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class RefreshMarketCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const MIN_REFRESH_PERIOD_SECONDS = 10;

    private const CACHE_KEY_LAST_REFRESH = 'refresh-market-cache-job.last-refresh-time';

    /**
     * Execute the job.
     */
    public function handle(
        MarketCacheManager $marketCacheManager,
        CacheRepository $cacheRepository,
        Logger $logger
    ): void {
        if ($this->isRefreshRateLimitExceeded($cacheRepository)) {
            $logger->notice('Refresh rate limit exceeded, skip refresh products cache job.');

            return;
        }

        $marketCacheManager->refreshCache();

        $this->updateLastRefreshedTime($cacheRepository);

        $logger->info('Market Products API cache refreshed.');
    }

    private function updateLastRefreshedTime(CacheRepository $cacheRepository): void
    {
        $cacheRepository->forever(self::MIN_REFRESH_PERIOD_SECONDS, Carbon::now()->getTimestamp());
    }

    private function isRefreshRateLimitExceeded(CacheRepository $cacheRepository): bool
    {
        $lastRefreshedAtTimestamp = $cacheRepository->get(self::MIN_REFRESH_PERIOD_SECONDS);
        if ($lastRefreshedAtTimestamp === null) {
            return false;
        }

        $lastRefreshedAt = Carbon::createFromTimestamp($lastRefreshedAtTimestamp);
        $nextRefreshAvailableAt = $lastRefreshedAt->addSeconds(self::MIN_REFRESH_PERIOD_SECONDS);

        return Carbon::now()->lte($nextRefreshAvailableAt);
    }
}
