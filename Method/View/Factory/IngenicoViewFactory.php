<?php

namespace Ingenico\Connect\OroCommerce\Method\View\Factory;

use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Method\View\IngenicoView;
use Ingenico\Connect\OroCommerce\Normalizer\AmountNormalizer;
use Ingenico\Connect\OroCommerce\Provider\PaymentTransactionProvider;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\LocaleBundle\Manager\LocalizationManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Factory for creating views of Ingenico payment method
 */
class IngenicoViewFactory
{
    /** @var LocalizationHelper */
    private $localizationHelper;

    /** @var LocalizationManager */
    private $localizationManager;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var AmountNormalizer */
    private $amountNormalizer;

    /** @var PaymentTransactionProvider */
    private $paymentTransactionProvider;

    /**
     * @param LocalizationHelper $localizationHelper
     * @param LocalizationManager $localizationManager
     * @param TokenStorageInterface $tokenStorage
     * @param AmountNormalizer $amountNormalizer
     * @param PaymentTransactionProvider $paymentTransactionProvider
     */
    public function __construct(
        LocalizationHelper $localizationHelper,
        LocalizationManager $localizationManager,
        TokenStorageInterface $tokenStorage,
        AmountNormalizer $amountNormalizer,
        PaymentTransactionProvider $paymentTransactionProvider
    ) {
        $this->localizationHelper = $localizationHelper;
        $this->localizationManager = $localizationManager;
        $this->tokenStorage = $tokenStorage;
        $this->amountNormalizer = $amountNormalizer;
        $this->paymentTransactionProvider = $paymentTransactionProvider;
    }

    /**
     * @param IngenicoConfig $config
     * @return IngenicoView
     */
    public function create(IngenicoConfig $config): IngenicoView
    {
        return new IngenicoView(
            $config,
            $this->getLocalizationCode(),
            $this->tokenStorage,
            $this->amountNormalizer,
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
