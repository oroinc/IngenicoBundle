<?php

namespace Ingenico\Connect\OroCommerce\Settings\DataProvider;

/**
 * Returns list of enabled products for Ingenico payment settings.
 */
class EnabledProductsDataProvider
{
    public const CREDIT_CARDS = 'cards';

    /**
     * @return array
     */
    public function getEnabledProducts(): array
    {
        return [
            self::CREDIT_CARDS,
        ];
    }
}
