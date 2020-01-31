<?php

namespace Ingenico\Connect\OroCommerce\Method\View\Factory;

use Ingenico\Connect\OroCommerce\Method\View\IngenicoView;
use Ingenico\Connect\OroCommerce\Provider\PaymentTransactionProvider;
use Oro\Bundle\CurrencyBundle\Rounding\RoundingServiceInterface;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\LocaleBundle\Manager\LocalizationManager;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;

/**
 * Factory for creating views of Ingenico payment method
 */
class IngenicoViewFactory
{
    /** @var LocalizationHelper */
    private $localizationHelper;

    /** @var LocalizationManager */
    private $localizationManager;

    /** @var RoundingServiceInterface */
    private $rounding;

    /** @var PaymentTransactionProvider */
    private $paymentTransactionProvider;

    /**
     * @param LocalizationHelper $localizationHelper
     * @param LocalizationManager $localizationManager
     * @param RoundingServiceInterface $rounding
     * @param PaymentTransactionProvider $paymentTransactionProvider
     */
    public function __construct(
        LocalizationHelper $localizationHelper,
        LocalizationManager $localizationManager,
        RoundingServiceInterface $rounding,
        PaymentTransactionProvider $paymentTransactionProvider
    ) {
        $this->localizationHelper = $localizationHelper;
        $this->localizationManager = $localizationManager;
        $this->rounding = $rounding;
        $this->paymentTransactionProvider = $paymentTransactionProvider;
    }

    /**
     * @param PaymentConfigInterface $config
     *
     * @return IngenicoView
     */
    public function create(PaymentConfigInterface $config): IngenicoView
    {
        return new IngenicoView(
            $config,
            $this->getLocalizationCode(),
            $this->rounding,
            $this->paymentTransactionProvider
        );
    }

    /**
     * @return string
     */
    protected function getLocalizationCode()
    {
        $currentLocalization = $this->localizationHelper->getCurrentLocalization();
        if ($currentLocalization) {
            return $currentLocalization->getFormattingCode();
        }

        $defaultLocalization = $this->localizationManager->getDefaultLocalization();
        if ($defaultLocalization) {
            return $defaultLocalization->getFormattingCode();
        }

        throw new \InvalidArgumentException('Default localization should exist.');
    }
}
