<?php

namespace App\Services\Scrap;

use App\ApiResources\ScrapJob;

interface ScrapperServiceInterface
{
    /**
     * @param ScrapJob $job
     *
     * @return void
     */
    public function scrap(ScrapJob $job): void;
}
