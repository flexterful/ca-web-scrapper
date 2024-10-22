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
 *
 * @method static self create(array $attributes = []))
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
    public const TABLE = 'jobs';

    protected $fillable = [
        'attempts',
        'available_at',
        'payload',
        'queue'
    ];

    /**
     * @param int|null $attempts
     * @param DateTime|null $available_at
     * @param array|null $payload
     * @param string|null $queue
     *
     * @return self
     */
    public static function make(
        ?int $attempts = null,
        ?DateTime $available_at = null,
        ?array $payload = null,
        ?string $queue = null
    ): self {
        $self = new self();

        $self->attempts = $attempts ?? 0;
        $self->available_at = $available_at ?? new DateTime();
        $self->payload = json_encode($payload ?? []);
        $self->queue = $queue ?? 'default';

        return $self;
    }
}
