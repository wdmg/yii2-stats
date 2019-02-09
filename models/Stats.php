<?php

namespace wdmg\stats\models;

use Yii;
class Stats extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{stats}}';
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
            [['https', 'unique'], 'integer', 'max' => 1, 'min' => 0],
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
            'id' => Module::t('stats', 'ID'),
            'request_uri' => Module::t('stats', 'Request URL'),
            'remote_addr' => Module::t('stats', 'Remote IP'),
            'remote_host' => Module::t('stats', 'Remote Host'),
            'user_id' => Module::t('stats', 'User ID'),
            'user_agent' => Module::t('stats', 'User Agent'),
            'referer_uri' => Module::t('stats', 'Referrer URL'),
            'referer_host' => Module::t('stats', 'Referrer Host'),
            'https' => Module::t('stats', 'HTTPS'),
            'datetime' => Module::t('stats', 'DateTime'),
            'session' => Module::t('stats', 'Session'),
            'unique' => Module::t('stats', 'Unique'),
            'params' => Module::t('stats', 'Params'),
        ];
    }

}