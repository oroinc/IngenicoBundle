<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Ingenico\Connect\OroCommerce\Ingenico\Gateway\Gateway;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\ActionParams\PaymentId;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Capture;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\EncryptedCustomerInput;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Order\AmountOfMoney;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Order\References\MerchantReference;
use Ingenico\Connect\OroCommerce\Ingenico\Provider\CheckoutInformationProvider;
use Ingenico\Connect\OroCommerce\Ingenico\Response\PaymentResponse;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Normalizer\AmountNormalizer;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

/**
 * Abstract(generic) class for Ingenico payment products handler
 */
abstract class AbstractPaymentProductHandler implements PaymentProductHandlerInterface
{
    protected const PAYMENT_PRODUCT_OPTION_KEY = 'ingenicoPaymentProduct';
    protected const CUSTOMER_ENC_DETAILS_OPTION_KEY = 'ingenicoCustomerEncDetails';

    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * @var AmountNormalizer
     */
    protected $amountNormalizer;

    /**
     * @var CheckoutInformationProvider
     */
    protected $checkoutInformationProvider;

    /**
     * @param Gateway $gateway
     * @param AmountNormalizer $amountNormalizer
     * @param CheckoutInformationProvider $checkoutInformationProvider
     */
    public function __construct(
        Gateway $gateway,
        AmountNormalizer $amountNormalizer,
        CheckoutInformationProvider $checkoutInformationProvider
    ) {
        $this->gateway = $gateway;
        $this->amountNormalizer = $amountNormalizer;
        $this->checkoutInformationProvider = $checkoutInformationProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(PaymentTransaction $paymentTransaction): bool
    {
        // using source transaction's(if exists) options for new transaction
        $targetPaymentTransaction =
            $paymentTransaction->getId() === null && $paymentTransaction->getSourcePaymentTransaction() ?
                $paymentTransaction->getSourcePaymentTransaction() : $paymentTransaction;

        $paymentProduct = (string)$this->getAdditionalDataFieldByKey(
            $targetPaymentTransaction,
            self::PAYMENT_PRODUCT_OPTION_KEY
        );

        return $this->getType() === $paymentProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(
        string $action,
        PaymentTransaction $paymentTransaction,
        IngenicoConfig $config
    ): array {
        if (!$this->isActionSupported($action)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '"%s" payment method "%s" action is not supported',
                    $config->getPaymentMethodIdentifier(),
                    $action
                )
            );
        }

        return $this->$action($paymentTransaction, $config) ?: [];
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param IngenicoConfig $config
     * @return array
     */
    public function capture(
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
     * @param string $key
     * @return mixed
     */
    protected function getAdditionalDataFieldByKey(PaymentTransaction $paymentTransaction, string $key)
    {
        $transactionOptions = $paymentTransaction->getTransactionOptions();
        $additionalData = json_decode($transactionOptions['additionalData'], true);

        if (!array_key_exists($key, $additionalData)) {
            throw new \InvalidArgumentException(sprintf(
                'Can not find field "%s" in additional data',
                $key
            ));
        }

        return $additionalData[$key];
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param IngenicoConfig $config
     * @param array $additionalRequestOptions
     * @return PaymentResponse
     */
    protected function requestCreatePayment(
        PaymentTransaction $paymentTransaction,
        IngenicoConfig $config,
        array $additionalRequestOptions = []
    ): PaymentResponse {
        $customerEncryptedDetails = $this->getAdditionalDataFieldByKey(
            $paymentTransaction,
            self::CUSTOMER_ENC_DETAILS_OPTION_KEY
        );

        $requestOptions = [
            EncryptedCustomerInput::NAME => $customerEncryptedDetails,
            AmountOfMoney\Amount::NAME => $this->normalizeAmount($paymentTransaction),
            AmountOfMoney\CurrencyCode::NAME => $paymentTransaction->getCurrency(),
            MerchantReference::NAME => sprintf('oroCommerceOrder:%d', $paymentTransaction->getEntityIdentifier()),
        ];

        $checkoutOptions = $this->checkoutInformationProvider->getCheckoutOptions($paymentTransaction);

        $response = $this->gateway->request(
            $config,
            $this->getCreatePaymentTransactionType(),
            array_merge($requestOptions, $checkoutOptions, $additionalRequestOptions)
        );

        return PaymentResponse::create($response->toArray());
    }

    /**
     * @return string
     */
    abstract protected function getCreatePaymentTransactionType(): string;

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param IngenicoConfig $config
     * @param array $additionalRequestOptions
     * @return PaymentResponse
     */
    protected function requestApprovePayment(
        PaymentTransaction $paymentTransaction,
        IngenicoConfig $config,
        array $additionalRequestOptions = []
    ): PaymentResponse {
        $requestOptions = [
            Capture\Amount::NAME => $this->normalizeAmount($paymentTransaction),
        ];

        $response = $this->gateway->request(
            $config,
            $this->getApprovePaymentTransactionType(),
            array_merge($requestOptions, $additionalRequestOptions),
            [PaymentId::NAME => $paymentTransaction->getReference()]
        );

        return PaymentResponse::create($response->toArray());
    }

    /**
     * @return string
     */
    protected function getApprovePaymentTransactionType(): string
    {
        return Transaction::APPROVE_PAYMENT;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @return int
     */
    protected function normalizeAmount(PaymentTransaction $paymentTransaction)
    {
        return $this->amountNormalizer->normalize($paymentTransaction->getAmount());
    }

    /**
     * @return string
     */
    abstract protected function getType(): string;

    /**
     * Check that this action is supported by payment product handler
     *
     * @param string $actionName
     * @return bool
     */
    abstract protected function isActionSupported(string $actionName): bool;
}
