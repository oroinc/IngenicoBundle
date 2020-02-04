<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling token field in SEPA-related create payment request.
 */
class Token implements OptionInterface
{
    public const NAME = '[sepaDirectDebitPaymentMethodSpecificInput][token]';

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
