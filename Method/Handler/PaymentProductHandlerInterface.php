<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

/**
 * Ingenico payment product handler interface
 */
interface PaymentProductHandlerInterface
{
    /**
     * @param string $action
     * @param PaymentTransaction $paymentTransaction
     * @param IngenicoConfig $config
     * @return array
     */
    public function execute(
        string $action,
        PaymentTransaction $paymentTransaction,
        IngenicoConfig $config
    ): array;

    /**
     *
     */
    public function supports(PaymentTransaction $paymentTransaction): bool;
}
