<?php

namespace Dataloft\Carrental\Lib;

use Exception;
use Psr\Log\LoggerInterface;
use SoapClient;

use App\Mail\ExceptionHandlerEmail;
use Illuminate\Support\Facades\Mail;

class LoggingSoapClient extends SoapClient
{
    /** @var  LoggerInterface */
    protected $logger;

    public function __call($function_name, $arguments)
    {
        $random_request_id = mt_rand(1000, 9999);

      //  $this->logger->debug('REQUEST #'.$random_request_id.' ('.$function_name.'): '.print_r($arguments, true));

        try {
            $result = parent::__call($function_name, $arguments);

            // Prefer decoded response if available
            if (is_object($result) && $result->return) {
                $json_decoded_result = json_decode($result->return, true);

              //    $this->logger->debug('RESPONSE #'.$random_request_id.': '.print_r($json_decoded_result ?: $result, true));
            }
        } catch (Exception $e) {
          $errorMessage = 'Fault code: ' . $e->getCode() . '. ' . $e->getMessage();
          if (env('APP_ENV', 'dev') == 'production') {
            $adminEmail = env('MYCARRENTAL_ERROR_HANDLER', 'avz@mycarrental.ru');
            Mail::to($adminEmail)
                ->send(new ExceptionHandlerEmail($errorMessage));
            $adminEmail2 = '12toydolls46@gmail.com';
            Mail::to($adminEmail2)
                ->send(new ExceptionHandlerEmail($errorMessage));
          }
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
