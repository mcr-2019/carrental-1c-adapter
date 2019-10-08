<?php

namespace Dataloft\Carrental;

use Exception;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Store;
use Psr\Log\LoggerInterface;

class ConnectionManager
{
    /** @var  Store */
    protected $cache;

    /** @var  LoggerInterface */
    protected $logger;

    /** @var  Connection */
    protected $defaultConnection = null;

    public function __construct(Repository $cache, LoggerInterface $logger)
    {
        $this->cache = $cache->getStore();
        $this->logger = $logger;

        $default_connection_name = config('carrental.defaultConnection');
        if (!empty($default_connection_name)) {
            $default_connection_config = config('carrental.connections.'.$default_connection_name);
            if (!empty($default_connection_config)) {
                $this->defaultConnection = new Connection($this->cache, $this->logger,
                    array_get($default_connection_config, 'wsdl_base_url'),
                    array_get($default_connection_config, 'login'),
                    array_get($default_connection_config, 'password')
                );
            }
        }
    }

    public function connection($wsdl_base_url = null, $login = null, $password = null)
    {
        if (empty($wsdl_base_url)) {
            if ($this->defaultConnection) {
                return $this->defaultConnection;
            }
            throw new Exception('No connection defined');
        }

        return new Connection($this->cache, $this->logger, $wsdl_base_url, $login, $password);
    }
}
