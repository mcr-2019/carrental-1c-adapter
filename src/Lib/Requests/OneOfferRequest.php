<?php

namespace Dataloft\Carrental\Lib\Requests;

use Dataloft\Carrental\Lib\Interfaces\VehicleInterface;
use Dataloft\Carrental\Lib\Responses\OneOfferResponse;

class OneOfferRequest extends OffersRequest
{
    protected $response_class = OneOfferResponse::class;

    /** @var  VehicleInterface */
    protected $vehicle;

    public function setVehicle(VehicleInterface $vehicle)
    {
        $this->vehicle = $vehicle;
        return $this;
    }

    public function getVehicle()
    {
        return $this->vehicle;
    }
}
