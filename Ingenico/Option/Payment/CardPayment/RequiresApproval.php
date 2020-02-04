<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\CardPayment;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option to configure that is approval(delayed capture) required or not
 *
 * true = the payment requires approval before the funds will be captured using the Approve payment or Capture API
 * false = the payment does not require approval, and the funds will be captured automatically
 */
class RequiresApproval implements OptionInterface
{
    public const NAME = '[cardPaymentMethodSpecificInput][requiresApproval]';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(self::NAME)
            ->setAllowedTypes(self::NAME, 'bool');
    }
}
