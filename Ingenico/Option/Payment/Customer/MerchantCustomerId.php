<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer;

use Ingenico\Connect\OroCommerce\Ingenico\Option\LengthNormalizerTrait;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling order customer ID.
 */
class MerchantCustomerId implements OptionInterface
{
    use LengthNormalizerTrait;

    public const NAME = '[order][customer][merchantCustomerId]';
    private const MAX_LENGTH = 16;

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
