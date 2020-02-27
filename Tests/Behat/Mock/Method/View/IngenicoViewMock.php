<?php

namespace Ingenico\Connect\OroCommerce\Tests\Behat\Mock\Method\View;

use Ingenico\Connect\OroCommerce\Method\View\IngenicoView;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;

class IngenicoViewMock extends IngenicoView
{
    /**
     * {@inheritdoc}
     */
    public function getOptions(PaymentContextInterface $context): array
    {
        return array_merge(parent::getOptions($context), [
            'testMode' => true,
        ]);
    }
}
