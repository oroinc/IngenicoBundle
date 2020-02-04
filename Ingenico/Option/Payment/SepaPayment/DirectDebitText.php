<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handle direct debit text in SEPA-related create payment request
 */
class DirectDebitText implements OptionInterface
{
    public const NAME = '[sepaDirectDebitPaymentMethodSpecificInput][directDebitText]';

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
