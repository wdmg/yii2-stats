<?php

use yii\db\Migration;

/**
 * Class m210107_223517_stats_visitors
 */
class m210107_223517_stats_visitors extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (is_null($this->getDb()->getSchema()->getTableSchema('{{%stats_visitors}}')->getColumn('iso_code')))
            $this->addColumn('{{%stats_visitors}}', 'iso_code', $this->string(3)->null()->after('unique'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->getDb()->getSchema()->getTableSchema('{{%stats_visitors}}')->getColumn('iso_code'))
            $this->dropColumn('{{%stats_visitors}}', 'iso_code');
    }
}