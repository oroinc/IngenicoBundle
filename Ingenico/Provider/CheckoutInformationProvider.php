<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Provider;

use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\BillingAddress\Address\CountryCode;
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
     * @return array
     */
    public function getCheckoutOptions(PaymentTransaction $paymentTransaction)
    {
        return array_merge($this->getBillingAddressOptions($paymentTransaction));
    }

    /**
     * @param $paymentTransaction
     * @return array
     */
    private function getBillingAddressOptions(PaymentTransaction $paymentTransaction)
    {
        $checkout = $this->extractCheckout($paymentTransaction);

        if (!$checkout) {
            return [];
        }

        $billingAddress = $checkout->getBillingAddress();

        if (!$billingAddress instanceof AbstractAddress) {
            return [];
        }

        return [
            CountryCode::NAME => $billingAddress->getCountryIso2(),
        ];
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
