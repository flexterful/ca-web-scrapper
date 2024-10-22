<?php

namespace App\Observers;

use App\Models\Job;
use App\Services\WebScrapperService;

readonly class JobObserver
{
    public function __construct(private WebScrapperService $webScrapperService)
    {
    }

    /**
     * Scrape the page for a newly created job
     */
    public function created(Job $job): void
    {
        $this->webScrapperService->scrap($job);
    }
}
