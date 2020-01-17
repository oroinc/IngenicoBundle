<?php

namespace Ingenico\Connect\OroCommerce\Method\Config\Factory;

use Ingenico\Connect\OroCommerce\Entity\IngenicoSettings;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Oro\Bundle\IntegrationBundle\Generator\IntegrationIdentifierGeneratorInterface;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;

/**
 * Creates instances of configurations for Ingenico payment method
 */
class IngenicoConfigFactory
{
    /**
     * @var IntegrationIdentifierGeneratorInterface
     */
    protected $identifierGenerator;

    public function __construct(
        IntegrationIdentifierGeneratorInterface $identifierGenerator
    ) {
        $this->identifierGenerator = $identifierGenerator;
    }

    /**
     * @param IngenicoSettings $settings
     * @return PaymentConfigInterface
     */
    public function createConfig(IngenicoSettings $settings): PaymentConfigInterface
    {
        $params = [];
        $channel = $settings->getChannel();

        $params[IngenicoConfig::FIELD_LABEL] = $channel->getName();
        $params[IngenicoConfig::FIELD_SHORT_LABEL] = $channel->getName();
        $params[IngenicoConfig::FIELD_ADMIN_LABEL] = $channel->getName();
        $params[IngenicoConfig::FIELD_API_ENDPOINT_KEY] = (string)$settings->getApiEndpoint();
        $params[IngenicoConfig::FIELD_API_KEY_ID_KEY] = (string)$settings->getApiKeyId();
        $params[IngenicoConfig::FIELD_API_SECRET_KEY] = (string)$settings->getApiSecret();
        $params[IngenicoConfig::FIELD_MERCHANT_ID_KEY] = (string)$settings->getMerchantId();
        $params[IngenicoConfig::FIELD_ENABLED_PRODUCTS_KEY] = is_array($settings->getEnabledProducts()) ?
            $settings->getEnabledProducts() : [];
        $params[IngenicoConfig::FIELD_PAYMENT_ACTION_KEY] = (string)$settings->getPaymentAction();
        $params[IngenicoConfig::FIELD_PAYMENT_METHOD_IDENTIFIER] =
            $this->identifierGenerator->generateIdentifier($channel);

        return new IngenicoConfig($params);
    }
}
