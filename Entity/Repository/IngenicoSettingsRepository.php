<?php

namespace Ingenico\Connect\OroCommerce\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Ingenico\Connect\OroCommerce\Entity\IngenicoSettings;

/**
 * Repository for IngenicoSetting entity.
 */
class IngenicoSettingsRepository extends EntityRepository
{
    /**
     * @param string $type
     *
     * @return IngenicoSettings[]
     */
    public function getEnabledSettingsByType($type)
    {
        return $this->createQueryBuilder('settings')
            ->innerJoin('settings.channel', 'channel')
            ->andWhere('channel.enabled = true')
            ->andWhere('channel.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }
}
