<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Mandate\BankAccountIban;

use Ingenico\Connect\OroCommerce\Ingenico\Option\LengthNormalizerTrait;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * Option for handling of IBAN number in SEPA-related payment token requests
 */
class Iban implements OptionInterface
{
    use LengthNormalizerTrait;

    public const NAME = '[sepaDirectDebit][mandate][bankAccountIban][iban]';
    private const MAX_LENGTH = 50;

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
                        'Incorrect iban. Max length %d, but value "%s" exceeded it',
                        self::MAX_LENGTH,
                        $value
                    ));
                }

                return $value;
            });
    }
}
