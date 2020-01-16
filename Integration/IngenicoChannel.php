<?php

namespace Ingenico\Connect\OroCommerce\Integration;

use Oro\Bundle\IntegrationBundle\Provider\ChannelInterface;
use Oro\Bundle\IntegrationBundle\Provider\IconAwareIntegrationInterface;

/**
 * Channel implementation for Ingenico payment integration.
 */
class IngenicoChannel implements ChannelInterface, IconAwareIntegrationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'ingenico.channel_type.label';
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'bundles/ingenico/img/ingenico-logo.png';
    }
}
