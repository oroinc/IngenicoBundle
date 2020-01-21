<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Gateway;

use Ingenico\Connect\OroCommerce\Ingenico\Client\Client;
use Ingenico\Connect\OroCommerce\Ingenico\Converter\RequestBodyConverter;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Ingenico\Connect\OroCommerce\Ingenico\Request\ActionParamsAwareInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Request\RequestRegistry;
use Ingenico\Connect\OroCommerce\Ingenico\Response\Response;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;

/**
 * Responsible for converting own request object to Ingenico request,
 * filling it with data and send it to Ingenico server.
 */
class Gateway
{
    /** @var Client */
    private $client;

    /** @var RequestRegistry */
    private $requestRegistry;

    /** @var RequestBodyConverter */
    private $requestBodyConverter;

    /**
     * @param Client $client
     * @param RequestRegistry $requestRegistry
     * @param RequestBodyConverter $requestBodyConverter
     */
    public function __construct(
        Client $client,
        RequestRegistry $requestRegistry,
        RequestBodyConverter $requestBodyConverter
    ) {
        $this->client = $client;
        $this->requestRegistry = $requestRegistry;
        $this->requestBodyConverter = $requestBodyConverter;
    }

    /**
     * @param PaymentConfigInterface $paymentConfig
     * @param string $transactionType
     * @param array $options
     * @param array $actionParams
     *
     * @return Response
     *
     * @throws \JsonException
     * @throws \InvalidArgumentException
     */
    public function request(
        PaymentConfigInterface $paymentConfig,
        string $transactionType,
        array $options = [],
        array $actionParams = []
    ): Response {
        $request = $this->requestRegistry->getRequest($transactionType);

        $optionsResolver = new OptionsResolver();
        $request->configureOptions($optionsResolver);
        $resolvedOptions = $optionsResolver->resolve($options);

        $requestBody = [$this->requestBodyConverter->convert($request, $resolvedOptions)];

        // as some action may need additional parameters such a payment ID
        // so we need to add such information to request body
        if ($request instanceof ActionParamsAwareInterface) {
            $actionParamsResolver = new OptionsResolver();
            $request->configureActionParamsOptions($actionParamsResolver);
            $resolverActionParams = $actionParamsResolver->resolve($actionParams);

            $requestBody = array_merge($resolverActionParams, $requestBody);
        }

        $response = $this->client->send($paymentConfig, $request->getResource(), $request->getAction(), $requestBody);

        return Response::create($response);
    }
}
