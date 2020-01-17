<?php

namespace Ingenico\Connect\OroCommerce\Method\Factory;

use Ingenico\Connect\OroCommerce\Method\IngenicoPaymentMethod;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;

/**
 * Factory class  to produce Ingenico payment method's instances with given configuration
 */
class IngenicoPaymentMethodFactory
{
    /**
     * {@inheritdoc}
     */
    public function create(PaymentConfigInterface $config): PaymentMethodInterface
    {
        return new IngenicoPaymentMethod($config);
    }
}
