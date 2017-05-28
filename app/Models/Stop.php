<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Stop
 *
 * @property integer $id
 * @property integer $route_id
 * @property integer $direction_id
 * @property integer $mbta_stop_order
 * @property string $mbta_stop_id
 * @property string $mbta_stop_name
 * @property string $mbta_parent_station
 * @property string $mbta_parent_station_name
 */
class Stop extends Model
{
    public $timestamps = false;

    protected $table = 'stops';

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function vocalizations()
    {
        return $this->hasMany(Vocalization::class);
    }
}
