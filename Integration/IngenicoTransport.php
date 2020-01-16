<?php

namespace Ingenico\Connect\OroCommerce\Integration;

use Ingenico\Connect\OroCommerce\Entity\IngenicoSettings;
use Ingenico\Connect\OroCommerce\Form\Type\IngenicoSettingsType;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Oro\Bundle\IntegrationBundle\Provider\TransportInterface;

/**
 * Transport implementation for Ingenico payment integration.
 */
class IngenicoTransport implements TransportInterface
{
    /**
     * {@inheritdoc}
     */
    public function init(Transport $transportEntity)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'ingenico.settings.label';
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsFormType()
    {
        return IngenicoSettingsType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsEntityFQCN()
    {
        return IngenicoSettings::class;
    }
}
