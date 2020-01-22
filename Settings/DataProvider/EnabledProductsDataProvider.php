<?php

namespace Ingenico\Connect\OroCommerce\Settings\DataProvider;

/**
 * Returns list of enabled products for Ingenico payment settings.
 */
class EnabledProductsDataProvider
{
    public const CREDIT_CARDS = 'cards';
    public const SEPA = 'sepa';
    public const ACH = 'ach';

    // Ingenico's hardcoded payment product IDs values
    public const SEPA_ID = 770;
    public const ACH_ID = 730;

    /**
     * @return array
     */
    public function getEnabledProducts(): array
    {
        return [
            self::CREDIT_CARDS,
            self::SEPA,
            self::ACH,
        ];
    }
}
