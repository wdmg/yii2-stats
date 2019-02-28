<?php

namespace wdmg\stats\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Visitors extends ActiveRecord
{

    /**
     * Visitor OS platform
     */
    public $client_os;

    /**
     * Visitor browser
     */
    public $client_browser;

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
        return '{{%stats_visitors}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['datetime'],
                ]
            ],
        ];
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
            'id' => Yii::t('app/modules/stats', 'ID'),
            'request_uri' => Yii::t('app/modules/stats', 'Request URL'),
            'remote_addr' => Yii::t('app/modules/stats', 'Remote IP'),
            'remote_host' => Yii::t('app/modules/stats', 'Remote Host'),
            'user_id' => Yii::t('app/modules/stats', 'User ID'),
            'user_agent' => Yii::t('app/modules/stats', 'User Agent'),
            'referer_uri' => Yii::t('app/modules/stats', 'Referrer URL'),
            'referer_host' => Yii::t('app/modules/stats', 'Referrer Host'),
            'https' => Yii::t('app/modules/stats', 'HTTPS'),
            'datetime' => Yii::t('app/modules/stats', 'DateTime'),
            'session' => Yii::t('app/modules/stats', 'Session'),
            'unique' => Yii::t('app/modules/stats', 'Unique'),
            'params' => Yii::t('app/modules/stats', 'Params'),
        ];
    }


    public static function getVisitorTypeList() {
        return [
            self::TYPE_UNDEFINED => Yii::t('app/modules/stats', 'Unknown'),
            self::TYPE_DERECT_ENTRY => Yii::t('app/modules/stats', 'Derect entry'),
            self::TYPE_INNER_VISIT => Yii::t('app/modules/stats', 'Inner visit'),
            self::TYPE_FROM_SEARCH => Yii::t('app/modules/stats', 'From search'),
            self::TYPE_FROM_ADVERTS => Yii::t('app/modules/stats', 'From Ads'),
            self::TYPE_FROM_SOCIALS => Yii::t('app/modules/stats', 'From socials'),
        ];
    }

    public function getClientOS($user_agent, $platforms)
    {
        $platform = [
            'title' => Yii::t('app/modules/stats', 'Unknown'),
            'icon' => 'icon-unknown'
        ];

        foreach ($platforms as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $platform = $value;
            }
        }
        return $platform;
    }

    public function getClientBrowser($user_agent, $browsers)
    {
        $browser = [
            'title' => Yii::t('app/modules/stats', 'Unknown'),
            'icon' => 'icon-unknown'
        ];

        foreach ($browsers as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $browser = $value;
            }
        }
        return $browser;
    }

}