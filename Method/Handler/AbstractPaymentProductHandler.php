<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Ingenico\Connect\OroCommerce\Ingenico\Gateway\Gateway;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;

/**
 * Abstract class for Ingenico payment product group handler
 */
abstract class AbstractPaymentProductHandler implements PaymentProductHandlerInterface
{
    const PAYMENT_PRODUCT_OPTION_KEY = 'ingenicoPaymentProduct';
    const CUSTOMER_ENC_DETAILS_OPTION_KEY = 'ingenicoCustomerEncDetails';

    protected $gateway;

    /**
     * @var string
     */
    protected $paymentProductGroupType;

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

        return $this->$action($paymentTransaction, $config);
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param string $optionKey
     * @return mixed
     */
    protected function getTransactionOption(PaymentTransaction $paymentTransaction, string $optionKey)
    {
        $transactionOptions = $paymentTransaction->getTransactionOptions();
        $additionalData = $transactionOptions['additionalData'] ?? [];

        return $additionalData[$optionKey] ?? null;
    }

    /**
     * @return string
     */
    abstract protected function getType(): string;
}
