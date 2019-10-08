<?php

namespace Dataloft\Carrental\Lib\Responses;

use Dataloft\Carrental\Lib\Collection;
use Dataloft\Carrental\Lib\Requests\Request;
use Dataloft\Carrental\Lib\Vehicle;
use stdClass;

class VehiclesResponse extends Response
{
    /** @var  Vehicle[]|Collection */
    protected $vehicles;

    public function __construct(Request $request, stdClass $raw_response)
    {
        parent::__construct($request, $raw_response);

        $this->vehicles = Collection::make($this->response_data)
            ->transform(function (array $vehicle_data) {
                return new Vehicle($vehicle_data);
            });
    }

    public function getVehicles()
    {
        return $this->vehicles;
    }

    public function toArray()
    {
        return $this->vehicles->toArray();
    }
}
