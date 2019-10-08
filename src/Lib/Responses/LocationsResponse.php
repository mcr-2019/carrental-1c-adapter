<?php

namespace Dataloft\Carrental\Lib\Responses;

use Dataloft\Carrental\Lib\Collection;
use Dataloft\Carrental\Lib\Location;
use Dataloft\Carrental\Lib\Requests\Request;
use stdClass;

class LocationsResponse extends Response
{
    /** @var  Location[]|Collection */
    protected $locations;

    public function __construct(Request $request, stdClass $raw_response)
    {
        parent::__construct($request, $raw_response);

        $this->locations = Collection::make($this->response_data)
            ->transform(function (array $location_data) {
                return new Location($location_data);
            });
    }

    public function getLocations()
    {
        return $this->locations;
    }

    public function toArray()
    {
        return $this->locations->toArray();
    }
}
