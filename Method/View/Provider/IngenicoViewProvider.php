<?php

namespace Ingenico\Connect\OroCommerce\Method\View\Provider;

use Ingenico\Connect\OroCommerce\Method\Config\Provider\IngenicoConfigProvider;
use Ingenico\Connect\OroCommerce\Method\View\Factory\IngenicoViewFactory;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;
use Oro\Bundle\PaymentBundle\Method\View\AbstractPaymentMethodViewProvider;

/**
 * Provider for retrieving Ingenico payment method's view instances
 */
class IngenicoViewProvider extends AbstractPaymentMethodViewProvider
{
    /**
     * @var IngenicoConfigProvider
     */
    private $configProvider;

    /**
     * @var IngenicoConfigProvider
     */
    protected $factory;

    /**
     * @param IngenicoConfigProvider $configProvider
     * @param IngenicoViewFactory $factory
     */
    public function __construct(
        IngenicoConfigProvider $configProvider,
        IngenicoViewFactory $factory
    ) {
        parent::__construct();

        $this->configProvider = $configProvider;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildViews(): void
    {
        $configs = $this->configProvider->getPaymentConfigs();
        foreach ($configs as $config) {
            $this->addIngenicoView($config);
        }
    }

    /**
     * @param PaymentConfigInterface $config
     */
    protected function addIngenicoView(PaymentConfigInterface $config): void
    {
        $this->addView(
            $config->getPaymentMethodIdentifier(),
            $this->factory->create($config)
        );
    }
}
