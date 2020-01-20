<?php

namespace Ingenico\Connect\OroCommerce\Method\View;

use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Method\View\PaymentMethodViewInterface;

/**
 * View for Ingenico payment method
 */
class IngenicoView implements PaymentMethodViewInterface
{
    /**
     * @var IngenicoConfig
     */
    private $config;

    /**
     * @param IngenicoConfig $config
     */
    public function __construct(IngenicoConfig $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(PaymentContextInterface $context): array
    {
        return [];
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
