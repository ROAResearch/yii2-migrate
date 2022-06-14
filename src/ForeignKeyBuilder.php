<?php

namespace roaresearch\yii2\migrate;

trait ForeignKeyBuilder
{
    /**
     * @var ReferenceOption default action delete used when creating foreign keys.
     */
    public ReferenceOption $defaultOnDelete = ReferenceOption::Cascade;

    /**
     * @var ReferenceOption default action update used when creating foreign keys.
     */
    public ReferenceOption $defaultOnUpdate = ReferenceOption::Cascade;

    /**
     * @var string the name of the default referenced column when its not specified
     */
    public string $defaultReferenceColumn = 'id';

    /**
     * Creates foreign keys for the table.
     *
     * @param array column_name => reference pairs where reference is an array
     * containing a 'table' index and optionally a 'column' index.
     */
    protected function createForeignKeys(array $keys): void
    {
        foreach ($keys as $columnName => $reference) {
            $this->buildForeignKey($columnName, $reference)->up();
        }
    }

    /**
     * Drops foreign keys for the table.
     *
     * @param array column_name => reference pairs where reference is an array
     * containing a 'table' index and optionally a 'column' index.
     */
    protected function dropForeignKeys(array $keys)
    {
        foreach ($keys as $columnName => $reference) {
            $this->buildForeignKey($columnName, $reference)->down();
        }
    }

    protected function buildForeignKey(
        string $columnName,
        string|array $reference
    ): ForeignKey {
        return new ForeignKey($this, $columnName, $reference);
    }}
