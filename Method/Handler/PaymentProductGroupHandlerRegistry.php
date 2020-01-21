<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

/**
 * Handlers registry of Ingenico payment product groups
 */
class PaymentProductGroupHandlerRegistry
{
    /**
     * @var iterable|PaymentProductGroupHandlerInterface[]
     */
    private $paymentProductsHandlers;

    /**
     * @param iterable\PaymentProductHandlerInterface[] $paymentProductsHandlers
     */
    public function __construct(iterable $paymentProductsHandlers)
    {
        $this->paymentProductsHandlers = $paymentProductsHandlers;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @return PaymentProductGroupHandlerInterface|null
     */
    public function getPaymentProductGroupHandler(
        PaymentTransaction $paymentTransaction
    ): ?PaymentProductGroupHandlerInterface {
        foreach ($this->paymentProductsHandlers as $productTypeHandler) {
            if ($productTypeHandler->supports($paymentTransaction)) {
                return $productTypeHandler;
            }
        }

        return null;
    }
}
