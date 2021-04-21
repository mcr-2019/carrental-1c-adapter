<?php

namespace Dataloft\Carrental\Lib;

use Carbon\Carbon;
use Dataloft\Carrental\Lib\Interfaces\VehicleInterface;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @property string UUID
 * @property string reg_number
 * @property string title
 * @property string made_year
 * @property float deposit
 * @property string color
 * @property string kpp
 * @property string fuel
 * @property int engine_volume
 * @property int power
 * @property string vin_number
 * @property string engine_number
 * @property string body_number
 * @property string frame_number
 * @property float price
 * @property float skidka_1
 * @property float skidka_2
 * @property int milesage
 * @property Carbon milesage_at
 * @property string dostupnadlyabroni_s 
 * @property string auto_class
 * @property bool reservation_denied
 * @property bool trashed
 * @property string WebColor
 * @property string WebColorID
 * @property int seats
 * @property int cargovolume
 * @property int modified
 * @property int is_hourly_rent_available
 * @property int is_transfer_available
 * @property string transfer_class
 */
class Vehicle extends MappingModel implements VehicleInterface
{
    protected $attributes_map = [
        'UUID' => ['auto_key', 'ID'],
        'title' => 'naimenovanie',
        'deposit' => 'zalog',
        'power' => 'moshhnost',
        'auto_class' => 'klassavtomobilya',
        'made_year' => 'godvypuska',
        'color' => 'cvet',
        'milesage' => 'probeg',
        'milesage_at' => 'probegnadatu',
        'engine_volume' => 'dvigatel',
        'reg_number' => 'gosnomer',
        'vin_number' => 'vin',
        'body_number' => 'nomerkuzova',
        'frame_number' => 'nomershassi',
        'engine_number' => 'nomerdvigatelya',
        'trashed' => 'Del',
        'fuel' => 'toplivo',
        'seats' => 'numberofseats',
        'drive_type_name' => 'DriveUnit',
        'is_transfer_available' => 'Transfer',
        'is_hourly_rent_available' => 'HourlyRental',
        'modified' => 'modifed',
        'transfer_class' => 'Klasstransfera',
        'reservation_denied' => 'zapretbronirovaniya',
        'price' => 'stoimost',
    ];

    protected $casts = [
        'price' => 'float',
        'made_year' => 'int',
        'deposit' => 'float',
        'power' => 'int',
        'milesage' => 'int',
        'milesage_at' => 'date',
        'is_transfer_available' => 'bool',
        'is_hourly_rent_available' => 'bool',
        'modified' => 'bool',
        'reservation_denied' => 'bool',
        'skidka_1' => 'float',
        'skidka_2' => 'float',
        'trashed' => 'bool',
        'engine_volume' => 'float',
        'seats' => 'int',
        'cargovolume' => 'int',
    ];

    public function getVehicleUUID()
    {
        return array_get($this->attributes, 'UUID');
    }
}
