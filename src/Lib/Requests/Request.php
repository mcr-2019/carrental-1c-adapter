<?php

namespace Dataloft\Carrental\Lib\Requests;

use Dataloft\Carrental\Connection;
use Dataloft\Carrental\Lib\Exceptions\BadRequestException;
use Dataloft\Carrental\Lib\Exceptions\ConnectionException;
use Dataloft\Carrental\Lib\Exceptions\InvalidRequestException;
use Dataloft\Carrental\Lib\LoggingSoapClient;
use Dataloft\Carrental\Lib\Responses\Response;
use Exception;
use SoapFault;

use App\Mail\ExceptionHandlerEmail;
use Illuminate\Support\Facades\Mail;

class Request
{
    /** @var  Connection */
    protected $connection;

    /** @var  string */
    protected $wsdl_path;

    /** @var  string */
    protected $method;

    /** @var array */
    protected $request_data = [];

    protected $response_class = Response::class;

    /** @var int Minutes */
    protected $cache_lifetime = 0;

    public function __construct(Connection $connection, $wsdl_path, $method, array $request_data = [])
    {
        $this->connection = $connection;
        $this->wsdl_path = $wsdl_path;
        $this->method    = $method;

        $this->setRawRequestData($request_data);
    }

    public function setRawRequestData(array $request_data = [])
    {
        foreach ($request_data as $attribute => $value) {
            $this->request_data[$attribute] = is_array($value)
                ? json_encode($value, JSON_UNESCAPED_UNICODE)
                : $value;
        }
        return $this;
    }

    /**
     * @return Response
     * @throws SoapFault
     * @throws Exception
     */
    public function call()
    {
        $wsdl_url = rtrim($this->connection->getWsdlBaseURL(), '/') . '/' . ltrim($this->wsdl_path, '/');

        if ($this->cache_lifetime > 0) {
            $cache_key = md5($wsdl_url.':'.serialize($this->request_data));
            $cached_result = $this->connection->getCache()->get($cache_key);
        }

        if (empty($cached_result)) {
            try {
                $soapClient = new LoggingSoapClient($wsdl_url, [
                    'trace' => true,
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'login' => $this->connection->getLogin(),
                    'password' => $this->connection->getPassword(),
                ]);
                $soapClient->setLogger($this->connection->getLogger());

                $raw_response = $soapClient->{$this->method}($this->request_data);
            } catch (SoapFault $e) {
              
              $errorMessage = 'Fault code: ' . $e->faultcode . '. Request failed ('.$wsdl_url.', '.$this->method.'): '.$e->getMessage();
              if (env('APP_ENV', 'dev') == 'production' && strlen($e->getMessage()) < 1200) {
                $adminEmail = env('MYCARRENTAL_ERROR_HANDLER', 'avz@mycarrental.ru');
                Mail::to($adminEmail)
                    ->send(new ExceptionHandlerEmail($errorMessage));
                $adminEmail2 = '12toydolls46@gmail.com';
                Mail::to($adminEmail2)
                    ->send(new ExceptionHandlerEmail($errorMessage));
              }
              $this->connection->getLogger()->error($errorMessage);
              switch ($e->faultcode) {
                  case 'WSDL':
                      throw new ConnectionException($e->faultcode, 'Failed to connect to WSDL: '.$wsdl_url.' '.($this->connection->getLogin() ? '(user '.$this->connection->getLogin().' '.($this->connection->getPassword() ? 'WITH password' : 'WITHOUT password').')' : '(without authorization)'));
                  case 'Client':
                      throw new BadRequestException($e->faultcode, 'Bad request ('.$wsdl_url.', '.$this->method.'), request data:'.PHP_EOL.print_r($this->request_data, true));
                  default:
                      throw $e;
              }
            }

            if (!empty($cache_key) && $this->cache_lifetime > 0) {
                $this->connection->getCache()->put($cache_key, $raw_response, $this->cache_lifetime);
            }
        }
        else {
          //    $this->connection->getLogger()->debug('Cache found ['.$this->method.']');
            $raw_response = $cached_result;
        }

        $response_class = $this->response_class;

        try {
            $response = new $response_class($this, $raw_response);
        } catch (InvalidRequestException $e) {
          
          $errorMessage = 'Fault code: ' . $e->getCode() . '. ' . 'Invalid request ('.$wsdl_url.', '.$this->method.'): '.$e->getMessage().', request data:'.PHP_EOL.print_r($this->request_data, true);
          if (env('APP_ENV', 'dev') == 'production' && strlen($e->getMessage()) < 1200) {
            $adminEmail = env('MYCARRENTAL_ERROR_HANDLER', 'avz@mycarrental.ru');
            Mail::to($adminEmail)
                ->send(new ExceptionHandlerEmail($errorMessage));
            $adminEmail2 = '12toydolls46@gmail.com';
            Mail::to($adminEmail2)
                ->send(new ExceptionHandlerEmail($errorMessage));
          }
          $this->connection->getLogger()->error($errorMessage);
          throw $e;
        }

        return $response;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
