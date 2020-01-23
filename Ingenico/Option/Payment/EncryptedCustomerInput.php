<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling encryptedCustomerInput.
 */
class EncryptedCustomerInput implements OptionInterface
{
    public const NAME = '[encryptedCustomerInput]';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(self::NAME)
            ->setAllowedValues(self::NAME, 'string');
    }
}