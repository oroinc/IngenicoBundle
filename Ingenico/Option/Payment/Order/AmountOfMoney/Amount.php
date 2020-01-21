<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Order\AmountOfMoney;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling order amount of money.
 */
class Amount implements OptionInterface
{
    public const NAME = '[order][amountOfMoney][amount]';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(self::NAME)
            ->setAllowedTypes(self::NAME, 'int');
    }
}
