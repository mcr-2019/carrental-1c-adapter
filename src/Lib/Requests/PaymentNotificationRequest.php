<?php

namespace Dataloft\Carrental\Lib\Requests;

use Dataloft\Carrental\Lib\Reservation;
use Dataloft\Carrental\Lib\Responses\VoidResponse;

class PaymentNotificationRequest extends Request
{
    protected $response_class = VoidResponse::class;

    protected $request_data = [
        'ID_reserve' => null,
        'sum' => null,
        'kassa' => '',
    ];

    public function setReservation(Reservation $reservation)
    {
        $this->setRawRequestData(['ID_reserve' => $reservation->UUID]);
        return $this;
    }

    public function setPaymentAmount($payment_amount)
    {
        $this->setRawRequestData(['sum' => $payment_amount]);
        return $this;
    }

    public function setPaymentGatewayID($payment_gateway_id)
    {
        $this->setRawRequestData(['kassa' => $payment_gateway_id]);
        return $this;
    }
}
