<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\BillingAddress\Address;

use Ingenico\Connect\OroCommerce\Ingenico\Option\LengthNormalizerTrait;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling order customer billingAddress state or region name.
 */
class State implements OptionInterface
{
    use LengthNormalizerTrait;

    public const NAME = '[order][customer][billingAddress][state]';
    private const MAX_LENGTH = 35;

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
