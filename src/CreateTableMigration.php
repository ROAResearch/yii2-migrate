<?php

namespace roaresearch\yii2\migrate;

use yii\db\ColumnSchemaBuilder;

/**
 * Handles the creation for one table.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 * @author Fernando (PFernando) López <flopez@alquimiadigital.mx>
 */
abstract class CreateTableMigration extends \yii\db\Migration
{
    use ForeignKeyBuilder;

    public const DEFAULT_KEY_LENGTH = 11;

    /**
     * Table name used to generate the migration.
     *
     * @return string table name without prefix.
     */
    abstract public function getTableName(): string;

    /**
     * Defines the columns which will be used on the table definition.
     *
     * @return array column_name => column_definition pairs.
     */
    abstract public function columns(): array;

    /**
     * Table name with prefix.
     * @return string table name with the prefix.
     */
    public function getPrefixedTableName(): string
    {
        return '{{%' . $this->getTableName() . '}}';
    }

    /**
     * @inheritdoc
     */
    public function primaryKey($length = self::DEFAULT_KEY_LENGTH)
    {
        return $this->normalKey($length)->append('AUTO_INCREMENT PRIMARY KEY');
    }

    /**
     * Returns a key column definition. Mostly used in foreign key columns.
     *
     * @param integer $length
     * @return ColumnSchemaBuilder
     */
    public function normalKey(
        $length = self::DEFAULT_KEY_LENGTH
    ): ColumnSchemaBuilder {
        return $this->integer($length)->unsigned()->notNull();        
    }

    /**
     * Returns an activable column definition.
     *
     * @param boolean $default
     * @return ColumnSchemaBuilder
     */
    public function activable($default = true): ColumnSchemaBuilder
    {
        return $this->boolean()->notNull()->defaultValue($default);
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->createTable(
            $this->prefixedTableName,
            [...$this->columns(), ...$this->defaultColumns()],
            $this->db->driverName === 'mysql'
                ? $this->mysqlOptions
                : $this->defaultOptions
        );

        $columns = $this->compositePrimaryKeys();
        if (!empty($columns)) {
            $this->addPrimaryKey(
                "{{%pk-{$this->tableName}}}",
                $this->prefixedTableName,
                $columns
            );
        }

        foreach ($this->compositeUniqueKeys() as $index => $columns) {
            $this->createIndex(
                "{{uq-{$this->tableName}-$index}}",
                $this->prefixedTableName,
                $columns,
                true // unique
            );
        }

        $this->createForeignKeys([
            ...$this->foreignKeys(),
            ...$this->defaultForeignKeys(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKeys([
            ...$this->foreignKeys(),
            ...$this->defaultForeignKeys(),
        ]);
        $this->dropTable($this->prefixedTableName);
    }

    /**
     * Default columns for a type of table.
     *
     * @return array column_name => column_definition pairs.
     */
    public function defaultColumns(): array
    {
        return [];
    }

    /**
     * The default foreign keys for a type of table.
     *
     * @return array column_name => reference pairs where reference is an array
     * containing a 'table' index and optionally a 'column' index.
     * @see foreignKeys()
     */
    public function defaultForeignKeys(): array
    {
        return [];
    }

    /**
     * The specific foreign keys for a type of table.
     *
     * The expected return is an array with each index representing a  foreign
     * key definition. There is a short syntax and a long syntax.
     *
     * On the short syntax the index key represents the both the column name to
     * be referenced and the foreign key name, while the index value represents
     * the referred table..
     *
     * On the long syntax the index key represents the foreign key name only and
     * the index value is an array itself which contains all the information to
     * define the foreign key. The definition can include the keys.
     * - table: required specifies which table will be referred.
     * - columns: optional specifies which columns will be linked. If not defined
     *   it will use the name of the index key referring to a column `id`.
     * - onDelete: optional defines which action to take when a record is deleted.
     *   if not defined it will use `$defaulOnDelete`.
     * - onUpdate: optional defines which action to take when a record is updated.
     *   if not defined it will use `$defaulOnUpdate`.
     *
     * Examples
     *
     * ```php
     * return [
     *     'stored_id' => ´department_store´, // short syntax
     *     'manager_id' => [
     *         'table' => 'user',
     *         'onDelete' => 'SET NULL',
     *     ],
     *     'region' => [
     *         'table' => 'country_division',
     *         'columns' => [
     *             'country_id' => 'country_id', // multiple columns
     *             'region_id' => 'region_id',
     *         ],
     *     ],
     * ];
     * ```
     *
     * @return array column_name => reference pairs. where reference is an array
     * containing a 'table' index and optionally a 'column' index.
     * @see $defaultOnDelete
     * @see $defaultOnUpdate
     */
    public function foreignKeys(): array
    {
        return [];
    }

    /**
     * Column names used to define a composite primary key.
     * Usage:
     *
     * ```php
     * public function compositePrimaryKeys()
     * {
     *     return ['sale_id', 'article_id'];
     * }
     * ```
     *
     * @return string[] column names that define the primary key. if the result
     * is empty it will be ignored.
     */
    public function compositePrimaryKeys(): array
    {
        return [];
    }

    /**
     * @return array index_name => index_definition pairs where index_name is
     * an string to be used to differentiate each index and index definition is
     * an array containing all the columns names for the unique index.
     */
    public function compositeUniqueKeys(): array
    {
        return [];
    }
}
