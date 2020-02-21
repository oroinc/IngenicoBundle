<?php

namespace Ingenico\Connect\OroCommerce\Migrations\Schema\v1_3;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class CreateLabelShortLabelTables implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->createIngenicoLabelTable($schema);
        $this->createIngenicoShortLabelTable($schema);
        $this->addIngenicoLabelForeignKeys($schema);
        $this->addIngenicoShortLabelForeignKeys($schema);
    }


    /**
     * Create ingenico_label table
     *
     * @param Schema $schema
     */
    protected function createIngenicoLabelTable(Schema $schema)
    {
        $table = $schema->createTable('ingenico_label');
        $table->addColumn('transport_id', 'integer', []);
        $table->addColumn('localized_value_id', 'integer', []);
        $table->setPrimaryKey(['transport_id', 'localized_value_id']);
        $table->addUniqueIndex(['localized_value_id'], 'UNIQ_5345E502EB576E89');
    }

    /**
     * Create ingenico_short_label table
     *
     * @param Schema $schema
     */
    protected function createIngenicoShortLabelTable(Schema $schema)
    {
        $table = $schema->createTable('ingenico_short_label');
        $table->addColumn('transport_id', 'integer', []);
        $table->addColumn('localized_value_id', 'integer', []);
        $table->setPrimaryKey(['transport_id', 'localized_value_id']);
        $table->addUniqueIndex(['localized_value_id'], 'UNIQ_F9487B3DEB576E89');
    }


    /**
     * Add ingenico_label foreign keys.
     *
     * @param Schema $schema
     */
    protected function addIngenicoLabelForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('ingenico_label');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_integration_transport'),
            ['transport_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * Add ingenico_short_label foreign keys.
     *
     *
     * @param Schema $schema
     */
    protected function addIngenicoShortLabelForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('ingenico_short_label');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_integration_transport'),
            ['transport_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }
}
