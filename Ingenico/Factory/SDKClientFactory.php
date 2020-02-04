<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Factory;

use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\Sdk\Client;
use Ingenico\Connect\Sdk\Communicator;
use Ingenico\Connect\Sdk\CommunicatorConfiguration;
use Ingenico\Connect\Sdk\DefaultConnection;

/**
 * Factory for creating Ingenico client instance from SDK.
 */
class SDKClientFactory
{
    private const INTEGRATOR = 'Oro Inc';

    /**
     * @param IngenicoConfig $paymentConfig
     * @return Client
     */
    public function create(IngenicoConfig $paymentConfig): Client
    {
        $communicatorConfiguration = new CommunicatorConfiguration(
            $paymentConfig->getApiKeyId(),
            $paymentConfig->getApiSecret(),
            $paymentConfig->getApiEndpoint(),
            self::INTEGRATOR
        );
        $connection = new DefaultConnection();
        $communicator = new Communicator($connection, $communicatorConfiguration);

        return new Client($communicator);
    }
}
