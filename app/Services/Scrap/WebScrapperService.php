<?php

namespace App\Services\Scrap;

use App\ApiResources\ScrapJob;
use App\Exceptions\InsufficientJobParametersException;
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
     * @param ScrapJob $job
     *
     * @return void
     *
     * @throws InsufficientJobParametersException
     */
    public function scrap(ScrapJob $job): void
    {
        // Parse job payload
        if (!$this->checkPayload($job)) {
            throw new InsufficientJobParametersException();
        }

        // Begin the DB transaction
        DB::beginTransaction();

        $this->scrapMultipleUrls($job->urls, $job->selectors);
        // Scrap the pages

        // Finish the transaction and save results to DB
        DB::commit();
    }

    /**
     * Check the job payload for URLs and CSS/HTML selectors
     *
     * @param ScrapJob $job
     *
     * @return bool
     */
    private function checkPayload(ScrapJob $job): bool
    {
        return !empty($job->urls) && !empty($job->urls);
    }

    /**
     * Save all parsed items to the dedicated DB table
     *
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
                // Store any scrapped data to the database
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
