<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Request\Payments;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\DirectDebitText;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;

/**
 * Handles create product request for SEPA payment products
 */
class CreateSepaDirectDebitPaymentRequest extends CreatePaymentRequest
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->addOption(new DirectDebitText())
            ->addOption(new Token());
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionType(): string
    {
        return Transaction::CREATE_SEPA_DIRECT_DEBIT_PAYMENT;
    }
}
