<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\AuthorizationMode;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\EnabledProductsDataProvider;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\PaymentActionDataProvider;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;

/**
 * 'Credit card' payment products handler
 */
class CreditCardPaymentProductHandler extends AbstractPaymentProductHandler
{
    /**
     * @param PaymentTransaction $paymentTransaction
     * @param PaymentConfigInterface $config
     * @throws \JsonException
     */
    public function purchase(
        PaymentTransaction $paymentTransaction,
        PaymentConfigInterface $config
    ) {
        $paymentTransaction->setSuccessful(false);
        $response = $this->requestCreatePayment($paymentTransaction, $config);

        $paymentAction = $config->getPaymentAction() == PaymentActionDataProvider::PRE_AUTHORIZATION ?
            PaymentMethodInterface::AUTHORIZE : $paymentTransaction->getAction();
        $paymentTransaction
            ->setSuccessful($response->isSuccessful())
            ->setActive($response->isSuccessful())
            ->setReference($response->getReference())
            ->setAction($paymentAction)
            ->setResponse($response->toArray());
    }

    /**
     * {@inheritdoc}
     */
    protected function getCreatePaymentOptions(
        PaymentTransaction $paymentTransaction,
        PaymentConfigInterface $config
    ): array {
        return [AuthorizationMode::NAME => $config->getPaymentAction()];
    }

    /**
     * @return string
     */
    protected function getCreatePaymentTransactionType(): string
    {
        return Transaction::CREATE_CARDS_PAYMENT;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): string
    {
        return EnabledProductsDataProvider::CREDIT_CARDS;
    }
}
