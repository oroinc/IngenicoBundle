<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option;

/**
 * This trait contains a function that returns callback that can be used as normalizer for OptionsResolver
 */
trait LengthNormalizerTrait
{
    /**
     * @param int $length
     * @return \Closure
     */
    protected function getLengthNormalizer($length)
    {
        return function (OptionsResolver $resolver, $value) use ($length) {
            return substr($value, 0, $length);
        };
    }
}
