<?php

namespace Dataloft\Carrental;

use Dataloft\Carrental\Lib\Interfaces\VehicleInterface;
use Dataloft\Carrental\Lib\Offer;
use Dataloft\Carrental\Lib\Requests\EquipmentRequest;
use Dataloft\Carrental\Lib\Requests\LocationsRequest;
use Dataloft\Carrental\Lib\Requests\LockRequest;
use Dataloft\Carrental\Lib\Requests\OffersRequest;
use Dataloft\Carrental\Lib\Requests\OneOfferRequest;
use Dataloft\Carrental\Lib\Requests\OrdersRequest;
use Dataloft\Carrental\Lib\Requests\PaymentNotificationRequest;
use Dataloft\Carrental\Lib\Requests\Request;
use Dataloft\Carrental\Lib\Requests\ReserveRequest;
use Dataloft\Carrental\Lib\Requests\VehiclesRequest;
use Dataloft\Carrental\Lib\Responses\OneOfferResponse;
use Exception;
use Illuminate\Contracts\Cache\Store;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

class Connection
{
    /** @var  Store */
    protected $cache;

    /** @var  LoggerInterface */
    protected $logger;

    protected $wsdl_base_url;

    /** @var  string */
    protected $login;

    /** @var  string */
    protected $password;

    public function __construct(Store $cache, LoggerInterface $logger, $wsdl_base_url, $login, $password)
    {
        if (empty($wsdl_base_url)) {
            throw new Exception('WSDL base URL is not defined');
        }

        $this->cache         = $cache;
        $this->logger        = $logger;
        $this->wsdl_base_url = $wsdl_base_url;
        $this->login         = $login;
        $this->password      = $password;
    }

    public function getWsdlBaseURL()
    {
        return $this->wsdl_base_url;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function getVehiclesRequest()
    {
        return new VehiclesRequest($this, 'ws_avto.Avto?wsdl', 'DannyePoAvto');
    }

    public function getLocationsRequest()
    {
        return new LocationsRequest($this, 'web_town.1cws?wsdl', 'GetTown');
    }

    public function getOffersRequest($type = Offer::TYPE_DAILY)
    {
        switch ($type) {
            case Offer::TYPE_DAILY:
                return new OffersRequest($this, 'ws_avto.SearchForFree?wsdl', 'SearchForFree', ['Zapros' => 'Rent']);
            case Offer::TYPE_HOURLY:
                return new OffersRequest($this, 'ws_avto.SearchForFree?wsdl', 'SearchForFree', ['Zapros' => 'HourlyRental']);
                break;
            case Offer::TYPE_TRANSFER:
                return new OffersRequest($this, 'ws_avto.SearchForFree?wsdl', 'FreeForTransfer', ['Zapros' => 'Transfer']);
                break;
            default:
                throw new InvalidArgumentException('Invalid offer type: '.print_r($type, true));
        }
    }

    /** @return OneOfferRequest */
    public function getOneOfferRequest(VehicleInterface $vehicle, $type = Offer::TYPE_DAILY)
    {
        switch ($type) {
            case Offer::TYPE_DAILY:
                return (new OneOfferRequest($this, 'ws_avto.SearchForFree?wsdl', 'SearchForFree', ['Zapros' => 'Rent']))
                    ->setVehicle($vehicle);
            case Offer::TYPE_HOURLY:
                return (new OneOfferRequest($this, 'ws_avto.SearchForFree?wsdl', 'SearchForFree', ['Zapros' => 'HourlyRental']))
                    ->setVehicle($vehicle);
                break;
            case Offer::TYPE_TRANSFER:
                return (new OneOfferRequest($this, 'ws_avto.SearchForFree?wsdl', 'FreeForTransfer', ['Zapros' => 'Transfer']))
                    ->setVehicle($vehicle);
                break;
            default:
                throw new InvalidArgumentException('Invalid offer type: '.print_r($type, true));
        }
    }

    public function getEquipmentRequest()
    {
        return new EquipmentRequest($this, 'ws_avto.SearchForFree?wsdl', 'SearchForFree', ['Zapros' => 'Equipment']);
    }

    public function getLockRequest()
    {
        return new LockRequest($this, 'ws_avto.locking?wsdl', 'locking', ['block' => 1]);
    }

    public function getReserveRequest($type = Offer::TYPE_DAILY)
    {
        switch ($type) {
            case Offer::TYPE_DAILY:
                return new ReserveRequest($this, 'ws_avto.Reserve?wsdl', 'Reserve', ['Zapros' => 'Reserve']);
                break;
            case Offer::TYPE_HOURLY:
                return new ReserveRequest($this, 'ws_avto.Reserve?wsdl', 'Reserve', ['Zapros' => 'HourlyRent']);
                break;
            case Offer::TYPE_TRANSFER:
                return new ReserveRequest($this, 'ws_avto.Reserve?wsdl', 'Reserve', ['Zapros' => 'Transfer']);
                break;
            default:
                throw new InvalidArgumentException('Invalid offer type: '.print_r($type, true));
        }
    }

    public function getPaymentNotificationRequest()
    {
        return new PaymentNotificationRequest($this, 'ws_avto.payment?wsdl', 'Payment');
    }

    public function getOrdersRequest()
    {
        return new OrdersRequest($this, 'ws_avto.Orders?wsdl', 'Orders');
    }

    public function getOrdersByIdsRequest()
    {
        return new OrdersRequest($this, 'ws_avto.Orders?wsdl', 'OrdersID');
    }

    public function getRawRequest($wsdl_path, $method, array $request_data = [])
    {
        return new Request($this, $wsdl_path, $method, $request_data);
    }
}
