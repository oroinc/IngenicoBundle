<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

/**
 * Handlers registry of Ingenico payment products
 */
class PaymentProductHandlerRegistry
{
    /**
     * @var iterable|PaymentProductHandlerInterface[]
     */
    private $paymentProductsHandlers;

    /**
     * @param iterable|PaymentProductHandlerInterface[] $paymentProductsHandlers
     */
    public function __construct(iterable $paymentProductsHandlers)
    {
        $this->paymentProductsHandlers = $paymentProductsHandlers;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @return PaymentProductHandlerInterface|null
     */
    public function getPaymentProductHandler(
        PaymentTransaction $paymentTransaction
    ): ?PaymentProductHandlerInterface {
        foreach ($this->paymentProductsHandlers as $productTypeHandler) {
            if ($productTypeHandler->isApplicable($paymentTransaction)) {
                return $productTypeHandler;
            }
        }

        return null;
    }
}
