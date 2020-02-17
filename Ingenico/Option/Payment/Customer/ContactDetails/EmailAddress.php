<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\ContactDetails;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * Option for handling order customer email address.
 */
class EmailAddress implements OptionInterface
{
    public const NAME = '[order][customer][contactDetails][emailAddress]';
    private const MAX_LENGTH = 70;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(self::NAME)
            ->setAllowedTypes(self::NAME, 'string')
            ->setNormalizer(self::NAME, function (OptionsResolver $resolver, $value) {
                if (strlen($value) > self::MAX_LENGTH) {
                    throw new InvalidOptionsException(sprintf(
                        'Incorrect email address. Max length %d, but value "%s" exceeded it',
                        self::MAX_LENGTH,
                        $value
                    ));
                }

                return $value;
            });
    }
}
