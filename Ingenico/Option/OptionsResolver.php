<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option;

use Symfony\Component\OptionsResolver\OptionsResolver as BaseOptionsResolver;

/**
 * Introduces addOption method for simplifying options manage.
 */
class OptionsResolver extends BaseOptionsResolver
{
    /**
     * @param OptionInterface $option
     *
     * @return OptionsResolver
     */
    public function addOption(OptionInterface $option): OptionsResolver
    {
        $option->configureOptions($this);

        return $this;
    }
}
