<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Mandate\MandateApproval;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling of mandate signature date in SEPA-related payment token requests
 */
class MandateSignatureDate implements OptionInterface
{
    public const NAME = '[sepaDirectDebit][mandate][mandateApproval][mandateSignatureDate]';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $utcNowDate = new \DateTime('now', new \DateTimeZone('UTC'));
        $resolver
            ->setDefault(self::NAME, $utcNowDate->format('Ymd'))
            ->setAllowedTypes(self::NAME, 'string');
    }
}
