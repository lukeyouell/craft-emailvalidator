<?php

namespace lukeyouell\emailvalidator\migrations;

use lukeyouell\emailvalidator\EmailValidator;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;
use craft\helpers\MigrationHelper;

/**
 * m180802_103331_providerRecords migration.
 */
class m180802_103331_providerRecords extends Migration
{
    // Public Properties
    // =========================================================================

    public $driver;

    // Public Methods
    // =========================================================================

    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            // Populate ev_providers with providers
            EmailValidator::getInstance()->recordService->updateProviders('free');
            EmailValidator::getInstance()->recordService->updateProviders('disposable');
        }

        return true;
    }

    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->dropTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    protected function createTables()
    {
        $tablesCreated = false;

        // support_tickets table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%ev_providers}}');
        if ($tableSchema === null) {
            $tablesCreated = true;

            $this->createTable(
                '{{%ev_providers}}',
                [
                    'id'          => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid'         => $this->uid(),
                    // Custom columns in the table
                    'type'        => $this->string(),
                    'domain'      => $this->string(),
                ]
            );
        }

        return $tablesCreated;
    }

    protected function dropTables()
    {
        $this->dropTable('{{%ev_providers}}');
    }
}
