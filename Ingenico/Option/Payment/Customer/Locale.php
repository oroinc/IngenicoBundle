<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling order customer locale.
 */
class Locale implements OptionInterface
{
    public const NAME = '[order][customer][locale]';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault(self::NAME, 'en_US')
            ->setAllowedTypes(self::NAME, 'string');
    }
}
