<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Mandate\BankAccountIban;

use Ingenico\Connect\OroCommerce\Ingenico\Option\LengthNormalizerTrait;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling of IBAN account holder name in SEPA-related payment token requests
 */
class AccountHolderName implements OptionInterface
{
    use LengthNormalizerTrait;

    public const NAME = '[sepaDirectDebit][mandate][bankAccountIban][accountHolderName]';
    private const MAX_LENGTH = 30;

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
