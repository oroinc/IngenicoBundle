<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Request;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Interface for request that should be aware of action params.
 */
interface ActionParamsAwareInterface
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureActionParamsOptions(OptionsResolver $resolver): void;
}
