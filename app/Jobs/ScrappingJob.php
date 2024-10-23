<?php

namespace App\Jobs;

use App\ApiResources\ScrapJob;
use App\Services\Scrap\WebScrapperService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redis;

class ScrappingJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param string $jobId
     */
    public function __construct(protected string $jobId)
    {
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        // Get stored job info
        $job = json_decode(Redis::get($this->jobId), true);
        if (!$job) {
            return;
        }

        // Perform scrapping with specified parameters
        $webScrapperService = App::make(WebScrapperService::class);
        $webScrapperService->scrap(
            new ScrapJob(
                id: $this->jobId,
                urls: $job['urls'],
                selectors: $job['selectors'],
            )
        );

        // Set status and store to Redis
        $job['status'] = 'completed';
        Redis::set($this->jobId, json_encode($job));
    }
}
