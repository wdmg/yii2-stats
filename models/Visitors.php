<?php

namespace wdmg\stats\models;

use Yii;
class Visitors extends \yii\db\ActiveRecord
{
    /**
     * Visitor types
     */
    const TYPE_UNDEFINED = 0;
    const TYPE_DERECT_ENTRY = 1;
    const TYPE_INNER_VISIT = 2;
    const TYPE_FROM_SEARCH = 3;
    const TYPE_FROM_ADVERTS = 4;
    const TYPE_FROM_SOCIALS = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{stats_visitors}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['request_uri', 'remote_addr', 'session'], 'required'],
            [['remote_addr'], 'string', 'max' => 45],
            [['request_uri', 'remote_host', 'user_agent', 'referer_uri', 'referer_host'], 'string', 'max' => 255],
            [['session'], 'string', 'max' => 32],
            [['type'], 'integer', 'min' => 0, 'max' => 6],
            [['https', 'unique'], 'integer', 'min' => 0, 'max' => 1],
            [['user_id'], 'integer'],
            [['params'], 'string'],
            [['datetime'], 'safe'],
        ];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('stats', 'ID'),
            'request_uri' => Yii::t('stats', 'Request URL'),
            'remote_addr' => Yii::t('stats', 'Remote IP'),
            'remote_host' => Yii::t('stats', 'Remote Host'),
            'user_id' => Yii::t('stats', 'User ID'),
            'user_agent' => Yii::t('stats', 'User Agent'),
            'referer_uri' => Yii::t('stats', 'Referrer URL'),
            'referer_host' => Yii::t('stats', 'Referrer Host'),
            'https' => Yii::t('stats', 'HTTPS'),
            'datetime' => Yii::t('stats', 'DateTime'),
            'session' => Yii::t('stats', 'Session'),
            'unique' => Yii::t('stats', 'Unique'),
            'params' => Yii::t('stats', 'Params'),
        ];
    }


    public static function getVisitorTypeList() {
        return [
            self::TYPE_UNDEFINED => Yii::t('stat', 'Unknown'),
            self::TYPE_DERECT_ENTRY => Yii::t('stat', 'Derect entry'),
            self::TYPE_INNER_VISIT => Yii::t('stat', 'Inner visit'),
            self::TYPE_FROM_SEARCH => Yii::t('stat', 'From search'),
            self::TYPE_FROM_ADVERTS => Yii::t('stat', 'From Ads'),
            self::TYPE_FROM_SOCIALS => Yii::t('stat', 'From socials'),
        ];
    }

}