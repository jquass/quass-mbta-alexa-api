<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Direction
 *
 * @property integer $id
 * @property integer $route_id
 * @property string $mbta_direction_id
 * @property string $mbta_direction_name
 */
class Direction extends Model
{
    public $timestamps = false;

    protected $table = 'directions';

    public function vocalizations()
    {
        return $this->hasMany(Vocalization::class);
    }
}
