<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handle card's payment token.
 */
class Token implements OptionInterface
{
    public const NAME = '[cardPaymentMethodSpecificInput][token]';

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined(self::NAME)
            ->setAllowedTypes(self::NAME, 'string');
    }
}
