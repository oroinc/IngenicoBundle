<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Client;

use Ingenico\Connect\OroCommerce\Ingenico\Factory\SDKClientFactory;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\Sdk\DataObject;

/**
 * Responsible for sending request to Ingenico server based on resource and action.
 */
class Client
{
    /** @var SDKClientFactory */
    private $sdkClientFactory;

    /**
     * @param SDKClientFactory $sdkClientFactory
     */
    public function __construct(SDKClientFactory $sdkClientFactory)
    {
        $this->sdkClientFactory = $sdkClientFactory;
    }

    /**
     * @param IngenicoConfig $paymentConfig
     * @param string $resource
     * @param string $action
     * @param array $body
     *
     * @return array
     *
     */
    public function send(IngenicoConfig $paymentConfig, string $resource, string $action, array $body = [])
    {
        $sdkClient = $this->sdkClientFactory->create($paymentConfig);
        $merchant = $sdkClient->merchant($paymentConfig->getMerchantId());

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
