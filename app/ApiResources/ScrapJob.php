<?php

namespace App\ApiResources;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;

#[ApiResource(
    shortName: 'Job',
    operations: [
        new Delete(uriTemplate: '/jobs/{id}'),
        new Get(uriTemplate: '/jobs/{id}'),
        new Post(
            uriTemplate: '/jobs',
            openapi: new Model\Operation(
                requestBody:
                new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'payload' => ['type' => 'object'],
                                ],
                            ],
                            'example' => [
                                'payload' => [
                                    'urls' => ['https://example.com'],
                                    'selectors' => [
                                        'title' => 'h1'
                                    ]
                                ],
                            ]
                        ]
                    ]),
                    required: true
                ),
            ),
        ),
    ],
)]
class ScrapJob
{
    public function __construct(
        public string $id,
        public array $urls,
        public array $selectors,
    ) {
    }
}
