<?php

namespace Ingenico\Connect\OroCommerce\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint for validation direct debit fields based on enabled payment products in Ingenico integration
 */
class NotBlankDirectDebitText extends Constraint
{
    public $message = 'ingenico.direct_debit_text.not_blank';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
