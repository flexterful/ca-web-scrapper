<?php

namespace App\Services\Scrap;

use App\Exceptions\InsufficientJobParametersException;
use App\Models\Job;
use App\Models\ScrappedItem;
use Illuminate\Support\Facades\DB;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;

readonly class WebScrapperService implements ScrapperServiceInterface
{
    /**
     * @param HttpBrowser $browser
     */
    public function __construct(private HttpBrowser $browser)
    {
    }

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

        // Begin the DB transaction
        DB::beginTransaction();

        // Scrap the pages
        $this->scrapMultipleUrls($urls, $selectors);

        // Finish the transaction and save results to DB
        DB::commit();
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
     * @param array $parsedNodeContents
     * @param string $contentName
     * @param string $selector
     * @param string $url
     *
     * @return void
     */
    private function saveParsedContents(
        array $parsedNodeContents,
        string $contentName,
        string $selector,
        string $url
    ): void {
        foreach ($parsedNodeContents as $content) {
            ScrappedItem::make(
                attribute: $contentName,
                selector: $selector,
                url: $url,
                value: $content,
            );
        }
    }

    /**
     * Get contents for all specified selectors
     *
     * @param Crawler $crawler
     * @param array $selectors
     * @param string $url
     *
     * @return void
     */
    private function scrapMultipleSelectors(Crawler $crawler, array $selectors, string $url): void
    {
        // Walk through every selector provided in the payload
        foreach ($selectors as $contentName => $selector) {
            // Walk through every node that matches the selector
            $parsedNodeContents = $this->scrapSelector($crawler, $selector);
            if (!empty($parsedNodeContents)) {
                $this->saveParsedContents($parsedNodeContents, $contentName, $selector, $url);
            }
        }
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
     * @return void
     */
    private function scrapMultipleUrls(array $urls, array $selectors): void
    {
        // Walk through every URL provided in the payload
        foreach ($urls as $url) {
            $this->scrapUrl($url, $selectors);
        }
    }

    /**
     * Scrap contents of the specified URL
     *
     * @param string $url
     * @param array $selectors
     *
     * @return void
     */
    private function scrapUrl(string $url, array $selectors): void
    {
        // Load the page contents
        $crawler = $this->browser->request('GET', $url);

        // Scrap the page
        $this->scrapMultipleSelectors($crawler, $selectors, $url);
    }
}
