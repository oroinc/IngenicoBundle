<?php

namespace Ingenico\Connect\OroCommerce\Method\View\Factory;

use Ingenico\Connect\OroCommerce\Method\View\IngenicoView;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;

/**
 * Factory for creating views of Ingenico payment method
 */
class IngenicoViewFactory
{
    /**
     * @param PaymentConfigInterface $config
     * @return IngenicoView
     */
    public function create(PaymentConfigInterface $config): IngenicoView
    {
        return new IngenicoView($config);
    }
}
