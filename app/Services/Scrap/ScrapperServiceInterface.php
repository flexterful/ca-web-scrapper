<?php

namespace App\Services\Scrap;

use App\Models\Job;

interface ScrapperServiceInterface
{
    /**
     * @param Job $job
     *
     * @return void
     */
    public function scrap(Job $job): void;
}
