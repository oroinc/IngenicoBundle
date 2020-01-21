<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Order\AmountOfMoney;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling order amount of money currency code.
 */
class CurrencyCode implements OptionInterface
{
    public const NAME = '[order][amountOfMoney][currencyCode]';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(self::NAME)
            ->setAllowedTypes(self::NAME, 'string');
    }
}
