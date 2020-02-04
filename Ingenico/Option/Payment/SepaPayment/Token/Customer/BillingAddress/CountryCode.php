<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Customer\BillingAddress;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling country code of customer who bills in SEPA-related payment token requests.
 */
class CountryCode implements OptionInterface
{
    public const NAME = '[sepaDirectDebit][customer][billingAddress][countryCode]';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault(self::NAME, 'US')
            ->setAllowedTypes(self::NAME, 'string');
    }
}
