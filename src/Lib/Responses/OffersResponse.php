<?php

namespace Dataloft\Carrental\Lib\Responses;

use Dataloft\Carrental\Lib\Collection;
use Dataloft\Carrental\Lib\Offer;
use Dataloft\Carrental\Lib\Requests\Request;
use Dataloft\Carrental\Lib\Vehicle;
use stdClass;

class OffersResponse extends Response
{
    /** @var  Offer[]|Collection */
    protected $offers;

    public function __construct(Request $request, stdClass $raw_response)
    {
        parent::__construct($request, $raw_response);

        /** @var VehiclesResponse $vehiclesResponse */
        $vehiclesResponse = $this->request->getConnection()->getVehiclesRequest()->call();
        $vehicles = $vehiclesResponse->getVehicles();

        $this->offers = Collection::make($this->response_data)
            ->transform(function (array $offer_data) use ($vehicles) {
                $offer = new Offer($offer_data);
                $vehicle = $vehicles
                    ->filter(function (Vehicle $vehicle) use ($offer) {
                        return $vehicle->UUID === $offer->vehicle_UUID;
                    })
                    ->first();

                if ($vehicle) {
                    $offer->setVehicle($vehicle);
                }

                return $offer;
            });

        $this->request->getConnection()->getLogger()
            ->debug('All offers ('.$this->offers->count().'): '.print_r($this->offers->map(function (Offer $offer) { return $offer->cost.($offer->getVehicle() ? ', '.$offer->getVehicle()->title.' ['.$offer->getVehicle()->reg_number.'], priority '.$offer->priority : ', NO VEHICLE'); })->toArray(), true));

        $this->offers = $this->offers
            ->filter(function (Offer $offer) {
                if (
                    is_null($offer->getVehicle()) ||
                    $offer->getVehicle()->trashed ||
                    $offer->getVehicle()->reservation_denied
                ) {
                    return false;
                }

                return true;
            })
            ->values();

        $this->request->getConnection()->getLogger()
            ->debug('Active offers ('.$this->offers->count().'): '.print_r($this->offers->map(function (Offer $offer) { return $offer->cost.($offer->getVehicle() ? ', '.$offer->getVehicle()->title.' ['.$offer->getVehicle()->reg_number.'], priority '.$offer->priority : ', NO VEHICLE'); })->toArray(), true));

        $this->offers = $this->offers
            ->groupBy(function (Offer $offer) {
                return $offer->getVehicle()->title.':'.$offer->cost;
            })
            ->transform(function (Collection $offersByNameAndPrice) {
                $offersByPriority = $offersByNameAndPrice
                    ->groupBy(function (Offer $offer) {
                        return 'priority_'.$offer->priority;
                    });

                return $offersByPriority->has('priority_1')
                    ? $offersByPriority->get('priority_1')->first()
                    : $offersByPriority->get('priority_0')->first();
            })
            ->values();

        $this->request->getConnection()->getLogger()
            ->debug('Distinct offers ('.$this->offers->count().'): '.print_r($this->offers->map(function (Offer $offer) { return $offer->cost.($offer->getVehicle() ? ', '.$offer->getVehicle()->title.' ['.$offer->getVehicle()->reg_number.'], priority '.$offer->priority : ', NO VEHICLE'); })->toArray(), true));
    }

    public function isPromocodeAccepted()
    {
        return $this->offers->filter(function (Offer $offer) {
                return $offer->promocode_accepted;
            })->count() > 0;
    }

    public function getOffers()
    {
        return $this->offers;
    }

    public function toArray()
    {
        return $this->offers->toArray();
    }
}
