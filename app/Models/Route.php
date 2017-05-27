<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Route
 *
 * @property integer $id
 * @property string $mbta_route_id
 * @property string $mbta_route_name
 * @property string $mbta_route_type
 * @property string $mbta_mode_name
 */
class Route extends Model
{
    public $timestamps = false;

    protected $table = 'routes';

    public function vocalizations()
    {
        return $this->hasMany(Vocalization::class);
    }
}
