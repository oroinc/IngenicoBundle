<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option;

/**
 * Interface for Option object.
 */
interface OptionInterface
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void;
}
