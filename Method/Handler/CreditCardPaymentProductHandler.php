<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Ingenico\Connect\OroCommerce\Ingenico\Gateway\Gateway;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\ActionParams\PaymentId;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\AuthorizationMode;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\RequiresApproval;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\Token;
use Ingenico\Connect\OroCommerce\Ingenico\Provider\CheckoutInformationProvider;
use Ingenico\Connect\OroCommerce\Ingenico\Response\TokenResponse;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Normalizer\AmountNormalizer;
use Ingenico\Connect\OroCommerce\Provider\PaymentTransactionProvider;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\EnabledProductsDataProvider;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\PaymentActionDataProvider;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;
use Psr\Log\LoggerInterface;

/**
 * 'Credit card' payment products handler
 */
class CreditCardPaymentProductHandler extends AbstractPaymentProductHandler
{
    /**
     * Constants of the data in tokenized payment transaction
     */
    public const TOKEN_KEY = PaymentTransactionProvider::TOKEN_KEY;
    public const CREDIT_CARD_KEY = 'cardNumber';
    public const PAYMENT_PRODUCT_KEY = 'paymentProduct';

    private const TOKEN_OPTION_KEY = 'ingenicoToken';
    private const SAVE_FOR_LATER_USE_OPTION_KEY = 'ingenicoSaveForLaterUse';

    /** @var PaymentTransactionProvider */
    protected $paymentTransactionProvider;

    /**
     * @inheritDoc
     */
    public function __construct(
        Gateway $gateway,
        AmountNormalizer $amountNormalizer,
        CheckoutInformationProvider $checkoutInformationProvider,
        LoggerInterface $logger,
        PaymentTransactionProvider $paymentTransactionProvider
    ) {
        parent::__construct($gateway, $amountNormalizer, $checkoutInformationProvider, $logger);
        $this->paymentTransactionProvider = $paymentTransactionProvider;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param IngenicoConfig $config
     * @return array
     */
    protected function purchase(
        PaymentTransaction $paymentTransaction,
        IngenicoConfig $config
    ): array {
        $paymentTransaction->setSuccessful(false);

        $response = $this->requestCreatePayment(
            $paymentTransaction,
            $config,
            $this->getCreatePaymentAdditionalOptions($paymentTransaction, $config)
        );

        $paymentTransaction
            ->setSuccessful($response->isSuccessful())
            ->setActive($response->isSuccessful())
            ->setReference($response->getReference())
            ->setAction($this->getPaymentAction($config))
            ->setResponse($response->toArray());

        // save token to another transaction for future use
        if ($response->isSuccessful() &&
            $config->isTokenizationEnabled() &&
            $this->isSaveForLaterUseApplicable($paymentTransaction)
        ) {
            $tokenResponse = $this->requestTokenize($config, $response->getReference());
            if ($tokenResponse->isSuccessful()) {
                $tokenizePaymentTransaction = $this->paymentTransactionProvider
                    ->createTokenizePaymentTransaction(
                        $paymentTransaction,
                        [
                            self::TOKEN_KEY => $tokenResponse->getToken(),
                            self::CREDIT_CARD_KEY => $response->getCardNumber(),
                            self::PAYMENT_PRODUCT_KEY => $response->getPaymentProduct(),
                        ]
                    );
                $tokenizePaymentTransaction->setResponse($tokenResponse->toArray());

                $this->paymentTransactionProvider->savePaymentTransaction($tokenizePaymentTransaction);
            } else {
                $this->logger->error('Can not create token for payment transaction', [
                    'paymentTransactionEntityClass' => $paymentTransaction->getEntityClass(),
                    'paymentTransactionEntityIdentifier' => $paymentTransaction->getEntityIdentifier(),
                    'errors' => $tokenResponse->getErrors(),
                ]);
            }
        }

        return [
            'purchaseSuccessful' => $response->isSuccessful(),
        ];
    }

    /**
     *
     * @param PaymentTransaction $paymentTransaction
     * @param IngenicoConfig $config
     * @return array
     */
    protected function capture(
        PaymentTransaction $paymentTransaction,
        IngenicoConfig $config
    ) {
        $sourcePaymentTransaction = $paymentTransaction->getSourcePaymentTransaction();
        if (!$sourcePaymentTransaction) {
            $paymentTransaction
                ->setSuccessful(false)
                ->setActive(false);

            return ['successful' => false];
        }

        $response = $this->requestApprovePayment($sourcePaymentTransaction, $config);

        $sourcePaymentTransaction->setActive(!$paymentTransaction->isSuccessful());
        $paymentTransaction
            ->setReference($response->getReference())
            ->setSuccessful($response->isSuccessful())
            ->setResponse($response->toArray())
            ->setActive($response->isSuccessful());

        return [
            'message' => $response->getErrors() ? implode("\n", $response->getErrors()) : null,
            'successful' => $response->isSuccessful(),
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
            RequiresApproval::NAME => $this->isRequiresApproval($config),
        ];

        if ($config->isTokenizationEnabled()) {
            $tokenizedPaymentTransactionId = $this->getAdditionalDataFieldByKey(
                $paymentTransaction,
                self::TOKEN_OPTION_KEY,
                false
            );

            if ($tokenizedPaymentTransactionId) {
                $token = $this->paymentTransactionProvider->getTokenFromTokenizePaymentTransactionById(
                    $config->getPaymentMethodIdentifier(),
                    $tokenizedPaymentTransactionId
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
     * {@inheritdoc}
     */
    protected function isActionSupported(string $actionName): bool
    {
        return in_array($actionName, [PaymentMethodInterface::PURCHASE, PaymentMethodInterface::CAPTURE], true);
    }

    /**
     * @param IngenicoConfig $config
     * @return string
     */
    private function getPaymentAction(IngenicoConfig $config)
    {
        switch ($config->getPaymentAction()) {
            case PaymentActionDataProvider::SALE:
                return PaymentMethodInterface::CHARGE;
            case PaymentActionDataProvider::PRE_AUTHORIZATION:
            case PaymentActionDataProvider::FINAL_AUTHORIZATION:
                return PaymentMethodInterface::AUTHORIZE;
        }

        throw new \LogicException(
            sprintf('Not expected payment action from the config received: "%s"', $config->getPaymentAction())
        );
    }

    /**
     * @param IngenicoConfig $config
     *
     * @return bool
     */
    private function isRequiresApproval(IngenicoConfig $config): bool
    {
        return $config->getPaymentAction() !== PaymentActionDataProvider::SALE;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     *
     * @return bool
     */
    private function isSaveForLaterUseApplicable(PaymentTransaction $paymentTransaction): bool
    {
        $saveForLaterUseChecked = $this->getAdditionalDataFieldByKey(
            $paymentTransaction,
            self::SAVE_FOR_LATER_USE_OPTION_KEY,
            false
        );

        $tokenUsed = $this->getAdditionalDataFieldByKey($paymentTransaction, self::TOKEN_OPTION_KEY, false);

        return $saveForLaterUseChecked && !$tokenUsed;
    }

    /**
     * @param IngenicoConfig $config
     * @param string $paymentId
     *
     * @return TokenResponse
     */
    private function requestTokenize(IngenicoConfig $config, string $paymentId): TokenResponse
    {
        $response = $this->gateway->request(
            $config,
            Transaction::CREATE_TOKEN,
            [],
            [PaymentId::NAME => $paymentId]
        );

        return TokenResponse::create($response->toArray());
    }
}
