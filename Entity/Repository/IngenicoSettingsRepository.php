<?php

namespace Ingenico\Connect\OroCommerce\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Ingenico\Connect\OroCommerce\Entity\IngenicoSettings;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

/**
 * Repository for IngenicoSetting entity.
 */
class IngenicoSettingsRepository extends EntityRepository
{
    /** @var AclHelper */
    private $aclHelper;

    /**
     * @param AclHelper $aclHelper
     */
    public function setAclHelper(AclHelper $aclHelper)
    {
        $this->aclHelper = $aclHelper;
    }

    /**
     * @param string $type
     * @return IngenicoSettings[]
     */
    public function getEnabledSettingsByType(string $type)
    {
        $qb = $this->createQueryBuilder('settings')
            ->innerJoin('settings.channel', 'channel')
            ->andWhere('channel.enabled = true')
            ->andWhere('channel.type = :type')
            ->setParameter('type', $type);

        return $this->aclHelper->apply($qb)->getResult();
    }
}
