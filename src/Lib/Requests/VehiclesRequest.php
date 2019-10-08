<?php

namespace Dataloft\Carrental\Lib\Requests;

use Dataloft\Carrental\Connection;
use Dataloft\Carrental\Lib\Responses\VehiclesResponse;

class VehiclesRequest extends Request
{
    protected $response_class = VehiclesResponse::class;

    public function __construct(Connection $connection, $wsdl_path, $method, array $request_data = [])
    {
        $this->cache_lifetime = env('CARRENTAL_CACHE_LIFETIME_MINUTES', 30);

        parent::__construct($connection, $wsdl_path, $method, $request_data);
    }
}
