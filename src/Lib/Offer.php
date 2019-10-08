<?php

namespace Dataloft\Carrental\Lib;

/**
 * @property string vehicle_UUID
 * @property float cost
 * @property int priority
 *
 * daily-rent-specific
 * @property float deposit
 * @property float price
 * @property float price_discount
 * @property float discount
 * @property float discount_percent
 * @property float discount1
 * @property float discount2
 * @property float period
 * @property float period_days
 * @property float period_hours
 * @property bool promocode_accepted
 *
 * hourly-rent-specific
 * @property int min_hours
 * Закомментировано чтобы не вызывать ошибки дублей (подсветка синтаксиса):
// * @property float price
// * @property int period
// * @property int period_days
// * @property int period_hours
 *
 * transfer-specific
 * @property string transfer_class
 */
class Offer extends MappingModel
{
    const TYPE_DAILY    = 'rent_daily';
    const TYPE_HOURLY   = 'rent_hourly';
    const TYPE_TRANSFER = 'transfer';

    /** @var  Vehicle */
    protected $vehicle;

    protected $attributes_map = [
        'vehicle_UUID' => 'ID',
        'deposit' => 'zalog',
        'cost' => 'Cost',
        'price' => 'Price',
        'price_discount' => 'PriceDiscount',
        'discount' => 'float',
        'discount_percent' => 'float',
        'period' => 'Period',
        'period_days' => 'PeriodDays',
        'period_hours' => 'PeriodTime',
        'discount1' => 'float',
        'discount2' => 'float',
        'min_hours' => 'MinHour',
        'transfer_class' => 'Klasstransfera',
    ];

    protected $casts = [
        'price' => 'float',
        'price_discount' => 'float',
        'promocode_accepted' => 'bool',
        'discount' => 'float',
        'discount_percent' => 'float',
        'discount1' => 'float',
        'discount2' => 'float',
        'cost' => 'float',
        'deposit' => 'float',
        'period' => 'float',
        'period_days' => 'float',
        'period_hours' => 'float',
        'priority' => 'int',
        'min_hours' => 'int',
    ];

    public function setVehicle(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;
        return $this;
    }

    public function getVehicle()
    {
        return $this->vehicle;
    }

    public function toArray()
    {
        $data = parent::toArray();
        $data['vehicle'] = $this->vehicle ? $this->vehicle->toArray() : null;

        return $data;
    }
}
