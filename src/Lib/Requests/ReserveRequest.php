<?php

namespace Dataloft\Carrental\Lib\Requests;

use Carbon\Carbon;
use Dataloft\Carrental\Lib\Client;
use Dataloft\Carrental\Lib\Collection;
use Dataloft\Carrental\Lib\Equipment;
use Dataloft\Carrental\Lib\Interfaces\LocationInterface;
use Dataloft\Carrental\Lib\Interfaces\VehicleInterface;
use Dataloft\Carrental\Lib\Location;
use Dataloft\Carrental\Lib\Responses\ReserveResponse;
use Illuminate\Support\Str;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class ReserveRequest extends Request
{
    protected $response_class = ReserveResponse::class;

    protected $request_data = [
        'Zapros' => null,
        'ID_Avto' => null,
        'ID_TownPickUP' => null,
        'ID_TownDropOff' => null,
        'DataNach' => null,
        'DataKon' => null,
        'ID_client' => null,
        'client_fio' => null,
        'email' => null,
        'comment' => null,
        'tel' => null,
        'tel_kod_country' => null,
        'tel_kod_city' => null,
        'tel_nomer' => null,
        'pasport_type' => null,
        'pasport_n' => null,
        'pasport_s' => null,
        'pasport_data1' => null,
        'pasport_data2' => null,
        'pasport_issued' => null,
        'pasport_issuedcode' => null,
        'drivinglicense_n' => null,
        'drivinglicense_s' => null,
        'Equipment' => '[]',
        'Documents' => '[]',
        'PromoCode' => null,
        'Roundtrip' => null,
        'RequiresConfirmation' => 0,
        'Roundtrip' => null
    ];

    public function setVehicle(VehicleInterface $vehicle)
    {
        $this->setRawRequestData([
            'ID_Avto' => $vehicle->getVehicleUUID(),
        ]);
        return $this;
    }

    public function setEquipment(Collection $equipmentList)
    {
        $this->setRawRequestData([
            'Equipment' => $equipmentList
                ->map(function (Equipment $equipment) {
                    return [
                        'ID' => $equipment->UUID,
                        'Quantity' => $equipment->getQuantity(),
                        'Name' => $equipment->name,
                        'RentPrice' => $equipment->price,
                    ];
                })
                ->values()
                ->toArray(),
        ]);
        return $this;
    }

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

    public function setPhone(PhoneNumber $phone)
    {
        // +79876543210
        $phone_e164 = PhoneNumberUtil::getInstance()->format($phone, PhoneNumberFormat::E164);
        $this->setRawRequestData([
            'tel'             => Str::substr($phone_e164, 1),// 79876543210
            'tel_kod_country' => Str::substr($phone_e164, 1, 1),// 7
            'tel_kod_city'    => Str::substr($phone_e164, 2, 3),// 987
            'tel_nomer'       => Str::substr($phone_e164, 5),// 6543210
        ]);
        return $this;
    }

    public function setDriverLicense($series = '', $number = '')
    {
        $this->setRawRequestData([
            'drivinglicense_s' => $series,
            'drivinglicense_n' => $number,
        ]);
        return $this;
    }

    public function setPassport($type = Client::PASSPORT_TYPE_RUSSIAN, $series = '', $number = '', $issued_by = '', $issued_by_code = '', Carbon $issued_at = null, Carbon $valid_to = null)
    {
        $this->setRawRequestData([
            'pasport_type' => $type,
            'pasport_s' => $series,
            'pasport_n' => $number,
            'pasport_data1' => $issued_at ? $issued_at->format('Ymd') : null,
            'pasport_data2' => $valid_to ? $valid_to->format('Ymd') : null,
            'pasport_issued' => $issued_by,
            'pasport_issuedcode' => $issued_by_code,
        ]);
        return $this;
    }

    public function setPromocode($promocode)
    {
        $this->setRawRequestData(['PromoCode' => $promocode]);
        return $this;
    }

    public function setRequiresConfirmation($requiresConfirmation)
    {
        $this->setRawRequestData(['RequiresConfirmation' => (int) (bool) $requiresConfirmation]);
        return $this;
    }
}
