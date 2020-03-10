<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Request\Payments;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\BillingAddress\Address\City;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\BillingAddress\Address\CountryCode;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\BillingAddress\Address\HouseNumber;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\BillingAddress\Address\State;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\BillingAddress\Address\StateCode;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\BillingAddress\Address\Street;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\BillingAddress\Address\Zip;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\ContactDetails\CompanyName;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\ContactDetails\EmailAddress;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\ContactDetails\PhoneNumber;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\Locale;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\MerchantCustomerId;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\PersonalInformation\FirstName;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\PersonalInformation\Surname;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\EncryptedCustomerInput;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Order\AmountOfMoney;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Order\References\Descriptor;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Order\References\MerchantOrderId;
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
            ->addOption(new State())
            ->addOption(new StateCode())
            ->addOption(new City())
            ->addOption(new Street())
            ->addOption(new HouseNumber())
            ->addOption(new Zip())
            ->addOption(new EncryptedCustomerInput())
            ->addOption(new MerchantCustomerId())
            ->addOption(new CompanyName())
            ->addOption(new EmailAddress())
            ->addOption(new PhoneNumber())
            ->addOption(new FirstName())
            ->addOption(new Surname())
            ->addOption(new MerchantReference())
            ->addOption(new MerchantOrderId())
            ->addOption(new Descriptor());
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
