<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Request\Payments;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\BillingAddress\Address\CountryCode;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\Locale;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\EncryptedCustomerInput;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Order\AmountOfMoney;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Order\References\MerchantReference;
use Ingenico\Connect\OroCommerce\Ingenico\Request\RequestInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\Sdk\DataObject;

/**
 * Handles create payment request.
 */
class CreatePaymentRequest implements RequestInterface
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->addOption(new AmountOfMoney\Amount())
            ->addOption(new AmountOfMoney\CurrencyCode())
            ->addOption(new Locale())
            ->addOption(new CountryCode())
            ->addOption(new EncryptedCustomerInput())
            ->addOption(new MerchantReference())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionType(): string
    {
        return Transaction::CREATE_PAYMENT;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource(): string
    {
        return 'payments';
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(): string
    {
        return 'create';
    }

    /**
     * {@inheritdoc}
     */
    public function createOriginalRequest(): DataObject
    {
        return new \Ingenico\Connect\Sdk\Domain\Payment\CreatePaymentRequest();
    }
}
