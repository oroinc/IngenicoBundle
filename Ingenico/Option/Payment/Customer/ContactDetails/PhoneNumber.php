<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\ContactDetails;

use Ingenico\Connect\OroCommerce\Ingenico\Option\LengthNormalizerTrait;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling order customer billing address phone number.
 */
class PhoneNumber implements OptionInterface
{
    use LengthNormalizerTrait;

    public const NAME = '[order][customer][contactDetails][phoneNumber]';
    private const MAX_LENGTH = 20;

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
