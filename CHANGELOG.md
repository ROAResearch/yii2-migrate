Changelog
=========

3.0.0 13 June, 2022
-------------------

- [Brk] Php8.1 dependency
- [Brk] DAO for foreign keys.

2.0.0
-----

- [Brk] PSR12 support
- [Brk] Namespace change.

1.0.2
-----

- [Enh] `tecnocen\migrate\CreateViewMigration` to create migrations
  using `yii\db\Query` instances.

1.0.1
-----

- [Enh] `CreateTableMigration::foreignKeys()` accepts string values and
  `sourceColumn` index for arrays which will contain columns to be used
  for multi-column foreign keys.
