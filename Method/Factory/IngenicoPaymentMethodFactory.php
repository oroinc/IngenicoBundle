<?php

namespace Ingenico\Connect\OroCommerce\Method\Factory;

use Ingenico\Connect\OroCommerce\Ingenico\Gateway\Gateway;
use Ingenico\Connect\OroCommerce\Method\Handler\PaymentProductHandlerRegistry;
use Ingenico\Connect\OroCommerce\Method\IngenicoPaymentMethod;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;

/**
 * Factory class to produce Ingenico payment method's instances with given configuration
 */
class IngenicoPaymentMethodFactory
{
    /** @var PaymentProductHandlerRegistry */
    private $paymentProductHandlersRegistry;

    /** @var Gateway */
    private $gateway;

    /**
     * @param PaymentProductHandlerRegistry $paymentProductHandlersRegistry
     * @param Gateway $gateway
     */
    public function __construct(PaymentProductHandlerRegistry $paymentProductHandlersRegistry, Gateway $gateway)
    {
        $this->paymentProductHandlersRegistry = $paymentProductHandlersRegistry;
        $this->gateway = $gateway;
    }

    /**
     * @param PaymentConfigInterface $config
     *
     * @return PaymentMethodInterface
     */
    public function create(PaymentConfigInterface $config): PaymentMethodInterface
    {
        return new IngenicoPaymentMethod($config, $this->paymentProductHandlersRegistry, $this->gateway);
    }
}
