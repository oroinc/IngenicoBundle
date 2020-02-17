<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Mandate\MandateApproval;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling of mandate signed flag in SEPA-related payment token requests
 */
class MandateSigned implements OptionInterface
{
    public const NAME = '[sepaDirectDebit][mandate][mandateApproval][mandateSigned]';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault(self::NAME, true)
            ->setAllowedTypes(self::NAME, 'bool');
    }
}
