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
     * Execute payment action under payment transaction
     *
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
     * Check that payment product handler is applicable on specific payment transaction
     *
     * @param PaymentTransaction $paymentTransaction
     * @return bool
     */
    public function isApplicable(PaymentTransaction $paymentTransaction): bool;
}
