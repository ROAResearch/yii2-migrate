<?php

namespace roaresearch\yii2\migrate;

use yii\base\InvalidConfigException;

/**
 * DAO which can be created to access all the properties needed to defined an SQL Foreign Key.
 * @author Angel (Faryshta) Guevara <aguevara@invernaderolabs.com>
 */
class ForeignKey
{
    /**
     * @var string name of the referenced table
     */
    public readonly string $refTable;

    /**
     * @var array pairs of source_column => reference_column
     */
    public readonly array $columns;

    /**
     * @var ReferenceOption
     */
    public readonly ReferenceOption $onDelete;

    /**
     * @var ReferenceOption
     */
    public readonly ReferenceOption $onUpdate;

    /**
     * @property CreateTableMigration $migration
     * @property string $name the name which will be used to create the fk and idx constraints
     * @property string|array $reference data used to create the data of the foreign key
     */
    public function __construct(
        protected readonly CreateTableMigration $migration,
        protected readonly string $name,
        string|array $reference
    ) {
        if (is_string($reference)) {
            $this->refTable = $reference;
            $this->columns = [$name => $migration->defaultReferenceColumn];
            $this->onDelete = $migration->defaultOnDelete;
            $this->onUpdate = $migration->defaultOnUpdate;
            return;
        }

        $this->refTable = $reference['table']
            ?? throw new InvalidConfigException(
                "The reference table must be defined for constraint $name."
            );

        $this->columns = $reference['columns']
            ?? [$name => $migration->defaultReferenceColumn];

        $this->onDelete = $reference['onDelete'] ?? $migration->defaultOnDelete;
        $this->onUpdate = $reference['onUpdate'] ?? $migration->defaultOnUpdate;
    }

    /**
     * Executed when the migration goes up creating the SQL constraints.
     */
    public function up(): void
    {
        // creates index for column
        $this->migration->createIndex(
            $this->getIdxName(),
            $this->migration->getPrefixedTableName(),
            $this->getSourceColumns()
        );

        // creates the foreign key
        $this->migration->addForeignKey(
            $this->getFKName(),
            $this->migration->getPrefixedTableName(),
            $this->getSourceColumns(),
            $this->migration->prefixName($this->refTable),
            $this->getReferenceColumns(),
            $this->onDelete->value,
            $this->onUpdate->value
        );
    }

    /**
     * Executed when the migration goes down deleting the SQL constraints.
     */
    public function down(): void
    {
        // drops the foreign key
        $this->migration->dropForeignKey(
            $this->getFKName(),
            $this->getPrefixedTableName()
        );

        // drops index for column
        $this->migraiton->dropIndex(
            $this->getIdxName(),
            $this->migration->getPrefixedTableName()
        );
    }

    /**
     * @return string[] list of the constrained columns on the source table
     */
    public function getSourceColumns(): array
    {
        return array_keys($this->columns);
    }

    /**
     * @return string[] list of the constrained columns on the reference table
     */
    public function getReferenceColumns(): array
    {
        return array_values($this->columns);
    }

    /**
     * @return string the SQL name of the FK constraint
     */
    public function getFKName(): string
    {
        return $this->migration->prefixName(
            "fk-{$this->migration->getTableName()}-{$this->name}"
        );
    }

    /**
     * @return string the SQL name of the idx constraint
     */
    public function getIdxName(): string
    {
        return $this->migration->prefixName(
            "idx-{$this->migration->getTableName()}-{$this->name}"
        );
    }
}
