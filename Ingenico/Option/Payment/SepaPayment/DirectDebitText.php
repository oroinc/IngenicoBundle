<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment;

use Ingenico\Connect\OroCommerce\Ingenico\Option\LengthNormalizerTrait;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handle direct debit text in SEPA-related create payment request
 */
class DirectDebitText implements OptionInterface
{
    use LengthNormalizerTrait;

    public const NAME = '[sepaDirectDebitPaymentMethodSpecificInput][directDebitText]';
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
