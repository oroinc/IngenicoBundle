<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Factory;

use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\Sdk\Client;
use Ingenico\Connect\Sdk\Communicator;
use Ingenico\Connect\Sdk\CommunicatorConfiguration;
use Ingenico\Connect\Sdk\DefaultConnection;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;

/**
 * Factory for creating Ingenico client instance.
 */
class ClientFactory
{
    private const INTEGRATOR = 'Oro Inc';

    /**
     * @param PaymentConfigInterface|IngenicoConfig $paymentConfig
     *
     * @return Client
     */
    public function create(PaymentConfigInterface $paymentConfig): Client
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
