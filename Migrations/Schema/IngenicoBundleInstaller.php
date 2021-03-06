<?php

namespace Ingenico\Connect\OroCommerce\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class IngenicoBundleInstaller implements Installation
{
    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_4';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateOroIntegrationTransportTable($schema);
        $this->createIngenicoLabelTable($schema);
        $this->createIngenicoShortLabelTable($schema);
        $this->addIngenicoLabelForeignKeys($schema);
        $this->addIngenicoShortLabelForeignKeys($schema);
    }

    /**
     * Add Ingenico configuration fields to the oro_integration_transport table
     *
     * @param Schema $schema
     */
    protected function updateOroIntegrationTransportTable(Schema $schema)
    {
        $table = $schema->getTable('oro_integration_transport');

        $table->addColumn('ingenico_api_key_id', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn(
            'ingenico_api_secret',
            'crypted_string',
            [
                'notnull' => false,
                'length' => 255,
                'comment' => '(DC2Type:crypted_string)',
            ]
        );
        $table->addColumn('ingenico_api_endpoint', 'text', ['notnull' => false]);
        $table->addColumn('ingenico_merchant_id', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('ingenico_enabled_products', 'array', ['notnull' => false, 'comment' => '(DC2Type:array)']);
        $table->addColumn('ingenico_payment_action', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('ingenico_direct_debit_text', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('ingenico_soft_descriptor', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('ingenico_tokenization_enabled', 'boolean', ['notnull' => false, 'default' => '0',]);
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
