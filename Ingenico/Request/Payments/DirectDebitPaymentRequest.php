<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Request\Payments;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\DirectDebitPayment\DirectDebitText;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;

/**
 * Handle create payment request specifically for direct debit payment products(ACH, SEPA).
 */
class DirectDebitPaymentRequest extends CreatePaymentRequest
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->addOption(new DirectDebitText());
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionType(): string
    {
        return Transaction::CREATE_DIRECT_DEBIT_PAYMENT;
    }
}
