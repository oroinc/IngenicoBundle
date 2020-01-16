<?php

namespace Ingenico\Connect\OroCommerce\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * IngenicoSettings Entity.
 * @ORM\Entity(repositoryClass="Ingenico\Connect\OroCommerce\Entity\Repository\IngenicoSettingsRepository")
 */
class IngenicoSettings extends Transport
{
    public const API_KEY_ID = 'api_key_id';
    public const API_SECRET = 'api_secret';
    public const API_ENDPOINT = 'api_endpoint';
    public const MERCHANT_ID = 'merchant_id';
    public const ENABLED_PRODUCTS = 'enabled_products';
    public const PAYMENT_ACTION = 'payment_action';

    /**
     * @var ParameterBag
     */
    protected $settings;

    /**
     * @var string
     *
     * @ORM\Column(name="ingenico_api_key_id", type="string", length=255, nullable=true)
     */
    protected $apiKeyId;

    /**
     * @var string
     *
     * @ORM\Column(name="ingenico_api_secret", type="crypted_string", length=255, nullable=true)
     */
    protected $apiSecret;

    /**
     * @var string
     *
     * @ORM\Column(name="ingenico_api_endpoint", type="text", length=255, nullable=true)
     */
    protected $apiEndpoint;

    /**
     * @var string
     *
     * @ORM\Column(name="ingenico_merchant_id", type="string", length=255, nullable=true)
     */
    protected $merchantId;

    /**
     * @var array
     *
     * @ORM\Column(name="ingenico_enabled_products", type="array", nullable=true)
     */
    protected $enabledProducts = [];

    /**
     * @var string
     *
     * @ORM\Column(name="ingenico_payment_action", type="string", length=255, nullable=true)
     */
    protected $paymentAction;

    /**
     * {@inheritdoc}
     */
    public function getSettingsBag()
    {
        if (null === $this->settings) {
            $this->settings = new ParameterBag([
                self::API_KEY_ID => $this->getApiKeyId(),
                self::API_SECRET => $this->getApiSecret(),
                self::API_ENDPOINT => $this->getApiEndpoint(),
                self::MERCHANT_ID => $this->getMerchantId(),
                self::ENABLED_PRODUCTS => $this->getEnabledProducts(),
                self::PAYMENT_ACTION => $this->getPaymentAction(),
            ]);
        }

        return $this->settings;
    }

    /**
     * @return string
     */
    public function getApiKeyId()
    {
        return $this->apiKeyId;
    }

    /**
     * @param string $apiKeyId
     * @return IngenicoSettings
     */
    public function setApiKeyId($apiKeyId): IngenicoSettings
    {
        $this->apiKeyId = $apiKeyId;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiSecret()
    {
        return $this->apiSecret;
    }

    /**
     * @param string $apiSecret
     *
     * @return IngenicoSettings
     */
    public function setApiSecret($apiSecret): IngenicoSettings
    {
        $this->apiSecret = $apiSecret;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiEndpoint()
    {
        return $this->apiEndpoint;
    }

    /**
     * @param string $apiEndpoint
     *
     * @return IngenicoSettings
     */
    public function setApiEndpoint($apiEndpoint): IngenicoSettings
    {
        $this->apiEndpoint = $apiEndpoint;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @param string $merchantId
     *
     * @return IngenicoSettings
     */
    public function setMerchantId($merchantId): IngenicoSettings
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    /**
     * @return array
     */
    public function getEnabledProducts()
    {
        return $this->enabledProducts;
    }

    /**
     * @param array $enabledProducts
     *
     * @return IngenicoSettings
     */
    public function setEnabledProducts($enabledProducts): IngenicoSettings
    {
        $this->enabledProducts = $enabledProducts;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentAction()
    {
        return $this->paymentAction;
    }

    /**
     * @param string $paymentAction
     *
     * @return IngenicoSettings
     */
    public function setPaymentAction($paymentAction): IngenicoSettings
    {
        $this->paymentAction = $paymentAction;

        return $this;
    }
}
