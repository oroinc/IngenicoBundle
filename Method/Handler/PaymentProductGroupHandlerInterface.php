<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;

/**
 * Ingenico payment product group handler interface
 */
interface PaymentProductGroupHandlerInterface
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
        PaymentConfigInterface $config
    ): array;

    /**
     *
     */
    public function supports(PaymentTransaction $paymentTransaction): bool;
}
