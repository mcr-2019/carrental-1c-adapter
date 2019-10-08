<?php

namespace Dataloft\Carrental\Lib;

/**
 * @property string UUID
 * @property string name
 * @property float price
 * @property int qty
 */
class Equipment extends MappingModel
{
    protected $attributes_map = [
        'UUID' => 'ID',
        'name' => 'Name',
        'price' => 'RentPrice',
    ];

    protected $qty;

    protected $casts = [
        'price' => 'float',
    ];

    public function setQuantity($qty)
    {
        $this->qty = $qty;
        return $this;
    }

    public function getQuantity()
    {
        return $this->qty ?: 1;
    }

    public function toArray()
    {
        $data = parent::toArray();
        $data['qty'] = $this->getQuantity();
        return $data;
    }
}
