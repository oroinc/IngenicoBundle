<?php

namespace Ingenico\Connect\OroCommerce\Tests\Behat\Mock\Method\View\Factory;

use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Method\View\Factory\IngenicoViewFactory;
use Ingenico\Connect\OroCommerce\Method\View\IngenicoView;
use Ingenico\Connect\OroCommerce\Normalizer\AmountNormalizer;
use Ingenico\Connect\OroCommerce\Provider\PaymentTransactionProvider;
use Ingenico\Connect\OroCommerce\Tests\Behat\Mock\Method\View\IngenicoViewMock;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;
use Oro\Bundle\LocaleBundle\Manager\LocalizationManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class IngenicoViewFactoryMock extends IngenicoViewFactory
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AmountNormalizer
     */
    private $amountNormalizer;

    /**
     * @var PaymentTransactionProvider
     */
    private $paymentTransactionProvider;

    /**
     * @inheritDoc
     */
    public function __construct(
        LocalizationHelper $localizationHelper,
        LocalizationManager $localizationManager,
        TokenStorageInterface $tokenStorage,
        AmountNormalizer $amountNormalizer,
        PaymentTransactionProvider $paymentTransactionProvider
    ) {
        parent::__construct(
            $localizationHelper,
            $localizationManager,
            $tokenStorage,
            $amountNormalizer,
            $paymentTransactionProvider
        );

        $this->tokenStorage = $tokenStorage;
        $this->amountNormalizer = $amountNormalizer;
        $this->paymentTransactionProvider = $paymentTransactionProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function create(IngenicoConfig $config): IngenicoView
    {
        return new IngenicoViewMock(
            $config,
            $this->getLocalizationCode(),
            $this->tokenStorage,
            $this->amountNormalizer,
            $this->paymentTransactionProvider
        );
    }
}
