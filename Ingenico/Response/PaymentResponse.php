<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Response;

/**
 * Responsible for storing common payment request's response data from Ingenico server
 */
class PaymentResponse extends Response
{
    public const PAYMENT_ID = 'payment.id';
    public const PAYMENT_STATUS = 'payment.status';

    /**
     * @return string
     */
    public function getReference(): ?string
    {
        return $this->offsetGetByPath(self::PAYMENT_ID);
    }

    /**
     * @return string
     */
    public function getPaymentStatus(): ?string
    {
        return $this->offsetGetByPath(self::PAYMENT_STATUS);
    }
}
