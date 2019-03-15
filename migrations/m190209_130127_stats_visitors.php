<?php

use yii\db\Migration;
use yii\base\InvalidConfigException;
use yii\rbac\DbManager;

/**
 * Class m190209_130127_stats_visitors
 */
class m190209_130127_stats_visitors extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%stats_visitors}}', [
            'id'=> $this->bigPrimaryKey(20),
            'request_uri' => $this->string(255)->notNull(),
            'remote_addr' => $this->string(45)->notNull(),
            'remote_host' => $this->string(255),
            'user_id' => $this->integer(11)->null()->defaultValue(null),
            'user_agent' => $this->string(255)->null()->defaultValue(null),
            'referer_uri' => $this->string(255)->null()->defaultValue(null),
            'referer_host' => $this->string(255),
            'robot_id' => $this->integer(11)->null()->defaultValue(0),
            'https' => $this->tinyInteger(1)->null()->defaultValue(0),
            'type' => $this->tinyInteger(1)->null()->defaultValue(0),
            'datetime' => $this->integer(),
            'session' => $this->string(32)->notNull(),
            'unique' => $this->boolean()->defaultValue(false),
            'params' => $this->text(),
        ], $tableOptions);

        $this->createIndex('request','{{%stats_visitors}}', ['request_uri'],false);
        $this->createIndex('remote','{{%stats_visitors}}', ['remote_addr', 'remote_host'],false);
        $this->createIndex('referer','{{%stats_visitors}}', ['referer_uri', 'referer_host'],false);
        $this->createIndex('session','{{%stats_visitors}}', ['session'],false);
        $this->createIndex('robot','{{%stats_visitors}}', ['robot_id'],false);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('request', '{{%stats_visitors}}');
        $this->dropIndex('remote', '{{%stats_visitors}}');
        $this->dropIndex('referer', '{{%stats_visitors}}');
        $this->dropIndex('session', '{{%stats_visitors}}');
        $this->dropIndex('robot', '{{%stats_visitors}}');
        $this->truncateTable('{{%stats_visitors}}');
        $this->dropTable('{{%stats_visitors}}');
    }
}