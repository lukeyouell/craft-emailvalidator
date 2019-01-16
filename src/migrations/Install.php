<?php

namespace lukeyouell\emailvalidator\migrations;

use lukeyouell\emailvalidator\EmailValidator;
use lukeyouell\emailvalidator\db\Table;
use lukeyouell\emailvalidator\records\Provider as ProviderRecord;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;
use craft\helpers\MigrationHelper;

class Install extends Migration
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
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            // Insert email providers (job?)
            $this->insertDefaultData();
        }

        return true;
    }

    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->dropForeignKeys();
        $this->dropTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema(TABLE::PROVIDERS);

        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                TABLE::PROVIDERS,
                [
                    'id'          => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid'         => $this->uid(),
                    // Custom columns
                    'type'        => $this->string(),
                    'provider'    => $this->string(),
                    'enabled'     => $this->boolean()->defaultValue(true),
                ]
            );
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(TABLE::RULESETS);

        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                TABLE::RULESETS,
                [
                    'id'          => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid'         => $this->uid(),
                    // Custom columns
                    'name'        => $this->string()->notNull(),
                    'handle'      => $this->string()->notNull(),
                    'trigger'     => $this->string()->notNull(),
                    'params'      => $this->string()->notNull(),
                    'rules'       => $this->text(),
                    'logData'     => $this->boolean()->defaultValue(true),
                    'logEnabled'  => $this->boolean()->defaultValue(true),
                    'enabled'     => $this->boolean()->defaultValue(true),
                ]
            );
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(TABLE::LOGS);

        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                TABLE::LOGS,
                [
                    'id'          => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid'         => $this->uid(),
                    // Custom columns
                    'rulesetId'   => $this->integer()->notNull(),
                    'description' => $this->text(),
                    'error'       => $this->boolean()->defaultValue(false),
                    'data'        => $this->text(),
                ]
            );
        }

        return $tablesCreated;
    }

    protected function addForeignKeys()
    {
        $this->addForeignKey(null, TABLE::LOGS, ['rulesetId'], TABLE::RULESETS, ['id'], 'CASCADE');
    }

    protected function insertDefaultData()
    {
        // Update free providers
        EmailValidator::$plugin->providers->updateProviders(ProviderRecord::TYPE_FREE);
        // Update disposable providers
        EmailValidator::$plugin->providers->updateProviders(ProviderRecord::TYPE_DISPOSABLE);
    }

    protected function dropForeignKeys()
    {
        MigrationHelper::dropAllForeignKeysOnTable(TABLE::LOGS, $this);
    }

    protected function dropTables()
    {
        $this->dropTableIfExists(TABLE::PROVIDERS);
        $this->dropTableIfExists(TABLE::RULESETS);
        $this->dropTableIfExists(TABLE::LOGS);
    }
}
