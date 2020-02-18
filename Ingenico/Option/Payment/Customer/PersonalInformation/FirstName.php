<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\PersonalInformation;

use Ingenico\Connect\OroCommerce\Ingenico\Option\LengthNormalizerTrait;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling order customer first name.
 */
class FirstName implements OptionInterface
{
    use LengthNormalizerTrait;

    public const NAME = '[order][customer][personalInformation][name][firstName]';
    private const MAX_LENGTH = 15;

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
