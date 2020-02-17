<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Customer\BillingAddress;

use Ingenico\Connect\OroCommerce\Ingenico\Option\LengthNormalizerTrait;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling country code of customer who bills in SEPA-related payment token requests.
 */
class CountryCode implements OptionInterface
{
    use LengthNormalizerTrait;

    public const NAME = '[sepaDirectDebit][customer][billingAddress][countryCode]';
    private const MAX_LENGTH = 2;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(self::NAME)
            ->setAllowedTypes(self::NAME, 'string')
            ->setNormalizer(self::NAME, $this->getLengthNormalizer(self::MAX_LENGTH));
    }
}
