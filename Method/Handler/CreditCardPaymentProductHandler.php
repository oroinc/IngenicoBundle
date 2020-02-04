<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\ActionParams\PaymentId;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\AuthorizationMode;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\Token;
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
    public const ADDITIONAL_DATA_TOKE_KEY = 'ingenicoToken';
    public const TOKEN_KEY = 'token';
    public const CREDIT_CARD_KEY = 'cardNumber';
    public const PAYMENT_PRODUCT_KEY = 'paymentProduct';

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
        $saveForLaterUseApplicable = $this->isSaveForLaterUseApplicable($paymentTransaction);
        if ($saveForLaterUseApplicable && $response->isSuccessful() && $config->isTokenizationEnabled()) {
            $tokenResponse = $this->requestTokenize($config, $response->getReference());
            if ($tokenResponse->isSuccessful()) {
                $tokenizePaymentTransaction = $this->paymentTransactionProvider->createTokenizePaymentTransaction(
                    $paymentTransaction,
                    IngenicoPaymentMethod::TOKENIZE,
                    [
                        self::TOKEN_KEY => $tokenResponse->offsetGetOr(self::TOKEN_KEY),
                        self::CREDIT_CARD_KEY => $response->getCardNumber(),
                        self::PAYMENT_PRODUCT_KEY => $response->getPaymentProduct(),
                    ]
                );
                $tokenizePaymentTransaction->setResponse($tokenResponse->toArray());

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
        $options = [AuthorizationMode::NAME => $config->getPaymentAction()];

        $tokenId = $this->getTransactionOption($paymentTransaction, self::ADDITIONAL_DATA_TOKE_KEY);
        if ($tokenId && $config->isTokenizationEnabled()) {
            $token = $this->paymentTransactionProvider->getTokenFromTokenizePaymentTransactionById(
                $config->getPaymentMethodIdentifier(),
                $tokenId
            );
            $options[Token::NAME] = $token;
        }

        return $options;
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
     * @param PaymentTransaction $paymentTransaction
     *
     * @return bool
     */
    protected function isSaveForLaterUseApplicable(PaymentTransaction $paymentTransaction): bool
    {
        $saveForLaterUseChecked = $this->getTransactionOption($paymentTransaction, 'ingenicoSaveForLaterUse');
        $tokenUsed = $this->getTransactionOption($paymentTransaction, 'ingenicoToken');

        return $saveForLaterUseChecked && !$tokenUsed;
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
