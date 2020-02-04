<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Mandate\BankAccountIban;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling of IBAN number in SEPA-related payment token requests
 */
class Iban implements OptionInterface
{
    public const NAME = '[sepaDirectDebit][mandate][bankAccountIban][iban]';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(self::NAME)
            ->setAllowedTypes(self::NAME, 'string');
    }
}
