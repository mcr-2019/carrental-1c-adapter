<?php

namespace Dataloft\Carrental\Lib;

use Dataloft\Carrental\Lib\Interfaces\LocationInterface;

/**
 * @property string UUID
 * @property string name
 * @property float cost
 * @property float geo_lat
 * @property float geo_lng
 * @property string project
 */
class Location extends MappingModel implements LocationInterface
{
    protected $attributes_map = [
        'UUID' => 'ID',
        'name' => 'Town',
        'cost' => 'Cost',
        'geo_lat' => 'GPS_N',
        'geo_lng' => 'GPS_E',
        'project' => 'Project',
        'days_for_serve' => 'DaysForServe',
        'days_for_return' => 'DaysForReturn'
    ];

    protected $casts = [
        'cost' => 'float',
        'geo_lat' => 'float',
        'geo_lng' => 'float',
    ];

    public function getLocationUUID()
    {
        return array_get($this->attributes, 'UUID');
    }
}
