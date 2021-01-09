<?php

namespace wdmg\stats\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Robots extends ActiveRecord
{

    /**
     * Visitor types
     */
    const TYPE_UNDEFINED = 0;
    const TYPE_SPAM_BOT = 1;
    const TYPE_BOT_NETWORK = 2;
    const TYPE_CRAWLER = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%stats_robots}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['name', 'regexp'], 'required'],
            [['name', 'regexp', 'hosts'], 'string'],
            [['is_badbot'], 'boolean'],
            [['type'], 'integer'],
        ];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->hosts = serialize(explode('\r\n', $this->hosts));
            return true;
        }
        return false;
    }

    public function afterFind() {
        parent::afterFind();

        $hosts = unserialize($this->hosts);
        if(is_array($hosts))
            $this->hosts = implode('\r\n', $hosts);
        else
            $this->hosts = '';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/stats', 'ID'),
            'name' => Yii::t('app/modules/stats', 'Name'),
            'regexp' => Yii::t('app/modules/stats', 'RegExp'),
            'hosts' => Yii::t('app/modules/stats', 'Hosts'),
            'type' => Yii::t('app/modules/stats', 'Type'),
            'is_badbot' => Yii::t('app/modules/stats', 'Is bad bot?'),
        ];
    }

    public static function getRobotsTypeList() {
        return [
            self::TYPE_UNDEFINED => Yii::t('app/modules/stats', 'Unknown'),
            self::TYPE_SPAM_BOT => Yii::t('app/modules/stats', 'SPAM Bot'),
            self::TYPE_BOT_NETWORK => Yii::t('app/modules/stats', 'Bot Network'),
            self::TYPE_CRAWLER => Yii::t('app/modules/stats', 'Crawler'),
        ];
    }
}