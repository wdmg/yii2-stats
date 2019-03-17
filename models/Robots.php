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
            [['id'], 'integer'],
            [['name', 'regexp'], 'string'],
            [['is_badbot'], 'integer', 'min' => 0, 'max' => 1],
        ];

        return $rules;
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