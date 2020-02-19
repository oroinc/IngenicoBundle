<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * Option for handle card's payment token.
 */
class Token implements OptionInterface
{
    public const NAME = '[cardPaymentMethodSpecificInput][token]';
    private const MAX_LENGTH = 40;

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined(self::NAME)
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
