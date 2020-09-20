<?php

namespace Dataloft\Carrental\Lib\Requests;

use Carbon\Carbon;
use Dataloft\Carrental\Lib\Interfaces\LocationInterface;												
use Dataloft\Carrental\Lib\Responses\EquipmentResponse;

class EquipmentRequest extends Request
{
    protected $response_class = EquipmentResponse::class;

    protected $cache_lifetime = 30;
    
    public function setLocationFrom(LocationInterface $locationFrom = null)
    {
        $this->setRawRequestData([
            'ID_TownPickUP' => $locationFrom ? $locationFrom->getLocationUUID() : null,
        ]);
        return $this;
    }

    public function setLocationTo(LocationInterface $locationTo = null)
    {
        $this->setRawRequestData([
            'ID_TownDropOff' => $locationTo ? $locationTo->getLocationUUID() : null,
        ]);
        return $this;
    }
    public function setDateFrom(Carbon $dateFrom)
    {
        $this->setRawRequestData([
            'DataNach' => $dateFrom->format('YmdHi'),
        ]);
        return $this;
    }

    public function setDateTo(Carbon $dateTo)
    {
        $this->setRawRequestData([
            'DataKon' => $dateTo->format('YmdHi'),
        ]);
        return $this;
    }
}
