<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Observers\JobObserver;
use DateTime;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $queue
 * @property string $payload
 * @property string $login_prefix
 * @property int $attempts
 * @property DateTime $available_at
 * @property DateTime $created_at
 * @property DateTime $reserved_at
 * @property DateTime $updated_at
 * @property string $scrapped
 */
#[ApiResource(
    operations: [
        new Delete(),
        new Get(),
        new Post(),
    ],
)]
#[ObservedBy([JobObserver::class])]
class Job extends Model
{
}
