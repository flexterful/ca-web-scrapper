<?php

namespace App\Services;

use App\Exceptions\InsufficientJobParametersException;
use App\Models\Job;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;

class WebScrapperService
{
    /**
     * @param Job $job
     *
     * @return void
     *
     * @throws InsufficientJobParametersException
     */
    public function scrap(Job $job): void
    {
        // Parse job payload
        if (!$this->parsePayload($job, $urls, $selectors)) {
            throw new InsufficientJobParametersException();
        }

        // Scrap the pages
        $scrapedData = $this->scrapMultipleUrls($urls, $selectors);

        // Save results to DB
        $job->scrapped = json_encode($scrapedData);
        $job->save();
    }

    /**
     * Parse the job payload for URLs and CSS/HTML selectors
     *
     * @param Job $job
     * @param $urls
     * @param $selectors
     *
     * @return bool
     */
    private function parsePayload(Job $job, &$urls, &$selectors): bool
    {
        $payload = json_decode($job->payload, true);
        $urls = $payload['urls'] ?? null;
        $selectors = $payload['selectors'] ?? null;

        return !empty($selectors) && !empty($urls);
    }

    /**
     * Get contents for all specified selectors
     *
     * @param Crawler $crawler
     * @param array $selectors
     *
     * @return array
     */
    private function scrapMultipleSelectors(Crawler $crawler, array $selectors): array
    {
        $scrappedUrlData = [];

        // Walk through every selector provided in the payload
        foreach ($selectors as $contentName => $selector) {
            // Walk through every node that matches the selector
            $parsedNodeContents = $this->scrapSelector($crawler, $selector);
            // Skip empty results
            if (!empty($parsedNodeContents)) {
                $scrappedUrlData[$contentName] = $parsedNodeContents;
            }
        }

        return $scrappedUrlData;
    }

    /**
     * Get contents of any element that matches the specified selector
     *
     * @param Crawler $crawler
     * @param string $selector
     *
     * @return array
     */
    private function scrapSelector(Crawler $crawler, string $selector): array
    {
        return $crawler->filter($selector)->each(function ($node) {
            return $node->text();
        });
    }

    /**
     * Scrap contents for specified URLs
     *
     * @param array $urls
     * @param array $selectors
     *
     * @return array
     */
    private function scrapMultipleUrls(array $urls, array $selectors): array
    {
        // Prepare for scrapping
        $browser = new HttpBrowser();
        $scrapedData = [];

        // Walk through every URL provided in the payload
        foreach ($urls as $url) {
            $scrappedUrlData = $this->scrapUrl($browser, $url, $selectors);
            // Skip empty results
            if (!empty($scrappedUrlData)) {
                $scrapedData[$url] = $scrappedUrlData;
            }
        }

        return $scrapedData;
    }

    /**
     * Scrap contents of the specified URL
     *
     * @param HttpBrowser $browser
     * @param string $url
     * @param array $selectors
     *
     * @return array
     */
    private function scrapUrl(HttpBrowser $browser, string $url, array $selectors): array
    {
        // Load the page contents
        $crawler = $browser->request('GET', $url);

        // Scrap the page
        return $this->scrapMultipleSelectors($crawler, $selectors);
    }
}
