<?php

namespace Ingenico\Connect\OroCommerce\Method\View;

use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Oro\Bundle\CurrencyBundle\Rounding\RoundingServiceInterface;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Method\View\PaymentMethodViewInterface;

/**
 * View for Ingenico payment method
 */
class IngenicoView implements PaymentMethodViewInterface
{
    /** @var IngenicoConfig */
    private $config;

    /** @var string */
    private $currentLocalizationCode;

    /** @var RoundingServiceInterface */
    private $rounding;

    /**
     * @param IngenicoConfig $config
     * @param string $currentLocalizationCode
     * @param RoundingServiceInterface $rounding
     */
    public function __construct(
        IngenicoConfig $config,
        string $currentLocalizationCode,
        RoundingServiceInterface $rounding
    ) {
        $this->config = $config;
        $this->currentLocalizationCode = $currentLocalizationCode;
        $this->rounding = $rounding;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(PaymentContextInterface $context): array
    {
        return [
            'paymentDetails' => [
                'totalAmount' => (int) ($this->rounding->round($context->getTotal()) * 100),
                'currency' => $context->getCurrency(),
                'countryCode' => $context->getBillingAddress()->getCountryIso2(),
                'isRecurring' => false,
                'locale' => $this->currentLocalizationCode
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getBlock(): string
    {
        return '_payment_methods_ingenico_widget';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return $this->config->getLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getShortLabel(): string
    {
        return $this->config->getShortLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminLabel()
    {
        return $this->config->getAdminLabel();
    }

    /** {@inheritdoc} */
    public function getPaymentMethodIdentifier()
    {
        return $this->config->getPaymentMethodIdentifier();
    }
}
