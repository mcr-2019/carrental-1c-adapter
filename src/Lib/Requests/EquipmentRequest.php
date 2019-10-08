<?php

namespace Dataloft\Carrental\Lib\Requests;

use Carbon\Carbon;
use Dataloft\Carrental\Lib\Responses\EquipmentResponse;

class EquipmentRequest extends Request
{
    protected $response_class = EquipmentResponse::class;

    protected $cache_lifetime = 30;

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
