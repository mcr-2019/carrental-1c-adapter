<?php

namespace Dataloft\Carrental\Lib\Responses;

use Dataloft\Carrental\Lib\Requests\Request;
use Dataloft\Carrental\Lib\Reservation;
use Dataloft\Carrental\Lib\Vehicle;
use stdClass;

class ReserveResponse extends Response
{
    /** @var  Reservation */
    protected $reservation;

    public function __construct(Request $request, stdClass $raw_response)
    {
        parent::__construct($request, $raw_response);

        $this->reservation = new Reservation($this->response_data);

        /** @var VehiclesResponse $vehiclesResponse */
        $vehiclesResponse = $this->request->getConnection()->getVehiclesRequest()->call();
        $vehicles = $vehiclesResponse->getVehicles();
        $vehicle = $vehicles
            ->filter(function (Vehicle $vehicle) {
                return $vehicle->UUID === $this->reservation->vehicle_UUID;
            })
            ->first();

        if ($vehicle) {
            $this->reservation->setVehicle($vehicle);
        }
    }

    public function getReservation()
    {
        return $this->reservation;
    }
}
