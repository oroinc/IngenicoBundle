<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling payment ID action param.
 */
class PaymenProducttId implements OptionInterface
{
    public const NAME = '[paymentProductId]';

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
