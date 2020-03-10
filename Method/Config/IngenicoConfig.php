<?php

namespace Ingenico\Connect\OroCommerce\Method\Config;

use Oro\Bundle\PaymentBundle\Method\Config\ParameterBag\AbstractParameterBagPaymentConfig;

/**
 * Configuration class which is used to get specific configuration for Ingenico method
 * Usually it has additional get methods for payment type specific configurations
 */
class IngenicoConfig extends AbstractParameterBagPaymentConfig
{
    public const FIELD_API_ENDPOINT_KEY = 'api_endpoint';
    public const FIELD_API_KEY_ID_KEY = 'api_key_id';
    public const FIELD_API_SECRET_KEY = 'api_secret';
    public const FIELD_MERCHANT_ID_KEY = 'merchant_id';
    public const FIELD_ENABLED_PRODUCTS_KEY = 'enabled_products';
    public const FIELD_PAYMENT_ACTION_KEY = 'payment_action';
    public const FIELD_TOKENIZAION_ENABLED_KEY = 'tokenization_enabled';
    public const FIELD_DIRECT_DEBIT_TEXT_KEY = 'direct_debit_text';
    public const FIELD_SOFT_DESCRIPTOR_KEY = 'soft_descriptor';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $parameters)
    {
        parent::__construct($parameters);
    }

    /**
     * @return string|null
     */
    public function getApiEndpoint(): ?string
    {
        return $this->get(self::FIELD_API_ENDPOINT_KEY);
    }

    /**
     * @return string|null
     */
    public function getApiKeyId(): ?string
    {
        return $this->get(self::FIELD_API_KEY_ID_KEY);
    }

    /**
     * @return string|null
     */
    public function getApiSecret(): ?string
    {
        return $this->get(self::FIELD_API_SECRET_KEY);
    }

    /**
     * @return string|null
     */
    public function getMerchantId(): ?string
    {
        return $this->get(self::FIELD_MERCHANT_ID_KEY);
    }

    /**
     * @return array|null
     */
    public function getEnabledProducts(): ?array
    {
        return $this->get(self::FIELD_ENABLED_PRODUCTS_KEY);
    }

    /**
     * @return string|null
     */
    public function getPaymentAction(): ?string
    {
        return $this->get(self::FIELD_PAYMENT_ACTION_KEY);
    }

    /**
     * @return bool|null
     */
    public function isTokenizationEnabled(): ?bool
    {
        return $this->get(self::FIELD_TOKENIZAION_ENABLED_KEY);
    }

    /**
     * @return string|null
     */
    public function getDirectDebitText(): ?string
    {
        return $this->get(self::FIELD_DIRECT_DEBIT_TEXT_KEY);
    }
    /**
     * @return string|null
     */
    public function getSoftDescriptor(): ?string
    {
        return $this->get(self::FIELD_SOFT_DESCRIPTOR_KEY);
    }
}
