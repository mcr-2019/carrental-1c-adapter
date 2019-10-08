<?php

namespace Dataloft\Carrental\Lib\Responses;

use Dataloft\Carrental\Lib\Collection;
use Dataloft\Carrental\Lib\Equipment;
use Dataloft\Carrental\Lib\Location;
use Dataloft\Carrental\Lib\Requests\Request;
use stdClass;

class EquipmentResponse extends Response
{
    /** @var  Equipment[]|Collection */
    protected $equipment;

    public function __construct(Request $request, stdClass $raw_response)
    {
        parent::__construct($request, $raw_response);

        $this->equipment = Collection::make($this->response_data)
            ->transform(function (array $equipment_data) {
                return new Equipment($equipment_data);
            });
    }

    public function getEquipment()
    {
        return $this->equipment;
    }

    public function toArray()
    {
        return $this->equipment->toArray();
    }
}
