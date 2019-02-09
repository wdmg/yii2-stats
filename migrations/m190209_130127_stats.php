<?php

use yii\db\Migration;
use yii\base\InvalidConfigException;
use yii\rbac\DbManager;

/**
 * Class m190209_130127_stats
 */
class m190209_130127_stats extends Migration
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

        $this->createTable('{{%stats%}}', [
            'id'=> $this->bigPrimaryKey(20),
            'request_uri' => $this->string(255)->notNull(),
            'remote_addr' => $this->string(45)->notNull(),
            'remote_host' => $this->string(255),
            'user_id' => $this->integer(11)->null()->defaultValue(null),
            'user_agent' => $this->string(255)->null()->defaultValue(null),
            'referer_uri' => $this->string(255)->null()->defaultValue(null),
            'referer_host' => $this->string(255),
            'https' => $this->tinyInteger(1)->null()->defaultValue(0),
            'datetime' => $this->timestamp()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
            'session' => $this->string(32)->notNull(),
            'unique' => $this->boolean()->defaultValue(false),
            'params' => $this->text(),
        ], $tableOptions);

        $this->createIndex('request','{{%stats}}', ['request_uri'],false);
        $this->createIndex('remote','{{%stats}}', ['remote_addr', 'remote_host'],false);
        $this->createIndex('referer','{{%stats}}', ['referer_uri', 'referer_host'],false);
        $this->createIndex('session','{{%stats}}', ['session'],false);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('request', '{{%stats}}');
        $this->dropIndex('remote', '{{%stats}}');
        $this->dropIndex('referer', '{{%stats}}');
        $this->dropIndex('session', '{{%stats}}');
        $this->truncateTable('{{%stats%}}');
        $this->dropTable('{{%stats%}}');
    }
}