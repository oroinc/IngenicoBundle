<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Request\Payments;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\AuthorizationMode;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment\Token;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;

/**
 * Handle create payment request specificly for credit card payment products.
 */
class CreateCardsPaymentRequest extends CreatePaymentRequest
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->addOption(new AuthorizationMode())
            ->addOption(new Token());
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionType(): string
    {
        return Transaction::CREATE_CARDS_PAYMENT;
    }
}
