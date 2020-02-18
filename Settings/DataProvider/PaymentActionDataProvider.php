<?php

namespace Ingenico\Connect\OroCommerce\Settings\DataProvider;

/**
 * Returns list of available payment action for Ingenico payment settings.
 */
class PaymentActionDataProvider
{
    public const SALE = 'SALE';
    public const PRE_AUTHORIZATION = 'PRE_AUTHORIZATION';
    public const FINAL_AUTHORIZATION = 'FINAL_AUTHORIZATION';

    /**
     * @return array
     */
    public function getPaymentActions(): array
    {
        return [
            self::SALE,
            self::PRE_AUTHORIZATION,
            self::FINAL_AUTHORIZATION,
        ];
    }
}
