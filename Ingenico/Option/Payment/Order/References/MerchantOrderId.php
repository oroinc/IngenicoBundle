<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Order\References;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * Option for merchant order id field
 */
class MerchantOrderId implements OptionInterface
{
    public const NAME = '[order][references][merchantOrderId]';
    private const MAX_LENGTH = 10;

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
                    throw new InvalidOptionsException(sprintf(
                        'Incorrect merchant order id. Max length %d, but value "%s" exceeded it',
                        self::MAX_LENGTH,
                        $value
                    ));
                }

                return $value;
            });
    }
}
