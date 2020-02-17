<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * Option for handling payment ID action param.
 */
class PaymentProductId implements OptionInterface
{
    public const NAME = '[paymentProductId]';
    private const MAX_LENGTH = 5;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(self::NAME)
            ->setAllowedTypes(self::NAME, 'int')
            ->setNormalizer(self::NAME, function (OptionsResolver $resolver, $value) {
                if (strlen($value) > self::MAX_LENGTH) {
                    throw new InvalidOptionsException(
                        sprintf(
                            'Payment product id max length is "%d", but value ("%s") exceeded it',
                            self::MAX_LENGTH,
                            $value
                        )
                    );
                }

                return $value;
            });
    }
}
