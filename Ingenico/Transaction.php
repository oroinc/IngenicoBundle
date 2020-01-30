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
    public const APPROVE_PAYMENT = 'approvePayment';
    public const CREATE_TOKEN = 'createToken';
}
