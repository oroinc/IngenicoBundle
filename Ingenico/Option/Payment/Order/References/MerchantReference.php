<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Order\References;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handle card's payment authorization mode.
 */
class MerchantReference implements OptionInterface
{
    public const NAME = '[order][references][merchantReference]';

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
