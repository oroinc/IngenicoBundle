<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Request\Payments;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\PaymenProducttId;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Customer\BillingAddress\CountryCode;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Mandate\BankAccountIban\AccountHolderName;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Mandate\BankAccountIban\Iban;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Mandate\DebtorSurname;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Mandate\MandateApproval\MandateSignatureDate;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Mandate\MandateApproval\MandateSigned;
use Ingenico\Connect\OroCommerce\Ingenico\Request\RequestInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\Sdk\DataObject;
use Ingenico\Connect\Sdk\Domain\Token\CreateTokenRequest;

/**
 * Handles create mandate request for SEPA payment products
 */
class CreateSepaDirectDebitPaymentTokenRequest implements RequestInterface
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->addOption(new CountryCode())
            ->addOption(new AccountHolderName())
            ->addOption(new Iban())
            ->addOption(new MandateSignatureDate())
            ->addOption(new MandateSigned())
            ->addOption(new DebtorSurname())
            ->addOption(new PaymenProducttId());
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionType(): string
    {
        return Transaction::CREATE_SEPA_DIRECT_DEBIT_PAYMENT_TOKEN;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource(): string
    {
        return 'tokens';
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
        return new CreateTokenRequest();
    }
}
