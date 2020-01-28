<?php

namespace Ingenico\Connect\OroCommerce\Method\Config\Provider;

use Doctrine\Persistence\ManagerRegistry;
use Ingenico\Connect\OroCommerce\Entity\IngenicoSettings;
use Ingenico\Connect\OroCommerce\Entity\Repository\IngenicoSettingsRepository;
use Ingenico\Connect\OroCommerce\Method\Config\Factory\IngenicoConfigFactory;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;
use Psr\Log\LoggerInterface;

/**
 * Creates instances of configurations for Ingenico payment method
 */
class IngenicoConfigProvider
{
    private const CONFIG_TYPE = 'ingenico';

    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var IngenicoConfigFactory
     */
    protected $configFactory;

    /**
     * @var IngenicoConfig[]
     */
    protected $configs;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param ManagerRegistry $doctrine
     * @param LoggerInterface $logger
     * @param IngenicoConfigFactory $configFactory
     */
    public function __construct(
        ManagerRegistry $doctrine,
        LoggerInterface $logger,
        IngenicoConfigFactory $configFactory
    ) {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->configFactory = $configFactory;
    }

    /**
     * @return PaymentConfigInterface[]
     */
    public function getPaymentConfigs(): array
    {
        $configs = [];

        $settings = $this->getEnabledIntegrationSettings();

        foreach ($settings as $setting) {
            $config = $this->configFactory->createConfig($setting);
            $configs[$config->getPaymentMethodIdentifier()] = $config;
        }

        return $configs;
    }

    /**
     * @param $identifier
     * @return PaymentConfigInterface
     */
    public function getPaymentConfig($identifier): PaymentConfigInterface
    {
        $paymentConfigs = $this->getPaymentConfigs();

        if ([] === $paymentConfigs || false === array_key_exists($identifier, $paymentConfigs)) {
            return null;
        }

        return $paymentConfigs[$identifier];
    }

    /**
     * @param $identifier
     * @return bool
     */
    public function hasPaymentConfig($identifier): bool
    {
        return null !== $this->getPaymentConfig($identifier);
    }

    /**
     * @return IngenicoSettings[]
     */
    protected function getEnabledIntegrationSettings()
    {
        try {
            /** @var IngenicoSettingsRepository $repository */
            $repository = $this->doctrine
                ->getManagerForClass(IngenicoSettings::class)
                ->getRepository(IngenicoSettings::class);

            return $repository->getEnabledSettingsByType(self::CONFIG_TYPE);
        } catch (\UnexpectedValueException $e) {
            $this->logger->critical($e->getMessage());

            return [];
        }
    }
}
