<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\ActionParams\PaymentId;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\AuthorizationMode;
use Ingenico\Connect\OroCommerce\Ingenico\Response\Response;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Method\IngenicoPaymentMethod;
use Ingenico\Connect\OroCommerce\Provider\PaymentTransactionProvider;
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
    private const TOKEN_KEY = 'token';

    /** @var PaymentTransactionProvider */
    protected $paymentTransactionProvider;

    /**
     * @param PaymentTransactionProvider $paymentTransactionProvider
     */
    public function setPaymentTransactionProvider(PaymentTransactionProvider $paymentTransactionProvider): void
    {
        $this->paymentTransactionProvider = $paymentTransactionProvider;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param PaymentConfigInterface|IngenicoConfig $config
     *
     * @throws \JsonException
     * @throws \Throwable
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

        // save token to another transaction for future use
        $saveForLaterUse = $this->getTransactionOption($paymentTransaction, 'ingenicoSaveForLaterUse');
        if ($saveForLaterUse && $response->isSuccessful() && $config->isTokenizationEnabled()) {
            $tokenResponse = $this->requestTokenize($config, $response->getReference());
            if ($tokenResponse->isSuccessful()) {
                $token = $tokenResponse->offsetGetOr(self::TOKEN_KEY);
                $cardNumber = $response->getCardNumber();

                $tokenizePaymentTransaction = $this->paymentTransactionProvider->createTokenizePaymentTransaction(
                    $paymentTransaction,
                    IngenicoPaymentMethod::TOKENIZE,
                    compact('token', 'cardNumber')
                );

                $this->paymentTransactionProvider->savePaymentTransaction($tokenizePaymentTransaction);
            }
        }
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

    /**
     * @param PaymentConfigInterface $config
     * @param string $paymentId
     *
     * @return Response
     *
     * @throws \JsonException
     */
    private function requestTokenize(PaymentConfigInterface $config, string $paymentId): Response
    {
        return $this->gateway->request(
            $config,
            Transaction::CREATE_TOKEN,
            [],
            [PaymentId::NAME => $paymentId]
        );
    }
}
