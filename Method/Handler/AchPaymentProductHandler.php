<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\DirectDebitPayment\DirectDebitText;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\EnabledProductsDataProvider;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;

/**
 * 'ACH' payment product handler
 */
class AchPaymentProductHandler extends AbstractPaymentProductHandler
{
    /**
     * @param PaymentTransaction $paymentTransaction
     * @param IngenicoConfig $config
     * @return array
     */
    public function purchase(
        PaymentTransaction $paymentTransaction,
        IngenicoConfig $config
    ): array {
        $paymentTransaction->setSuccessful(false);
        $response = $this->requestCreatePayment(
            $paymentTransaction,
            $config,
            $this->getCreatePaymentAdditionalOptions($config)
        );

        $paymentTransaction
            ->setSuccessful($response->isSuccessful())
            ->setActive($response->isSuccessful())
            ->setAction(PaymentMethodInterface::PENDING)
            ->setReference($response->getReference())
            ->setResponse($response->toArray());

        return [
            'purchaseSuccessful' => $response->isSuccessful(),
        ];
    }

    /**
     * @param IngenicoConfig $config
     * @return array
     */
    private function getCreatePaymentAdditionalOptions(IngenicoConfig $config): array
    {
        // hardcoded value to be replaced with a value from payment integration's settings. @INGA-40
        return [
            DirectDebitText::NAME => 'COMPANYNAME 123-123-1234 ZIP CODE UK',
        ];
    }

    /**
     * @return string
     */
    protected function getCreatePaymentTransactionType(): string
    {
        return Transaction::CREATE_DIRECT_DEBIT_PAYMENT;
    }

    /**
     * {@inheritdoc}
     */
    protected function isActionSupported(string $actionName): bool
    {
        return  $actionName === PaymentMethodInterface::PURCHASE;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): string
    {
        return EnabledProductsDataProvider::ACH;
    }
}
