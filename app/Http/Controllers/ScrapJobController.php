<?php

namespace App\Http\Controllers;

use App\ApiResources\ScrapJob;
use App\Http\Requests\ScrapJobPostRequest;
use App\Jobs\ScrappingJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;

class ScrapJobController extends Controller
{
    /**
     * Get existing job info
     *
     * @param string $id
     *
     * @return JsonResponse
     */
    public function get(string $id): JsonResponse
    {
        // Fetch the job
        $job = json_decode(Redis::get($id), true);

        // Return contents or 404 response
        return $job
            ? response()->json($job)
            : response()->json(data: 'Not Found', status: 404);
    }

    /**
     * Store a new job
     *
     * @param ScrapJobPostRequest $request
     *
     * @return JsonResponse
     */
    public function post(ScrapJobPostRequest $request): JsonResponse
    {
        // Build a job using request params
        $scrapJob = new ScrapJob(
            id: uniqid('job_'),
            urls: $request['payload']['urls'],
            selectors: $request['payload']['selectors'],
        );

        // Store job data
        Redis::set($scrapJob->id, json_encode($scrapJob));

        // Add job to queue
        ScrappingJob::dispatch($scrapJob->id);

        return response()->json(['job_id' => $scrapJob->id], 201);
    }

    /**
     * Delete existing job
     *
     * @param string $id
     *
     * @return JsonResponse
     */
    public function delete(string $id): JsonResponse
    {
        // Remove the job and save status
        $success = Redis::del($id);

        // Return success (204 Deleted) or 404 response
        return $success ?
            response()->json(data: 'Deleted', status: 204) :
            response()->json(data: 'Not Found', status: 404);
    }
}
