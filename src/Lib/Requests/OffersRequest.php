<?php

namespace Dataloft\Carrental\Lib\Requests;

use Carbon\Carbon;
use Dataloft\Carrental\Connection;
use Dataloft\Carrental\Lib\Interfaces\LocationInterface;
use Dataloft\Carrental\Lib\Responses\OffersResponse;

class OffersRequest extends Request
{
    protected $response_class = OffersResponse::class;

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

    public function setDateFrom(Carbon $dateFrom = null)
    {
        $this->setRawRequestData([
            'DataNach' => $dateFrom ? $dateFrom->format('YmdHi') : null,
        ]);
        return $this;
    }

    public function setDateTo(Carbon $dateTo = null)
    {
        $this->setRawRequestData([
            'DataKon' => $dateTo ? $dateTo->format('YmdHi') : null,
        ]);
        return $this;
    }

    public function setPromocode($promocode)
    {
        $this->setRawRequestData(['PromoCode' => $promocode]);
        return $this;
    }
}
