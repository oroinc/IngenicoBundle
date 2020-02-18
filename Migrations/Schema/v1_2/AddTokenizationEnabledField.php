<?php

namespace Ingenico\Connect\OroCommerce\Migrations\Schema\v1_2;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddTokenizationEnabledField implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('oro_integration_transport');

        $table->addColumn(
            'ingenico_tokenization_enabled',
            'boolean',
            [
                'notnull' => false,
                'default' => '0',
            ]
        );
    }
}
