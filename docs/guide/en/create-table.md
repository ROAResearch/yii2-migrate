Create Table
============

The class `roaresearch/yii2/migrate/CreateTableMigration` can be extended to
create a single single table.

Method `getTableName(): string`
-------------------------------

This method defines the name of the table created without prefix. The prefix will
be appended on creation.

Methods `columns(): array` and `defaultColumns(): array`
-------------------------------------------------

Both methods return an array of column definitions.

The method `columns()` is executed first to define the columns specific to the
table being created.

`defaultColumns()` is executed afterwards and the columns defined here are
appended at the end of the table. Its use case is to avoid redefining columns
which are common in several tables, for example as used in
[ROAResearch/yii2-rmdb](https://github.com/ROAResearch/yii2-rmdb).

See https://www.yiiframework.com/doc/api/2.0/yii-db-columnschema

Methods `foreignKeys(): array` and `defaultForeignKeys(): array`
----------------------------------------------------------------

Both methods return an array of foreign key definitions.

The method `foreignKeys()` defines foreign keys for the columns defined in
`columns()` while `defaultForeignKeys()` defines foreign keys for the columns
defined in `defaultForeignKeys()`.

The foreign keys can be defined using a short syntax or a long syntax.

On the short syntax the index key represents the both the column name to
be referenced and the foreign key name, while the index value represents
the referred table..

On the long syntax the index key represents the foreign key name only and
the index value is an array itself which contains all the information to
define the foreign key. The definition can include the keys.

- table: required specifies which table will be referred.
- columns: optional specifies which columns will be linked. If not defined
  it will use the name of the index key referring to a column `id`.
- onDelete: optional defines which action to take when a record is deleted.
  if not defined it will use `$defaulOnDelete`.
- onUpdate: optional defines which action to take when a record is updated.
  if not defined it will use `$defaulOnUpdate`.

Examples

```php
return [
    'stored_id' => ´department_store´, // short syntax
    'manager_id' => [
        'table' => 'user',
        'onDelete' => 'SET NULL',
    ],
    'region' => [
        'table' => 'country_division',
        'columns' => [
            'country_id' => 'country_id', // multiple columns
            'region_id' => 'region_id',
        ],
    ],
];
```

Method `compositePrimaryKeys(): array`
--------------------------------------

Method to define primary keys using multiple columns or a single column
without rellying on `primaryKey()`.

```php
public function compositePrimaryKeys()
{
    return ['sale_id', 'article_id'];
}
```

Method `compositeUniqueKeys(): array`
-------------------------------------

Returns an array where each key is the name of a unique key and the
value is an array of column names to be simultaneously compared.

For example lets say you have a `product` table, each product belong
in a category and has a `name` and `description`. But it must not be
possible to create 2 products with the same `name` or the same
`description` on a category.

```php
public function compositeUniqueKeys()
{
    return [
        'category_name' => ['category_id', 'name'],
        'category_description' => ['category_id', 'description'],
    ];
}
