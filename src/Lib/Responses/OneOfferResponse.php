<?php

namespace Dataloft\Carrental\Lib\Responses;

use Dataloft\Carrental\Lib\Offer;
use Dataloft\Carrental\Lib\Requests\OneOfferRequest;
use stdClass;

class OneOfferResponse extends OffersResponse
{
    public function __construct(OneOfferRequest $request, stdClass $raw_response)
    {
        parent::__construct($request, $raw_response);

        $this->offers = $this->offers
            ->filter(function (Offer $offer) use ($request) {
                return $offer->vehicle_UUID == $request->getVehicle()->getVehicleUUID();
            })
            ->values();
    }

    public function getOffer()
    {
        return $this->offers->first();
    }
}
