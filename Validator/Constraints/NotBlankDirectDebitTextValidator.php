<?php

namespace Ingenico\Connect\OroCommerce\Validator\Constraints;

use Ingenico\Connect\OroCommerce\Entity\IngenicoSettings;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\EnabledProductsDataProvider;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validator for NotBlankDirectDebitText constraint
 */
class NotBlankDirectDebitTextValidator extends ConstraintValidator
{
    /** @var EnabledProductsDataProvider */
    private $enabledProductsDataProvider;

    /**
     * @param EnabledProductsDataProvider $enabledProductsDataProvider
     */
    public function __construct(EnabledProductsDataProvider $enabledProductsDataProvider)
    {
        $this->enabledProductsDataProvider = $enabledProductsDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof NotBlankDirectDebitText) {
            return;
        }

        if (!$value instanceof Channel) {
            return;
        }

        $transport = $value->getTransport();
        if (!$transport instanceof IngenicoSettings) {
            return;
        }

        $intersect = array_intersect(
            $this->enabledProductsDataProvider->getDirectDebitProducts(),
            $transport->getEnabledProducts()
        );
        if (!empty($intersect) && empty($transport->getDirectDebitText())) {
            $this->context->buildViolation($constraint->message)
                ->atPath('transport.directDebitText')
                ->addViolation();
        }
    }
}
