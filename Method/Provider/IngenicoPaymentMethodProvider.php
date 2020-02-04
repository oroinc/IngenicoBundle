<?php

namespace Ingenico\Connect\OroCommerce\Method\Provider;

use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Method\Config\Provider\IngenicoConfigProvider;
use Ingenico\Connect\OroCommerce\Method\Factory\IngenicoPaymentMethodFactory;
use Oro\Bundle\PaymentBundle\Method\Provider\AbstractPaymentMethodProvider;

/**
 * Configuration provider class for Ingenico payment method
 */
class IngenicoPaymentMethodProvider extends AbstractPaymentMethodProvider
{
    /**
     * @var IngenicoConfigProvider
     */
    private $configProvider;

    /**
     * @var IngenicoPaymentMethodFactory
     */
    protected $factory;

    /**
     * @param IngenicoConfigProvider $configProvider
     * @param IngenicoPaymentMethodFactory $factory
     */
    public function __construct(
        IngenicoConfigProvider $configProvider,
        IngenicoPaymentMethodFactory $factory
    ) {
        parent::__construct();

        $this->configProvider = $configProvider;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    protected function collectMethods(): void
    {
        $configs = $this->configProvider->getPaymentConfigs();
        foreach ($configs as $config) {
            $this->addIngenicoMethod($config);
        }
    }

    /**
     * @param IngenicoConfig $config
     */
    private function addIngenicoMethod(IngenicoConfig $config): void
    {
        $this->addMethod(
            $config->getPaymentMethodIdentifier(),
            $this->factory->create($config)
        );
    }
}
