<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handle card's payment authorization mode.
 */
class AuthorizationMode implements OptionInterface
{
    public const NAME = '[cardPaymentMethodSpecificInput][authorizationMode]';

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
