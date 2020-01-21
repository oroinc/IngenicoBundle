<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;

/**
 * Abstract class for Ingenico payment product group handler
 */
abstract class AbstractPaymentProductGroupHandler implements PaymentProductGroupHandlerInterface
{
    const PAYMENT_PRODUCT_GROUP_OPTION_KEY = 'ingenicoProductGroup';
    const CUSTOMER_ENC_DETAILS_OPTION_KEY = 'ingenicoCustomerEncDetails';

    protected $gateway;

    /**
     * @var string
     */
    protected $paymentProductGroupType;

    /**
     * @param string $gateway
     */
    public function __construct(string $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(PaymentTransaction $paymentTransaction): bool
    {
        $paymentProductGroup = (string)$this->getTransactionOption(
            $paymentTransaction,
            static::PAYMENT_PRODUCT_GROUP_OPTION_KEY
        );

        return $this->getType() === $paymentProductGroup;
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
