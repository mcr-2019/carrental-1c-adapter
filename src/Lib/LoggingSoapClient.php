<?php

namespace Dataloft\Carrental\Lib;

use Exception;
use Psr\Log\LoggerInterface;
use SoapClient;

class LoggingSoapClient extends SoapClient
{
    /** @var  LoggerInterface */
    protected $logger;

    public function __call($function_name, $arguments)
    {
        $random_request_id = mt_rand(1000, 9999);

        try {
            $result = parent::__call($function_name, $arguments);

            // Prefer decoded response if available
            if (is_object($result) && $result->return) {
                $json_decoded_result = json_decode($result->return, true);
            }
        } catch (Exception $e) {
          $errorMessage = 'Fault code: ' . $e->getCode() . '. ' . $e->getMessage();
          $this->logger->error($errorMessage);
          throw $e;
        }

        return $result;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
