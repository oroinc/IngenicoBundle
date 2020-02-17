<?php

namespace Ingenico\Connect\OroCommerce\Ingenico;

/**
 * Represents all available transaction list.
 */
final class Transaction
{
    public const CREATE_SESSION = 'createSession';
    public const CREATE_PAYMENT = 'createPayment';
    public const CREATE_CARDS_PAYMENT = 'createPayment.cards';
    public const CREATE_DIRECT_DEBIT_PAYMENT = 'createPayment.directDebit';
    public const CREATE_SEPA_DIRECT_DEBIT_PAYMENT = 'createPayment.directDebit.sepa';
    public const CREATE_SEPA_DIRECT_DEBIT_PAYMENT_TOKEN = 'createToken.directDebit.sepa';
    public const APPROVE_PAYMENT = 'approvePayment';
}
