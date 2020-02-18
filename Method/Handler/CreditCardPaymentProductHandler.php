<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Ingenico\Connect\OroCommerce\Ingenico\Gateway\Gateway;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\ActionParams\PaymentId;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\AuthorizationMode;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\RequiresApproval;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\Token;
use Ingenico\Connect\OroCommerce\Ingenico\Provider\CheckoutInformationProvider;
use Ingenico\Connect\OroCommerce\Ingenico\Response\PaymentResponse;
use Ingenico\Connect\OroCommerce\Ingenico\Response\Response;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Method\IngenicoPaymentMethod;
use Ingenico\Connect\OroCommerce\Normalizer\AmountNormalizer;
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
    private const TOKEN_OPTION_KEY = 'ingenicoToken';
    private const SAVE_FOR_LATER_USE_OPTION_KEY = 'ingenicoSaveForLaterUse';
    private const TOKEN_KEY = 'token';
    private const CREDIT_CARD_KEY = 'cardNumber';
    private const PAYMENT_PRODUCT_KEY = 'paymentProduct';

    /** @var PaymentTransactionProvider */
    protected $paymentTransactionProvider;

    /**
     * @inheritDoc
     */
    public function __construct(
        Gateway $gateway,
        AmountNormalizer $amountNormalizer,
        CheckoutInformationProvider $checkoutInformationProvider,
        PaymentTransactionProvider $paymentTransactionProvider
    ) {
        parent::__construct($gateway, $amountNormalizer, $checkoutInformationProvider);
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
            ->setAction($this->getPurchaseActionByPaymentResponse($response))
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
            RequiresApproval::NAME => $this->isRequiresApproval($config),
        ];

        if ($config->isTokenizationEnabled()) {
            $tokenId = $this->getAdditionalDataFieldByKey($paymentTransaction, self::TOKEN_OPTION_KEY, false);
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
     * {@inheritdoc}
     */
    protected function isActionSupported(string $actionName): bool
    {
        return in_array($actionName, [PaymentMethodInterface::PURCHASE, PaymentMethodInterface::CAPTURE], true);
    }

    /**
     * Return new payment action based on the response from the Ingenico API
     * In case we are requesting AUTHORIZE but Ingenico does CHARGE/SALE
     *
     * @param PaymentResponse $response
     * @return string
     */
    protected function getPurchaseActionByPaymentResponse(PaymentResponse $response): string
    {
        $paymentStatus = $response->getPaymentStatus();

        if ($paymentStatus === PaymentResponse::PENDING_APPROVAL_PAYMENT_STATUS) {
            return PaymentMethodInterface::AUTHORIZE;
        }

        if ($paymentStatus === PaymentResponse::CAPTURE_REQUESTED_PAYMENT_STATUS) {
            return PaymentMethodInterface::CAPTURE;
        }

        return PaymentMethodInterface::CHARGE;
    }

    /**
     * @param IngenicoConfig $config
     *
     * @return bool
     */
    protected function isRequiresApproval(IngenicoConfig $config): bool
    {
        return $config->getPaymentAction() !== PaymentActionDataProvider::SALE;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     *
     * @return bool
     */
    protected function isSaveForLaterUseApplicable(PaymentTransaction $paymentTransaction): bool
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
