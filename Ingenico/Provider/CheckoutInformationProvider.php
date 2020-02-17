<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Provider;

use Oro\Bundle\AddressBundle\Entity\AbstractAddress;
use Oro\Bundle\CheckoutBundle\Entity\Checkout;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

/**
 * Extracts information (like billing address) from the checkout (if possible) to pass it to the payment system
 */
class CheckoutInformationProvider
{
    /**
     * @var DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @return AbstractAddress|null
     */
    public function getBillingAddress(PaymentTransaction $paymentTransaction): ?AbstractAddress
    {
        $checkout = $this->extractCheckout($paymentTransaction);

        if (!$checkout) {
            return null;
        }

        return $checkout->getBillingAddress();
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @return Checkout|null
     */
    private function extractCheckout(PaymentTransaction $paymentTransaction)
    {
        $transactionOptions = $paymentTransaction->getTransactionOptions();
        if (!isset($transactionOptions['checkoutId'])) {
            return null;
        }

        return $this->doctrineHelper->getEntity(Checkout::class, $transactionOptions['checkoutId']);
    }
}
