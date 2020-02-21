<?php

namespace Ingenico\Connect\OroCommerce\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Ingenico payment integration settings entity
 *
 * @ORM\Entity(repositoryClass="Ingenico\Connect\OroCommerce\Entity\Repository\IngenicoSettingsRepository")
 */
class IngenicoSettings extends Transport
{
    public const LABELS_KEY = 'labels';
    public const SHORT_LABELS_KEY = 'short_labels';
    public const API_KEY_ID = 'api_key_id';
    public const API_SECRET = 'api_secret';
    public const API_ENDPOINT = 'api_endpoint';
    public const MERCHANT_ID = 'merchant_id';
    public const ENABLED_PRODUCTS = 'enabled_products';
    public const PAYMENT_ACTION = 'payment_action';
    public const TOKENIZATION_ENABLED = 'tokenization_enabled';
    public const DIRECT_DEBIT_TEXT = 'direct_debit_text';

    /**
     * @var ParameterBag
     */
    private $settings;


    /**
     * @var Collection|LocalizedFallbackValue[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue",
     *      cascade={"ALL"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinTable(
     *      name="ingenico_label",
     *      joinColumns={
     *          @ORM\JoinColumn(name="transport_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="localized_value_id", referencedColumnName="id", onDelete="CASCADE", unique=true)
     *      }
     * )
     */
    private $labels;

    /**
     * @var Collection|LocalizedFallbackValue[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue",
     *      cascade={"ALL"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinTable(
     *      name="ingenico_short_label",
     *      joinColumns={
     *          @ORM\JoinColumn(name="transport_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="localized_value_id", referencedColumnName="id", onDelete="CASCADE", unique=true)
     *      }
     * )
     */
    private $shortLabels;

    /**
     * @var string
     *
     * @ORM\Column(name="ingenico_api_key_id", type="string", length=255, nullable=true)
     */
    private $apiKeyId;

    /**
     * @var string
     *
     * @ORM\Column(name="ingenico_api_secret", type="crypted_string", length=255, nullable=true)
     */
    private $apiSecret;

    /**
     * @var string
     *
     * @ORM\Column(name="ingenico_api_endpoint", type="text", nullable=true)
     */
    private $apiEndpoint;

    /**
     * @var string
     *
     * @ORM\Column(name="ingenico_merchant_id", type="string", length=255, nullable=true)
     */
    private $merchantId;

    /**
     * @var array
     *
     * @ORM\Column(name="ingenico_enabled_products", type="array", nullable=true)
     */
    private $enabledProducts = [];

    /**
     * @var string
     *
     * @ORM\Column(name="ingenico_payment_action", type="string", length=255, nullable=true)
     */
    private $paymentAction;

    /**
     * @var bool
     *
     * @ORM\Column(name="ingenico_tokenization_enabled", type="boolean", nullable=true, options={"default"=false})
     */
    private $tokenizationEnabled = false;

    /**
     * @var string
     *
     * @ORM\Column(name="ingenico_direct_debit_text", type="string", length=255, nullable=true)
     */
    private $directDebitText;

    public function __construct()
    {
        $this->labels = new ArrayCollection();
        $this->shortLabels = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsBag()
    {
        if (null === $this->settings) {
            $this->settings = new ParameterBag([
                self::LABELS_KEY => $this->getLabels(),
                self::SHORT_LABELS_KEY => $this->getShortLabels(),
                self::API_KEY_ID => $this->getApiKeyId(),
                self::API_SECRET => $this->getApiSecret(),
                self::API_ENDPOINT => $this->getApiEndpoint(),
                self::MERCHANT_ID => $this->getMerchantId(),
                self::ENABLED_PRODUCTS => $this->getEnabledProducts(),
                self::PAYMENT_ACTION => $this->getPaymentAction(),
                self::TOKENIZATION_ENABLED => $this->isTokenizationEnabled(),
                self::DIRECT_DEBIT_TEXT => $this->getDirectDebitText(),
            ]);
        }

        return $this->settings;
    }


    /**
     * @param LocalizedFallbackValue $label
     *
     * @return IngenicoSettings
     */
    public function addLabel(LocalizedFallbackValue $label): IngenicoSettings
    {
        if (!$this->labels->contains($label)) {
            $this->labels->add($label);
        }

        return $this;
    }

    /**
     * @param LocalizedFallbackValue $label
     *
     * @return IngenicoSettings
     */
    public function removeLabel(LocalizedFallbackValue $label): IngenicoSettings
    {
        if ($this->labels->contains($label)) {
            $this->labels->removeElement($label);
        }

        return $this;
    }

    /**
     * Get labels
     *
     * @return Collection|LocalizedFallbackValue[]
     */
    public function getLabels(): Collection
    {
        return $this->labels;
    }

    /**
     * @param LocalizedFallbackValue $shortLabel
     *
     * @return IngenicoSettings
     */
    public function addShortLabel(LocalizedFallbackValue $shortLabel): IngenicoSettings
    {
        if (!$this->shortLabels->contains($shortLabel)) {
            $this->shortLabels->add($shortLabel);
        }

        return $this;
    }

    /**
     * @param LocalizedFallbackValue $shortLabel
     *
     * @return IngenicoSettings
     */
    public function removeShortLabel(LocalizedFallbackValue $shortLabel): IngenicoSettings
    {
        if ($this->shortLabels->contains($shortLabel)) {
            $this->shortLabels->removeElement($shortLabel);
        }

        return $this;
    }

    /**
     * @return Collection|LocalizedFallbackValue[]
     */
    public function getShortLabels(): Collection
    {
        return $this->shortLabels;
    }

    /**
     * @return string
     */
    public function getApiKeyId()
    {
        return $this->apiKeyId;
    }

    /**
     * @param string|null $apiKeyId
     * @return IngenicoSettings
     */
    public function setApiKeyId(?string $apiKeyId): IngenicoSettings
    {
        $this->apiKeyId = $apiKeyId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getApiSecret()
    {
        return $this->apiSecret;
    }

    /**
     * @param string|null $apiSecret
     * @return IngenicoSettings
     */
    public function setApiSecret(?string $apiSecret): IngenicoSettings
    {
        $this->apiSecret = $apiSecret;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getApiEndpoint()
    {
        return $this->apiEndpoint;
    }

    /**
     * @param string|null $apiEndpoint
     * @return IngenicoSettings
     */
    public function setApiEndpoint(?string $apiEndpoint): IngenicoSettings
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
     * @param string|null $merchantId
     * @return IngenicoSettings
     */
    public function setMerchantId(?string $merchantId): IngenicoSettings
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
     * @return IngenicoSettings
     */
    public function setEnabledProducts(array $enabledProducts): IngenicoSettings
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
     * @param string|null $paymentAction
     * @return IngenicoSettings
     */
    public function setPaymentAction(?string $paymentAction): IngenicoSettings
    {
        $this->paymentAction = $paymentAction;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTokenizationEnabled(): bool
    {
        return $this->tokenizationEnabled;
    }

    /**
     * @param bool $tokenizationEnabled
     *
     * @return IngenicoSettings
     */
    public function setTokenizationEnabled(bool $tokenizationEnabled): IngenicoSettings
    {
        $this->tokenizationEnabled = $tokenizationEnabled;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDirectDebitText()
    {
        return $this->directDebitText;
    }

    /**
     * @param string|null $directDebitText
     * @return IngenicoSettings
     */
    public function setDirectDebitText(?string $directDebitText): IngenicoSettings
    {
        $this->directDebitText = $directDebitText;

        return $this;
    }
}
