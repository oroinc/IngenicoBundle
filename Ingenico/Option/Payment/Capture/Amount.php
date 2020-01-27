<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Capture;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling authorized trancation's amount of money to be captured.
 */
class Amount implements OptionInterface
{
    public const NAME = '[amount]';

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
