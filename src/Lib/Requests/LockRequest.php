<?php

namespace Dataloft\Carrental\Lib\Requests;

use Carbon\Carbon;
use Dataloft\Carrental\Lib\Interfaces\VehicleInterface;
use Dataloft\Carrental\Lib\Responses\VoidResponse;

class LockRequest extends Request
{
    protected $response_class = VoidResponse::class;

    public function setVehicle(VehicleInterface $vehicle)
    {
        $this->setRawRequestData([
            'ID_Avto' => $vehicle->getVehicleUUID(),
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
