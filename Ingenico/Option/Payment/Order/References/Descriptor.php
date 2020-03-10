<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Order\References;

use Ingenico\Connect\OroCommerce\Ingenico\Option\LengthNormalizerTrait;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;

/**
 * Option for descriptor field
 *
 * @codingStandardsIgnoreStart
 * @see https://epayments-api.developer-ingenico.com/s2sapi/v1/en_US/java/payments/create.html?paymentPlatform=ALL#payments-create-payload
 * @codingStandardsIgnoreEnd
 */
class Descriptor implements OptionInterface
{
    use LengthNormalizerTrait;

    public const NAME = '[order][references][descriptor]';

    /**
     * From the doc:
     *  Note that we advise you to use 22 characters as the max length as beyond this our experience is that issuers
     *  will start to truncate.
     */
    private const MAX_LENGTH = 22;

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
