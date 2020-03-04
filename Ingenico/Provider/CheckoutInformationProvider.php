<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Provider;

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
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\MerchantCustomerId;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\PersonalInformation\FirstName;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\PersonalInformation\Surname;
use Oro\Bundle\AddressBundle\Entity\AbstractAddress;
use Oro\Bundle\CheckoutBundle\Entity\Checkout;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;

/**
 * Extracts information (like billing address) from the checkout (if possible) to pass it to the payment system
 */
class CheckoutInformationProvider
{
    /**
     * @var DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @return array
     */
    public function getCheckoutOptions(PaymentTransaction $paymentTransaction): array
    {
        return array_merge(
            $this->getBillingAddressOptions($paymentTransaction),
            $this->getCustomerUserOptions($paymentTransaction)
        );
    }

    /**
     * @param $paymentTransaction
     * @return AbstractAddress|null
     */
    public function getBillingAddress(PaymentTransaction $paymentTransaction): ?AbstractAddress
    {
        $checkout = $this->extractCheckout($paymentTransaction);

        if (!$checkout) {
            return null;
        }

        return $checkout->getBillingAddress();
    }

    /**
     * @param $paymentTransaction
     * @return array
     */
    private function getBillingAddressOptions(PaymentTransaction $paymentTransaction)
    {
        $billingAddress = $this->getBillingAddress($paymentTransaction);

        if (!$billingAddress) {
            return [];
        }

        return [
            CountryCode::NAME => $billingAddress->getCountryIso2(),
            City::NAME => $billingAddress->getCity(),
            StateCode::NAME => $billingAddress->getRegionCode(),
            State::NAME => $billingAddress->getRegionName(),
            Street::NAME => $billingAddress->getStreet(),
            HouseNumber::NAME => $billingAddress->getStreet2(),
            Zip::NAME => $billingAddress->getPostalCode(),
            PhoneNumber::NAME => $billingAddress->getPhone(),
        ];
    }

    /**
     * @param $paymentTransaction
     * @return array
     */
    private function getCustomerUserOptions(PaymentTransaction $paymentTransaction)
    {
        $checkout = $this->extractCheckout($paymentTransaction);

        if (!$checkout) {
            return [];
        }

        $customerUser = $checkout->getCustomerUser();

        if (!$customerUser instanceof CustomerUser) {
            return [];
        }

        $customerUserOptions = [
            EmailAddress::NAME => $customerUser->getEmail(),
            MerchantCustomerId::NAME => $customerUser->getId(),
        ];

        $firstName = $customerUser->getFirstName();
        if ($firstName) {
            $customerUserOptions[FirstName::NAME] = $firstName;
        }

        $lastName = $customerUser->getLastName();
        if ($lastName) {
            $customerUserOptions[Surname::NAME] = $lastName;
        }

        $customer = $customerUser->getCustomer();
        if ($customer) {
            $customerUserOptions[CompanyName::NAME] = $customer->getName();
        }

        return $customerUserOptions;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @return Checkout|null
     */
    private function extractCheckout(PaymentTransaction $paymentTransaction)
    {
        $transactionOptions = $paymentTransaction->getTransactionOptions();
        if (!isset($transactionOptions['checkoutId'])) {
            return null;
        }

        return $this->doctrineHelper->getEntity(Checkout::class, $transactionOptions['checkoutId']);
    }
}
