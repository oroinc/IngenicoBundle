<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\ActionParams\PaymentId;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\AuthorizationMode;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\RequiresApproval;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\Token;
use Ingenico\Connect\OroCommerce\Ingenico\Response\Response;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Method\IngenicoPaymentMethod;
use Ingenico\Connect\OroCommerce\Provider\PaymentTransactionProvider;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\EnabledProductsDataProvider;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\PaymentActionDataProvider;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;

/**
 * 'Credit card' payment products handler
 */
class CreditCardPaymentProductHandler extends AbstractPaymentProductHandler
{
    public const ACTION_PURCHASE = 'purchase';
    public const ACTION_CAPTURE = 'capture';

    private const TOKEN_OPTION_KEY = 'ingenicoToken';
    private const TOKEN_KEY = 'token';
    private const CREDIT_CARD_KEY = 'cardNumber';
    private const PAYMENT_PRODUCT_KEY = 'paymentProduct';

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
     * @param IngenicoConfig $config
     * @return array
     */
    public function purchase(PaymentTransaction $paymentTransaction, IngenicoConfig $config)
    {
        $paymentTransaction->setSuccessful(false);

        $response = $this->requestCreatePayment(
            $paymentTransaction,
            $config,
            $this->getCreatePaymentAdditionalOptions($paymentTransaction, $config)
        );

        $paymentAction = $config->getPaymentAction() === PaymentActionDataProvider::PRE_AUTHORIZATION ?
            PaymentMethodInterface::AUTHORIZE : $paymentTransaction->getAction();
        $paymentTransaction
            ->setSuccessful($response->isSuccessful())
            ->setActive($response->isSuccessful())
            ->setReference($response->getReference())
            ->setAction($paymentAction)
            ->setResponse($response->toArray());

        // save token to another transaction for future use
        if ($response->isSuccessful() &&
            $config->isTokenizationEnabled() &&
            $this->isSaveForLaterUseApplicable($paymentTransaction)
        ) {
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

        return [
            'purchaseSuccessful' => $response->isSuccessful(),
        ];
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param IngenicoConfig $config
     *
     * @return array
     */
    private function getCreatePaymentAdditionalOptions(
        PaymentTransaction $paymentTransaction,
        IngenicoConfig $config
    ): array {
        $options = [
            AuthorizationMode::NAME => $config->getPaymentAction(),
            // This logic should be moved from here in scope of INGA-72
            RequiresApproval::NAME => $config->getPaymentAction() !== PaymentActionDataProvider::SALE,
        ];

        if ($config->isTokenizationEnabled()) {
            $tokenId = $this->getAdditionalDataFieldByKey($paymentTransaction, self::TOKEN_OPTION_KEY);
            if ($tokenId) {
                $token = $this->paymentTransactionProvider->getTokenFromTokenizePaymentTransactionById(
                    $config->getPaymentMethodIdentifier(),
                    $tokenId
                );
                $options[Token::NAME] = $token;
            }
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
        return EnabledProductsDataProvider::CREDIT_CARDS_GROUP_ID;
    }

    /**
     * @inheritDoc
     */
    protected function isActionSupported(string $actionName): bool
    {
        return in_array($actionName, [self::ACTION_CAPTURE, self::ACTION_PURCHASE], true);
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     *
     * @return bool
     */
    protected function isSaveForLaterUseApplicable(PaymentTransaction $paymentTransaction): bool
    {
        $saveForLaterUseChecked = $this->getAdditionalDataFieldByKey($paymentTransaction, 'ingenicoSaveForLaterUse');
        $tokenUsed = $this->getAdditionalDataFieldByKey($paymentTransaction, 'ingenicoToken');

        return $saveForLaterUseChecked && !$tokenUsed;
    }

    /**
     * @param IngenicoConfig $config
     * @param string $paymentId
     *
     * @return Response
     */
    private function requestTokenize(IngenicoConfig $config, string $paymentId): Response
    {
        return $this->gateway->request(
            $config,
            Transaction::CREATE_TOKEN,
            [],
            [PaymentId::NAME => $paymentId]
        );
    }
}
