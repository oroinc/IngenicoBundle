<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\AuthorizationMode;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\RequiresApproval;
use Ingenico\Connect\OroCommerce\Ingenico\Response\PaymentResponse;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\EnabledProductsDataProvider;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\PaymentActionDataProvider;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;

/**
 * 'Credit card' payment products handler
 */
class CreditCardPaymentProductHandler extends AbstractPaymentProductHandler
{
    private const PENDING_APPROVAL_STATUS = 'PENDING_APPROVAL';
    private const CAPTURE_REQUESTED_STATUS = 'CAPTURE_REQUESTED';

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

        $paymentAction = $config->getPaymentAction() === PaymentActionDataProvider::PRE_AUTHORIZATION ?
            PaymentMethodInterface::AUTHORIZE : $paymentTransaction->getAction();
        $paymentTransaction
            ->setSuccessful($response->isSuccessful())
            ->setActive($response->isSuccessful())
            ->setReference($response->getReference())
            ->setAction($this->getPurchaseActionByPaymentResponse($response))
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
        return [
            AuthorizationMode::NAME => $config->getPaymentAction(),
            // TODO: This logic should be moved from here
            RequiresApproval::NAME => $config->getPaymentAction() !== PaymentActionDataProvider::SALE,
        ];
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
        return EnabledProductsDataProvider::CREDIT_CARDS_GROUP_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function isActionSupported(string $actionName): bool
    {
        return in_array($actionName, [PaymentMethodInterface::PURCHASE, PaymentMethodInterface::CAPTURE], true);
    }

    /**
     * @param PaymentResponse $response
     * @return string
     */
    protected function getPurchaseActionByPaymentResponse(PaymentResponse $response): string
    {
        $paymentStatus = $response->getPaymentStatus();

        if ($paymentStatus == self::PENDING_APPROVAL_STATUS) {
            return PaymentMethodInterface::AUTHORIZE;
        } elseif ($paymentStatus == self::CAPTURE_REQUESTED_STATUS) {
            return PaymentMethodInterface::CAPTURE;
        }

        return parent::getPurchaseActionByPaymentResponse($response);
    }
}
