<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\BillingAddress\Address;

use Ingenico\Connect\OroCommerce\Ingenico\Option\LengthNormalizerTrait;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling order customer billingAddress house number(street2).
 */
class HouseNumber implements OptionInterface
{
    use LengthNormalizerTrait;

    public const NAME = '[order][customer][billingAddress][houseNumber]';
    private const MAX_LENGTH = 15;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined(self::NAME)
            ->setAllowedTypes(self::NAME, ['string', 'null'])
            ->setNormalizer(self::NAME, $this->getLengthNormalizer(self::MAX_LENGTH));
    }
}
