<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\DirectDebitPayment;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handle direct debit text
 */
class DirectDebitText implements OptionInterface
{
    public const NAME = '[directDebitPaymentMethodSpecificInput][directDebitText]';

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
