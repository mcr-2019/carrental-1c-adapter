<?php

namespace Dataloft\Carrental\Lib\Responses;

use Dataloft\Carrental\Lib\Exceptions\InvalidRequestException;
use Dataloft\Carrental\Lib\Requests\Request;
use Illuminate\Contracts\Support\Arrayable;
use stdClass;

class Response implements Arrayable
{
    /** @var  array */
    protected $response_data = [];

    /** @var  Request */
    protected $request;

    public function __construct(Request $request, stdClass $raw_response)
    {
        $this->request = $request;
        $this->response_data = json_decode($raw_response->return, true);

        if (isset($this->response_data['result']) && $this->response_data['result'] !== 'true') {
            throw new InvalidRequestException(array_get($this->response_data, 'error', ''), array_get($this->response_data, 'faultcode', 0));
        }
    }

    public function toArray()
    {
        return $this->response_data;
    }

    public function getRawResponseData()
    {
        return $this->response_data;
    }
}
