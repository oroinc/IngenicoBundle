<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Customer\ContactDetails;

use Ingenico\Connect\OroCommerce\Ingenico\Option\LengthNormalizerTrait;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for handling order customer company name.
 */
class CompanyName implements OptionInterface
{
    use LengthNormalizerTrait;

    public const NAME = '[order][customer][companyInformation][name]';
    private const MAX_LENGTH = 40;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined(self::NAME)
            ->setAllowedTypes(self::NAME, ['string', 'null'])
            ->setNormalizer(self::NAME, $this->getLengthNormalizer(self::MAX_LENGTH));
    }
}
