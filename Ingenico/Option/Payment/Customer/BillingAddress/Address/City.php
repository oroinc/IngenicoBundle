<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\BillingAddress\Address;

use Ingenico\Connect\OroCommerce\Ingenico\Option\LengthNormalizerTrait;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling order customer billingAddress city.
 */
class City implements OptionInterface
{
    use LengthNormalizerTrait;

    public const NAME = '[order][customer][billingAddress][city]';
    private const MAX_LENGTH = 40;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined(self::NAME)
            ->setAllowedTypes(self::NAME, 'string')
            ->setNormalizer(self::NAME, $this->getLengthNormalizer(self::MAX_LENGTH));
    }
}
