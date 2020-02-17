<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\DirectDebitPayment;

use Ingenico\Connect\OroCommerce\Ingenico\Option\LengthNormalizerTrait;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handle direct debit text
 */
class DirectDebitText implements OptionInterface
{
    use LengthNormalizerTrait;

    public const NAME = '[directDebitPaymentMethodSpecificInput][directDebitText]';
    private const MAX_LENGTH = 50;

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
