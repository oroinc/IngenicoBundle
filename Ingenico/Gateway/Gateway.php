<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Gateway;

use Ingenico\Connect\OroCommerce\Ingenico\Client\Client;
use Ingenico\Connect\OroCommerce\Ingenico\Converter\RequestBodyConverter;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Ingenico\Connect\OroCommerce\Ingenico\Request\ActionParamsAwareInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Request\RequestRegistry;
use Ingenico\Connect\OroCommerce\Ingenico\Response\Response;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\Sdk\ResponseException;
use Psr\Log\LoggerInterface;

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

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param Client $client
     * @param RequestRegistry $requestRegistry
     * @param RequestBodyConverter $requestBodyConverter
     * @param LoggerInterface $logger
     */
    public function __construct(
        Client $client,
        RequestRegistry $requestRegistry,
        RequestBodyConverter $requestBodyConverter,
        LoggerInterface $logger
    ) {
        $this->client = $client;
        $this->requestRegistry = $requestRegistry;
        $this->requestBodyConverter = $requestBodyConverter;
        $this->logger = $logger;
    }

    /**
     * @param IngenicoConfig $paymentConfig
     * @param string $transactionType
     * @param array $options
     * @param array $actionParams
     *
     * @return Response
     */
    public function request(
        IngenicoConfig $paymentConfig,
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

        try {
            $response = $this->client->send(
                $paymentConfig,
                $request->getResource(),
                $request->getAction(),
                $requestBody
            );
        } catch (ResponseException $e) {
            $response = (array)$e->getResponse()->toObject();
            $this->logger->error('Ingenico response exception', ['exception' => $e, 'response' => $response]);
        }

        return Response::create($response);
    }
}
