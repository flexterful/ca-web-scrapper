<?php

namespace App\Observers;

use App\Exceptions\InsufficientJobParametersException;
use App\Models\Job;
use App\Services\Scrap\WebScrapperService;

readonly class JobObserver
{
    public function __construct(private WebScrapperService $webScrapperService)
    {
    }

    /**
     * Scrape the page for a newly created job
     *
     * @throws InsufficientJobParametersException
     */
    public function created(Job $job): void
    {
        $this->webScrapperService->scrap($job);
    }
}
