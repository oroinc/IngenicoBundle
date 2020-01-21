<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Client;

use Ingenico\Connect\OroCommerce\Ingenico\Factory\ClientFactory;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\Sdk\DataObject;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;

/**
 * Responsible for sending request to Ingenico server based on resource and action.
 */
class Client
{
    /** @var ClientFactory */
    private $clientFactory;

    /**
     * @param ClientFactory $clientFactory
     */
    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param PaymentConfigInterface|IngenicoConfig $paymentConfig
     * @param string $resource
     * @param string $action
     * @param array $body
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function send(PaymentConfigInterface $paymentConfig, string $resource, string $action, array $body = [])
    {
        $client = $this->clientFactory->create($paymentConfig);
        $merchant = $client->merchant($paymentConfig->getMerchantId());

        if (!method_exists($merchant, $resource)) {
            throw new \InvalidArgumentException(sprintf('Resource "%s" does not exists', $resource));
        }

        $resourceObject = $merchant->$resource();
        if (!method_exists($resourceObject, $action)) {
            throw new \InvalidArgumentException(sprintf('Action "%s" does not exists', $action));
        }

        // $body is an array of request elements, such as payment ID or SessionRequest object.
        // e.g. ['paymentId' => 103, SessionRequest] or [SessionRequest]
        // so we need to remove string keys in $body before unpacking array
        $body = array_values($body);

        /** @var DataObject $response */
        $response = $resourceObject->$action(...$body);

        return json_decode($response->toJson(), true);
    }
}
