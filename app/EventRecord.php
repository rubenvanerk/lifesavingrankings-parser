<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\EventRecord
 *
 * @property int $id
 * @property int $gender
 * @property string $time
 * @property int $event_id
 * @property-read \App\Event $event
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\EventRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\EventRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\EventRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\EventRecord whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\EventRecord whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\EventRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\EventRecord whereTime($value)
 */
class EventRecord extends Model
{
    protected $table = 'rankings_eventrecord';
    public $timestamps = false;


    public function event(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
