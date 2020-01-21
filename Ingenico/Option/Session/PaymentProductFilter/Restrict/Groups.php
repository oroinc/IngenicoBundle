<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Session\PaymentProductFilter\Restrict;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling payment product filters.
 */
class Groups implements OptionInterface
{
    public const NAME = '[paymentProductFilter][restrictTo][groups]';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined(self::NAME)
            ->setAllowedTypes(self::NAME, 'array');
    }
}
