<?php

namespace Ingenico\Connect\OroCommerce\Settings\DataProvider;

/**
 * Returns list of enabled products for Ingenico payment settings.
 */
class EnabledProductsDataProvider
{
    // Constants for the available payment products in the system
    public const CREDIT_CARDS = 'cards';
    public const SEPA = 'sepa';
    public const ACH = 'ach';

    // Ingenico's payment product IDs
    public const CREDIT_CARDS_GROUP_ID = 'cards';
    public const SEPA_ID = 770;
    public const ACH_ID = 730;

    /**
     * @return array
     */
    public function getAvailableProducts(): array
    {
        return [
            self::CREDIT_CARDS,
            self::SEPA,
            self::ACH,
        ];
    }
}
