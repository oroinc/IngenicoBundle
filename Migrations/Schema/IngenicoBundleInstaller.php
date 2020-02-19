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
        return 'v1_2';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateOroIntegrationTransportTable($schema);
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
        $table->addColumn('ingenico_tokenization_enabled', 'boolean', ['notnull' => false, 'default' => '0',]);
    }
}
