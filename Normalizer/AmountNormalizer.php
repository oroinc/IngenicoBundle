<?php

namespace Ingenico\Connect\OroCommerce\Normalizer;

use Oro\Bundle\CurrencyBundle\Rounding\RoundingServiceInterface;

/**
 * Normalize float amount to the amount in cents
 */
class AmountNormalizer
{
    /**
     * @var RoundingServiceInterface
     */
    private $roundingService;

    /**
     * @param RoundingServiceInterface $roundingService
     */
    public function __construct(RoundingServiceInterface $roundingService)
    {
        $this->roundingService = $roundingService;
    }

    /**
     * Normalize float amount to the amount in cents
     *
     * @param float $amount
     * @return int
     */
    public function normalize(float $amount): int
    {
        // Ingenico accepts amount in cents
        $amountInCents = $amount * 100;

        return (int)$this->roundingService->round($amountInCents, 0);
    }
}
