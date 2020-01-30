<?php

namespace Ingenico\Connect\OroCommerce\Provider;

use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Provider\PaymentTransactionProvider as BasePaymentTransactionProvider;

/**
 * Added logic for working with tokenize payment transactions.
 */
class PaymentTransactionProvider extends BasePaymentTransactionProvider
{
    /**
     * @param PaymentTransaction $paymentTransaction
     * @param string $type
     * @param array $transactionOptions
     *
     * @return PaymentTransaction
     */
    public function createTokenizePaymentTransaction(
        PaymentTransaction $paymentTransaction,
        string $type,
        array $transactionOptions
    ): PaymentTransaction {
        $tokenizePaymentTransaction = $this->createEmptyPaymentTransaction()
            ->setPaymentMethod($paymentTransaction->getPaymentMethod())
            ->setAction($type)
            ->setEntityClass($paymentTransaction->getEntityClass())
            ->setEntityIdentifier($paymentTransaction->getEntityIdentifier())
            ->setFrontendOwner($this->customerUserProvider->getLoggedUser(true))
            ->setTransactionOptions($transactionOptions)
            ->setActive(false)
            ->setSuccessful(true)
            ->setAmount(0)
            ->setCurrency('');

        return $tokenizePaymentTransaction;
    }

    /**
     * @return PaymentTransaction
     */
    private function createEmptyPaymentTransaction(): PaymentTransaction
    {
        return new $this->paymentTransactionClass();
    }
}
