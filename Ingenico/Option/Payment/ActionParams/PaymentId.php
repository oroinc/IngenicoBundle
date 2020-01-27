<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\ActionParams;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling payment ID action param.
 */
class PaymentId implements OptionInterface
{
    public const NAME = 'paymentId';

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
