<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Ingenico\Connect\OroCommerce\Ingenico\Gateway\Gateway;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\ActionParams\PaymentId;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Capture;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\EncryptedCustomerInput;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Order\AmountOfMoney;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Order\References\MerchantReference;
use Ingenico\Connect\OroCommerce\Ingenico\Response\PaymentResponse;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;

/**
 * Abstract(generic) class for Ingenico payment products handler
 */
abstract class AbstractPaymentProductHandler implements PaymentProductHandlerInterface
{
    const PAYMENT_PRODUCT_OPTION_KEY = 'ingenicoPaymentProduct';
    const CUSTOMER_ENC_DETAILS_OPTION_KEY = 'ingenicoCustomerEncDetails';

    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(PaymentTransaction $paymentTransaction): bool
    {
        // using source transaction's(if exists) options for new transaction
        $paymentTransaction =
            $paymentTransaction->getId() === null && $paymentTransaction->getSourcePaymentTransaction() ?
                $paymentTransaction->getSourcePaymentTransaction() : $paymentTransaction;

        $paymentProduct = (string)$this->getTransactionOption(
            $paymentTransaction,
            static::PAYMENT_PRODUCT_OPTION_KEY
        );

        return $this->getType() === $paymentProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(
        string $action,
        PaymentTransaction $paymentTransaction,
        PaymentConfigInterface $config
    ): array {
        if (!method_exists($this, $action)) {
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
     * @param PaymentConfigInterface $config
     * @return array
     * @throws \JsonException
     */
    public function capture(
        PaymentTransaction $paymentTransaction,
        PaymentConfigInterface $config
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
            'successful' => $response->isSuccessful()
        ];
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param string $optionKey
     * @return mixed
     */
    protected function getTransactionOption(PaymentTransaction $paymentTransaction, string $optionKey)
    {
        $transactionOptions = $paymentTransaction->getTransactionOptions();
        $additionalData = @json_decode($transactionOptions['additionalData'] ?? null, true);

        return $additionalData[$optionKey] ?? null;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param PaymentConfigInterface $config
     * @return PaymentResponse
     * @throws \JsonException
     */
    protected function requestCreatePayment(
        PaymentTransaction $paymentTransaction,
        PaymentConfigInterface $config
    ): PaymentResponse {
        $requestOptions = [
            EncryptedCustomerInput::NAME => $this->getTransactionOption(
                $paymentTransaction,
                self::CUSTOMER_ENC_DETAILS_OPTION_KEY
            ),
            AmountOfMoney\Amount::NAME => (int)($paymentTransaction->getAmount() * 100),
            AmountOfMoney\CurrencyCode::NAME => $paymentTransaction->getCurrency(),
            MerchantReference::NAME => sprintf('oroCommerceOrder:%d', $paymentTransaction->getEntityIdentifier())
        ];

        $response = $this->gateway->request(
            $config,
            $this->getCreatePaymentTransactionType(),
            array_merge($requestOptions, $this->getCreatePaymentOptions($paymentTransaction, $config))
        );

        return PaymentResponse::create($response->toArray());
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param PaymentConfigInterface $config
     * @return array
     */
    protected function getCreatePaymentOptions(
        PaymentTransaction $paymentTransaction,
        PaymentConfigInterface $config
    ): array {
        return [];
    }

    /**
     * @return string
     */
    protected function getCreatePaymentTransactionType(): string
    {
        return Transaction::CREATE_PAYMENT;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param PaymentConfigInterface $config
     * @return PaymentResponse
     * @throws \JsonException
     */
    protected function requestApprovePayment(
        PaymentTransaction $paymentTransaction,
        PaymentConfigInterface $config
    ): PaymentResponse {
        $requestOptions = [
            Capture\Amount::NAME => (int)($paymentTransaction->getAmount() * 100)
        ];

        $response = $this->gateway->request(
            $config,
            $this->getApprovePaymentTransactionType(),
            array_merge($requestOptions, $this->getApprovePaymentOptions($paymentTransaction, $config)),
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
     * @param PaymentConfigInterface $config
     * @return array
     */
    protected function getApprovePaymentOptions(
        PaymentTransaction $paymentTransaction,
        PaymentConfigInterface $config
    ): array {
        return [];
    }

    /**
     * @return string
     */
    abstract protected function getType(): string;
}
