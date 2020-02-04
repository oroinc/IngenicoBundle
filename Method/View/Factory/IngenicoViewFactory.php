<?php

namespace Ingenico\Connect\OroCommerce\Method\View\Factory;

use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Method\View\IngenicoView;
use Ingenico\Connect\OroCommerce\Normalizer\AmountNormalizer;
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

    /** @var AmountNormalizer */
    private $amountNormalizer;

    /**
     * @param LocalizationHelper $localizationHelper
     * @param LocalizationManager $localizationManager
     * @param AmountNormalizer $amountNormalizer
     */
    public function __construct(
        LocalizationHelper $localizationHelper,
        LocalizationManager $localizationManager,
        AmountNormalizer $amountNormalizer
    ) {
        $this->localizationHelper = $localizationHelper;
        $this->localizationManager = $localizationManager;
        $this->amountNormalizer = $amountNormalizer;
    }

    /**
     * @param IngenicoConfig $config
     * @return IngenicoView
     */
    public function create(IngenicoConfig $config): IngenicoView
    {
        return new IngenicoView($config, $this->getLocalizationCode(), $this->amountNormalizer);
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
