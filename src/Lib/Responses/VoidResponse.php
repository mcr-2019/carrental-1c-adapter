<?php

namespace Dataloft\Carrental\Lib\Responses;

class VoidResponse extends Response
{
    public function isSuccess()
    {
        $true_values = ['true', true, '1', 1];
        return in_array($this->response_data['result'], $true_values, true);
    }
}
