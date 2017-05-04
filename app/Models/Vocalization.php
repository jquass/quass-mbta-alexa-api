<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vocalization
 *
 * @property integer $id
 * @property integer $stop_id
 * @property integer $direction_id
 * @property string $map
 */
class Vocalization extends Model
{
    public $timestamps = false;

    protected $table = 'vocalizations';

    public function stop()
    {
        return $this->belongsTo(Stop::class);
    }

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }
}
