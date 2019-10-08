<?php

namespace Dataloft\Carrental\Lib;

/**
 * @property string UUID
 * @property string vehicle_UUID
 * @property string client_UUID
 * @property string reg_number
 * @property float prepayment
 * @property float prepayment_percent
 * @property string number
 */
class Reservation extends MappingModel
{
    /** @var  Vehicle */
    protected $vehicle;

    protected $attributes_map = [
        'UUID' => 'reserve_id',
        'vehicle_UUID' => 'ID_Avto',
        'client_UUID' => 'client_id',
        'reg_number' => 'gosnomer',
        'prepayment' => 'PrepaymentAmount',
        'prepayment_percent' => 'PrepaymentPercentage',
    ];

    protected $casts = [
        'prepayment' => 'float',
        'prepayment_percent' => 'float',
    ];

    public function setVehicle(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;
        return $this;
    }

    public function toArray()
    {
        $data = parent::toArray();
        $data['vehicle'] = $this->vehicle ? $this->vehicle->toArray() : null;

        return $data;
    }
}
