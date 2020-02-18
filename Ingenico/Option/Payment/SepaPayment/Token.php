<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * Option for handling token field in SEPA-related create payment request.
 */
class Token implements OptionInterface
{
    public const NAME = '[sepaDirectDebitPaymentMethodSpecificInput][token]';
    private const MAX_LENGTH = 40;

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
                        'Incorrect token. Max length %d, but value "%s" exceeded it',
                        self::MAX_LENGTH,
                        $value
                    ));
                }

                return $value;
            });
    }
}
