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
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Gateway $gateway
     * @param AmountNormalizer $amountNormalizer
     * @param CheckoutInformationProvider $checkoutInformationProvider
     * @param LoggerInterface $logger
     */
    public function __construct(
        Gateway $gateway,
        AmountNormalizer $amountNormalizer,
        CheckoutInformationProvider $checkoutInformationProvider,
        LoggerInterface $logger
    ) {
        $this->gateway = $gateway;
        $this->amountNormalizer = $amountNormalizer;
        $this->checkoutInformationProvider = $checkoutInformationProvider;
        $this->logger = $logger;
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
                    'Payment product handler "%s" for payment method "%s" doesn\'t support "%s" action',
                    static::class,
                    $config->getPaymentMethodIdentifier(),
                    $action
                )
            );
        }

        return $this->$action($paymentTransaction, $config) ?: [];
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param string $key
     * @param bool $throwException
     * @return mixed
     */
    protected function getAdditionalDataFieldByKey(
        PaymentTransaction $paymentTransaction,
        string $key,
        $throwException = true
    ) {
        $transactionOptions = $paymentTransaction->getTransactionOptions();
        $additionalData = json_decode($transactionOptions['additionalData'], true);

        if ($throwException && !array_key_exists($key, $additionalData)) {
            throw new \InvalidArgumentException(sprintf(
                'Can not find field "%s" in additional data',
                $key
            ));
        }

        return $additionalData[$key] ?? null;
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
            self::CUSTOMER_ENC_DETAILS_OPTION_KEY,
            false
        );

        $requestOptions = [
            EncryptedCustomerInput::NAME => $customerEncryptedDetails,
            AmountOfMoney\Amount::NAME => $this->normalizeAmount($paymentTransaction),
            AmountOfMoney\CurrencyCode::NAME => $paymentTransaction->getCurrency(),
            MerchantReference::NAME => $this->generateMerchantReference($paymentTransaction),
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
            Transaction::APPROVE_PAYMENT,
            array_merge($requestOptions, $additionalRequestOptions),
            [PaymentId::NAME => $paymentTransaction->getReference()]
        );

        return PaymentResponse::create($response->toArray());
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @return int
     */
    protected function normalizeAmount(PaymentTransaction $paymentTransaction): int
    {
        return $this->amountNormalizer->normalize($paymentTransaction->getAmount());
    }

    /**
     * Generate merchant reference for the payment transaction on the Ingenico side
     * This reference must be unique.
     * This method uses next format: "o:<order_id>:n:<random_nonce>"
     *
     * @param PaymentTransaction $paymentTransaction
     * @return string
     */
    protected function generateMerchantReference(PaymentTransaction $paymentTransaction)
    {
        return sprintf('o:%d:n:%s', $paymentTransaction->getEntityIdentifier(), uniqid());
    }

    /**
     * @return string
     */
    abstract protected function getCreatePaymentTransactionType(): string;

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
