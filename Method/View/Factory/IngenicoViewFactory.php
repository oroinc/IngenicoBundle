<?php

namespace Ingenico\Connect\OroCommerce\Method\View\Factory;

use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Method\View\IngenicoView;
use Oro\Bundle\CurrencyBundle\Rounding\RoundingServiceInterface;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\LocaleBundle\Manager\LocalizationManager;

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

    /**
     * @param LocalizationHelper $localizationHelper
     * @param LocalizationManager $localizationManager
     * @param RoundingServiceInterface $rounding
     */
    public function __construct(
        LocalizationHelper $localizationHelper,
        LocalizationManager $localizationManager,
        RoundingServiceInterface $rounding
    ) {
        $this->localizationHelper = $localizationHelper;
        $this->localizationManager = $localizationManager;
        $this->rounding = $rounding;
    }

    /**
     * @param IngenicoConfig $config
     *
     * @return IngenicoView
     */
    public function create(IngenicoConfig $config): IngenicoView
    {
        return new IngenicoView($config, $this->getLocalizationCode(), $this->rounding);
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
