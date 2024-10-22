<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $attribute
 * @property string $selector
 * @property string $url
 * @property string $value
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @method static self create(array $attributes = []))
 */
class ScrappedItem extends Model
{
    public const TABLE = 'scrapped_items';

    protected $fillable = [
        'attribute',
        'selector',
        'url',
        'value'
    ];

    /**
     * @param string $attribute
     * @param string $selector
     * @param string $url
     * @param string $value
     *
     * @return self
     */
    public static function make(
        string $attribute,
        string $selector,
        string $url,
        string $value
    ): self {
        return self::create([
            'attribute' => $attribute,
            'selector' => $selector,
            'url' => $url,
            'value' => $value,
        ]);
    }
}
