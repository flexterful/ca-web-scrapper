<?php

namespace Tests\Unit;

use App\Exceptions\InsufficientJobParametersException;
use App\Models\Job;
use App\Services\Scrap\WebScrapperService;
use DateTime;
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

        /** @var HttpBrowser $browser */
        $browser = $this->browserMock; // Used to type-hint the mocked browser
        $this->webScrapperService = new WebScrapperService($browser);
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
            $this->createTestJob()
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
        $this->browserMock->shouldReceive('request')->andReturn($this->crawlerMock);

        // Mock the crawler to return specific content for given selectors
        $this->crawlerMock->shouldReceive('filter')->with('h1')->andReturnSelf();
        $this->crawlerMock->shouldReceive('each')->andReturn(['Sample Title']);

        // Create a Job with a valid payload
        $job = $this->createTestJob(
            payload: [
                'urls' => ['https://example.com'],
                'selectors' => ['title' => 'h1']
            ]
        );

        // Act
        $this->webScrapperService->scrap($job);

        // Assert
        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'scrapped' => json_encode([
                'https://example.com' => [
                    'title' => ['Sample Title']
                ]
            ])
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
        $job = $this->createTestJob(
            payload: [
                'urls' => ['https://example.com'],
                'selectors' => ['title' => 'h1']
            ]
        );

        // Act
        $this->webScrapperService->scrap($job);

        // Assert
        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'scrapped' => json_encode([])
        ]);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @param string|null $attempts
     * @param DateTime|null $available_at
     * @param array|null $payload
     * @param string|null $queue
     *
     * @return Job
     */
    private function createTestJob(
        ?string $attempts = '0',
        ?DateTime $available_at = null,
        ?array $payload = [],
        ?string $queue = 'default'
    ): Job {
        return Job::create([
            'attempts' => $attempts,
            'available_at' => $available_at ?? now(),
            'payload' => json_encode($payload),
            'queue' => $queue
        ]);
    }
}
