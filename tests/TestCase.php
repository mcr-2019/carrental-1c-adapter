<?php

namespace Dataloft\Carrental\Tests;

use Carbon\Carbon;
use Dataloft\Carrental\Connection;
use Dataloft\Carrental\ConnectionManager;
use Dataloft\Carrental\Lib\Client;
use Dataloft\Carrental\Lib\Equipment;
use Dataloft\Carrental\Lib\Location;
use Dataloft\Carrental\Lib\Offer;
use Dataloft\Carrental\Lib\Responses\EquipmentResponse;
use Dataloft\Carrental\Lib\Responses\LocationsResponse;
use Dataloft\Carrental\Lib\Responses\OffersResponse;
use Dataloft\Carrental\Lib\Responses\OneOfferResponse;
use Dataloft\Carrental\Lib\Responses\ReserveResponse;
use Dataloft\Carrental\Lib\Responses\Response;
use Dataloft\Carrental\Lib\Responses\VoidResponse;
use Dotenv\Dotenv;
use Faker\Factory;
use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application as LaravelApplication;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    /** @var  Connection */
    protected $connection;

    public function createApplication()
    {
        $dotenv = new Dotenv(__DIR__ . '/../');
        $dotenv->load();

        $app = new LaravelApplication();
        $app->singleton('config', function () {
            return new Repository([
                'carrental' => [
                    'defaultConnection' => 'testing',
                    'connections' => [
                        'testing' => [
                            'wsdl_base_url' => env('CARRENTAL_DEFAULT_CONNECTION_WSDL_URL'),
                            'login'         => env('CARRENTAL_DEFAULT_CONNECTION_LOGIN'),
                            'password'      => env('CARRENTAL_DEFAULT_CONNECTION_PASSWORD'),
                        ],
                    ],
                ],
            ]);
        });

        return $app;
    }

    public function setUp()
    {
        parent::setUp();

        $cacheStore = new FileStore(new Filesystem(), __DIR__.'/../cache/');
        $cacheRepository = new CacheRepository($cacheStore);

        $logger = new ConsoleLogger(new StreamOutput(
            fopen(__DIR__.'/../log/'.Carbon::now()->format('Y-m-d').'.log', 'a'),
            ConsoleOutput::VERBOSITY_DEBUG
        ));
        $connectionManager = new ConnectionManager($cacheRepository, $logger);
        $this->connection = $connectionManager->connection();
    }

    public function testFullLifeCycle()
    {
        $dateFrom = Carbon::now()->addDays(mt_rand(5, 10))->setTime(12, 0, 0);
        $type = Offer::TYPE_TRANSFER;

        if ($type == Offer::TYPE_TRANSFER) {
            $dateTo = clone $dateFrom;
        } elseif ($type == Offer::TYPE_DAILY) {
            $dateTo = with(clone $dateFrom)->addDays(9);
        } elseif ($type == Offer::TYPE_HOURLY) {
            $dateTo = with(clone $dateFrom)->addHours(9);
        }

        /** @var LocationsResponse $localities */
        $localities = $this->connection->getLocationsRequest()->call();
        /** @var Location $locationFrom */
        $locationFrom = $localities->getLocations()->first(function (Location $location) {
            return $location->UUID == '76149c6a-44f4-11e5-aa23-bcee7b591456';// Симферополь (SIP)
        });
        /** @var Location $locationTo */
        $locationTo = $localities->getLocations()->first(function (Location $location) {
            return $location->UUID == '76149c6f-44f4-11e5-aa23-bcee7b591456';// Ялта
        });

        /** @var OffersResponse $response */
        $response = $this->connection->getOffersRequest($type)
            ->setDateFrom($dateFrom)
            ->setDateTo($dateTo)
            ->setLocationFrom($locationFrom)
            ->setLocationTo($locationTo)
            ->setPromocode('123')
            ->call();

        if ($type == Offer::TYPE_DAILY) {
            /**
             * Пример того, как можно вызывать методы, поддержка которых в адаптере еще не реализована.
             * Данный вызов является полным дублем запроса getOffers представленного выше (для посуточной аренды).
             */
            $this->connection->getRawRequest('ws_avto.SearchForFree?wsdl', 'SearchForFree')
                ->setRawRequestData([
                    'Zapros' => 'Avto',
                    'DataNach' => $dateFrom->format('YmdHi'),
                    'DataKon' => $dateTo->format('YmdHi'),
                    'ID_TownPickUP' => $locationFrom->getLocationUUID(),
                    'ID_TownDropOff' => $locationTo->getLocationUUID(),
                    'promocode' => '123',
                ])
                ->call();
        }

        $this->connection->getLogger()->info('Offers available: '.$response->getOffers()->count());

        /** @var Offer $offer */
        $offer = $response->getOffers()->first();

        $this->connection->getLogger()->info('Promocode accepted: '.print_r($offer->promocode_accepted, true));
        $this->connection->getLogger()->debug('Offer to lock: '.print_r($offer->toArray(), true));

        /** @var VoidResponse $lockResponse */
        $lockResponse = $this->connection->getLockRequest()
            ->setVehicle($offer->getVehicle())
            ->setDateFrom($dateFrom)
            ->setDateTo($dateTo)
            ->call();

        /** @var EquipmentResponse $equipmentResponse */
        $equipmentResponse = $this->connection->getEquipmentRequest()
            ->setDateFrom($dateFrom)
            ->setDateTo($dateTo)
            ->call();
        $chosenEquipment = $equipmentResponse->getEquipment()->take(2)
            ->map(function (Equipment $equipment) {
                return $equipment->setQuantity(mt_rand(2, 5));
            })
            ->values();

        $phone = PhoneNumberUtil::getInstance()->parse('+79876543210', 'RU');
        $client_fio = Factory::create('ru_RU')->name;
        $client_email = Factory::create()->email;

        /** @var OneOfferResponse $actualOffer */
        $actualOffer = $this->connection->getOneOfferRequest($offer->getVehicle(), $type)
            ->setDateFrom($dateFrom)
            ->setDateTo($dateTo)
            ->setLocationFrom($locationFrom)
            ->setLocationTo($locationTo)
            ->setPromocode('123')
            ->call();
        $this->connection->getLogger()->debug('Actual offer (count: '.$actualOffer->getOffers()->count().'): '.print_r($actualOffer->getOffer()->toArray(), true));

        /** @var ReserveResponse $reserveResponse */
        $reserveResponse = $this->connection->getReserveRequest($type)
            ->setVehicle($offer->getVehicle())
            ->setEquipment($chosenEquipment)
            ->setLocationFrom($locationFrom)
            ->setLocationTo($locationTo)
            ->setPhone($phone)
            ->setDateFrom($dateFrom)
            ->setDateTo($dateTo)
            ->setPassport(
                Client::PASSPORT_TYPE_RUSSIAN,
                '9876',
                '543210',
                'УФМС г.Москва',
                '987-123',
                Carbon::now()->subYears(5)
            )
            ->setDriverLicense('1234', '567890')
            ->setRawRequestData([
                'client_fio' => $client_fio,
                'email' => $client_email,
            ])
            ->setPromocode('123')
            ->call();

        $total_cost = $offer->cost + $chosenEquipment
            ->sum(function (Equipment $equipment) {
                return $equipment->price * $equipment->getQuantity();
            });

        if ($type != Offer::TYPE_TRANSFER) {
            // @todo: не реализовано на стороне 1С

            /** @var Response $paymentNotificationResponse */
            $paymentNotificationResponse = $this->connection->getPaymentNotificationRequest()
                ->setReservation($reserveResponse->getReservation())
                ->setPaymentAmount($total_cost)
                ->call();
        }

        // @todo: не реализовано на стороне 1С
//        $client = new Client([
//            'UUID' => $reserveResponse->getReservation()->client_UUID,
//            'client_fio' => $client_fio,
//            'client_email' => $client_email,
//        ]);
//
//        $ordersResponse = $this->connection->getOrdersRequest()
//            ->setClient($client)
//            ->call();
    }
}
