<?php

namespace Tests\Unit;

use App\Exceptions\InsufficientJobParametersException;
use App\Models\Job;
use App\Models\ScrappedItem;
use App\Services\Scrap\WebScrapperService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class WebScrapperServiceTest extends TestCase
{
    use RefreshDatabase;

    private WebScrapperService $webScrapperService;

    private mixed $browserMock;

    private mixed $crawlerMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->browserMock = Mockery::mock(HttpBrowser::class);
        $this->crawlerMock = Mockery::mock(Crawler::class);
        // Use mock class for service
        $this->webScrapperService = new WebScrapperService($this->browserMock);
    }

    /**
     * @return void
     *
     * @throws InsufficientJobParametersException
     */
    public function test_it_throws_exception_when_job_payload_is_missing_parameters(): void
    {
        $this->expectException(InsufficientJobParametersException::class);

        // Create a Job with an invalid payload
        $this->webScrapperService->scrap(
            Job::make()
        );
    }

    /**
     * @return void
     *
     * @throws InsufficientJobParametersException
     */
    public function test_it_successfully_scraps_data_and_updates_the_job(): void
    {
        // Mock the browser request and response
        $this->browserMock->shouldReceive('request')->with('GET', 'https://example.com')->andReturn($this->crawlerMock);

        // Mock the crawler to return specific content for given selectors
        $this->crawlerMock->shouldReceive('filter')->with('h1')->andReturnSelf();
        $this->crawlerMock->shouldReceive('each')->andReturn(['Sample Title']);

        // Create a Job with a valid payload, the scrapping process will start automatically
        $job = Job::make(
            payload: [
                'urls' => ['https://example.com'],
                'selectors' => ['title' => 'h1']
            ]
        );

        $this->webScrapperService->scrap($job);

        // Assert
        $this->assertDatabaseHas(ScrappedItem::TABLE, [
            'attribute' => 'title',
            'selector' => 'h1',
            'url' => 'https://example.com',
            'value' => 'Sample Title',
        ]);
    }

    /**
     * @return void
     *
     * @throws InsufficientJobParametersException
     */
    public function test_it_returns_empty_scrapped_data_when_no_elements_match_selectors(): void
    {
        // Mock the browser request and response
        $this->browserMock->shouldReceive('request')->with('GET', 'https://example.com')->andReturn($this->crawlerMock);

        // Mock the crawler to return empty content for given selectors
        $this->crawlerMock->shouldReceive('filter')->with('h1')->andReturnSelf();
        $this->crawlerMock->shouldReceive('each')->andReturn([]);

        // Create a Job with a valid payload
        $job = Job::make(
            payload: [
                'urls' => ['https://example.com'],
                'selectors' => ['title' => 'h1']
            ]
        );


        // Save the table entry count
        $savedCount = ScrappedItem::all()->count();

        // Act
        $this->webScrapperService->scrap($job);

        // Assert
        $this->assertEquals(
            expected: $savedCount,
            actual: ScrappedItem::all()->count(),
            message: 'No elements should match the selection so there should be no new scrapped products'
        );
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
