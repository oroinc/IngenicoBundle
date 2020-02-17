<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer;

use Ingenico\Connect\OroCommerce\Ingenico\Option\LengthNormalizerTrait;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling order customer locale.
 */
class Locale implements OptionInterface
{
    use LengthNormalizerTrait;

    public const NAME = '[order][customer][locale]';
    private const MAX_LENGTH = 6;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault(self::NAME, 'en_US')
            ->setAllowedTypes(self::NAME, 'string')
            ->setNormalizer(self::NAME, $this->getLengthNormalizer(self::MAX_LENGTH));
    }
}
