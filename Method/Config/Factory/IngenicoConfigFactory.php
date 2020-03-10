<?php

namespace Ingenico\Connect\OroCommerce\Method\Config\Factory;

use Doctrine\Common\Collections\Collection;
use Ingenico\Connect\OroCommerce\Entity\IngenicoSettings;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Oro\Bundle\IntegrationBundle\Generator\IntegrationIdentifierGeneratorInterface;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;

/**
 * Creates instances of configurations for Ingenico payment method
 */
class IngenicoConfigFactory
{
    /**
     * @var IntegrationIdentifierGeneratorInterface
     */
    private $identifierGenerator;

    /**
     * @var LocalizationHelper
     */
    private $localizationHelper;

    /**
     * @param IntegrationIdentifierGeneratorInterface $identifierGenerator
     * @param LocalizationHelper $localizationHelper
     */
    public function __construct(
        IntegrationIdentifierGeneratorInterface $identifierGenerator,
        LocalizationHelper $localizationHelper
    ) {
        $this->identifierGenerator = $identifierGenerator;
        $this->localizationHelper = $localizationHelper;
    }

    /**
     * @param IngenicoSettings $settings
     * @return IngenicoConfig
     */
    public function createConfig(IngenicoSettings $settings): IngenicoConfig
    {
        $params = [];
        $channel = $settings->getChannel();

        $params[IngenicoConfig::FIELD_LABEL] = $this->getLocalizedValue($settings->getLabels());
        $params[IngenicoConfig::FIELD_SHORT_LABEL] = $this->getLocalizedValue($settings->getShortLabels());
        $params[IngenicoConfig::FIELD_ADMIN_LABEL] = $channel->getName();
        $params[IngenicoConfig::FIELD_API_ENDPOINT_KEY] = (string)$settings->getApiEndpoint();
        $params[IngenicoConfig::FIELD_API_KEY_ID_KEY] = (string)$settings->getApiKeyId();
        $params[IngenicoConfig::FIELD_API_SECRET_KEY] = (string)$settings->getApiSecret();
        $params[IngenicoConfig::FIELD_MERCHANT_ID_KEY] = (string)$settings->getMerchantId();
        $params[IngenicoConfig::FIELD_ENABLED_PRODUCTS_KEY] = is_array($settings->getEnabledProducts()) ?
            $settings->getEnabledProducts() : [];
        $params[IngenicoConfig::FIELD_PAYMENT_ACTION_KEY] = (string)$settings->getPaymentAction();
        $params[IngenicoConfig::FIELD_TOKENIZAION_ENABLED_KEY] = $settings->isTokenizationEnabled();
        $params[IngenicoConfig::FIELD_PAYMENT_METHOD_IDENTIFIER] =
            $this->identifierGenerator->generateIdentifier($channel);
        $params[IngenicoConfig::FIELD_DIRECT_DEBIT_TEXT_KEY] = (string)$settings->getDirectDebitText();
        $params[IngenicoConfig::FIELD_SOFT_DESCRIPTOR_KEY] = (string)$settings->getSoftDescriptor();

        return new IngenicoConfig($params);
    }

    /**
     * @param Collection $values
     * @return string
     */
    protected function getLocalizedValue(Collection $values)
    {
        return (string)$this->localizationHelper->getLocalizedValue($values);
    }
}
