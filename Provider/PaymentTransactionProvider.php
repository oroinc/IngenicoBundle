<?php

namespace Ingenico\Connect\OroCommerce\Provider;

use Doctrine\Common\Collections\Criteria;
use Ingenico\Connect\OroCommerce\Method\IngenicoPaymentMethod;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Provider\PaymentTransactionProvider as BasePaymentTransactionProvider;

/**
 * Added logic for working with tokenize payment transactions.
 */
class PaymentTransactionProvider extends BasePaymentTransactionProvider
{
    private const LAST_TOKEN_LIMIT = 10;
    private const TOKEN_KEY = 'token';

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
        // as entityIdentifier field couldn't be null we should set dummy value in case there is no CustomerUser
        $entityIdentifier = 0;

        $customerUser = $this->customerUserProvider->getLoggedUser(true);
        if ($customerUser) {
            $entityIdentifier = $customerUser->getId();
        }

        return $this->createEmptyPaymentTransaction()
            ->setPaymentMethod($paymentTransaction->getPaymentMethod())
            ->setAction($type)
            ->setEntityClass(CustomerUser::class)
            ->setEntityIdentifier($entityIdentifier)
            ->setFrontendOwner($customerUser)
            ->setTransactionOptions($transactionOptions)
            ->setActive(true)
            ->setSuccessful(true)
            ->setAmount(0)
            ->setCurrency('');
    }

    /**
     * @param string $paymentMethod
     *
     * @return PaymentTransaction[]|array
     */
    public function getActiveTokenizePaymentTransactions(string $paymentMethod): array
    {
        $customerUser = $this->customerUserProvider->getLoggedUser(true);
        if (!$customerUser) {
            return [];
        }

        return $this->doctrineHelper->getEntityRepository($this->paymentTransactionClass)->findBy(
            [
                'active' => true,
                'successful' => true,
                'action' => IngenicoPaymentMethod::TOKENIZE,
                'paymentMethod' => $paymentMethod,
                'frontendOwner' => $customerUser,
            ],
            ['id' => Criteria::DESC],
            self::LAST_TOKEN_LIMIT
        );
    }

    /**
     * @param string $paymentMethod
     * @param int $id
     *
     * @return string|null
     */
    public function getTokenFromTokenizePaymentTransactionById(string $paymentMethod, int $id): ?string
    {
        $customerUser = $this->customerUserProvider->getLoggedUser(true);
        if (!$customerUser) {
            return null;
        }

        /** @var PaymentTransaction $paymentTransaction */
        $paymentTransaction = $this->doctrineHelper->getEntityRepository($this->paymentTransactionClass)->findOneBy(
            [
                'active' => true,
                'successful' => true,
                'action' => IngenicoPaymentMethod::TOKENIZE,
                'paymentMethod' => $paymentMethod,
                'frontendOwner' => $customerUser,
                'id' => $id,
            ]
        );

        $transactionOptions = $paymentTransaction->getTransactionOptions();

        return $transactionOptions[self::TOKEN_KEY] ?? null;
    }

    /**
     * @return PaymentTransaction
     */
    private function createEmptyPaymentTransaction(): PaymentTransaction
    {
        return new $this->paymentTransactionClass();
    }
}
