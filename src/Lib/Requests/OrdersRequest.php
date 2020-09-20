<?php

namespace Dataloft\Carrental\Lib\Requests;

use Dataloft\Carrental\Lib\Client;

class OrdersRequest extends Request
{
    public function setClient(Client $client)
    {
        $this->setRawRequestData([
            'ID_client' => $client->UUID,
            'client_fio' => $client->fio,
            'email' => $client->email,
        ]);
        return $this;
    }

    public function setOrders(array $ids)
    {
      $this->setRawRequestData([
        'ID' => $ids
      ]);
      return $this;
    }
}
