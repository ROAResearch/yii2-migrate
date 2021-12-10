<?php

namespace roaresearch\yii2\migrate;

use yii\db\Query;

/**
 * Handles the creation for one view query.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 * @since 1.0.2
 */
abstract class CreateViewMigration extends \yii\db\Migration
{
    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute(
            'CREATE VIEW '
            . $this->quotedViewName()
            . ' AS '
            . $this->viewQuery()->createCommand($this->getDb())->getRawSql()
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DROP VIEW ' . $this->quotedViewName());
    }

    /**
     * @return string view name quoted and with prefixes.
     */
    private function quotedViewName(): string
    {
        return $this->getDb()->quoteSql('{{%' . $this->viewName() . '}}');
    }
    
    /**
     * @return string the name of the view to be created. It will be
     * automatically quoted.
     */
    abstract public function viewName(): string;
    
    /**
     * @return Query query to be used to obtain the SQL to create the view.
     */
    abstract public function viewQuery(): Query;
}
