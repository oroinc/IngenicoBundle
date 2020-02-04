<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\BillingAddress\Address;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling order customer billingAddress countryCode.
 */
class CountryCode implements OptionInterface
{
    public const NAME = '[order][customer][billingAddress][countryCode]';

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
