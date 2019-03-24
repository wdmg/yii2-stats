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
     * Robot data
     */
    public $robot = null;

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
            [['user_id', 'robot_id'], 'integer'],
            [['params'], 'string'],
            [['datetime', 'code'], 'safe'],
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
            'user' => Yii::t('app/modules/stats', 'User'),
            'user_id' => Yii::t('app/modules/stats', 'User ID'),
            'robot' => Yii::t('app/modules/stats', 'Robot'),
            'robot_id' => Yii::t('app/modules/stats', 'Robot ID'),
            'user_agent' => Yii::t('app/modules/stats', 'User Agent'),
            'referer_uri' => Yii::t('app/modules/stats', 'Referrer URL'),
            'referer_host' => Yii::t('app/modules/stats', 'Referrer Host'),
            'https' => Yii::t('app/modules/stats', 'HTTPS'),
            'datetime' => Yii::t('app/modules/stats', 'DateTime'),
            'type' => Yii::t('app/modules/stats', 'Type'),
            'code' => Yii::t('app/modules/stats', 'Code'),
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

    public static function getStatusCodeList() {
        return [
            0 => Yii::t('app/modules/stats', 'Unknown status code'),
            100 => Yii::t('app/modules/stats', 'Continue'),
            101 => Yii::t('app/modules/stats', 'Switching Protocols'),
            102 => Yii::t('app/modules/stats', 'Processing'), // WebDAV; RFC 2518
            200 => Yii::t('app/modules/stats', 'OK'),
            201 => Yii::t('app/modules/stats', 'Created'),
            202 => Yii::t('app/modules/stats', 'Accepted'),
            203 => Yii::t('app/modules/stats', 'Non-Authoritative Information'), // since HTTP/1.1
            204 => Yii::t('app/modules/stats', 'No Content'),
            205 => Yii::t('app/modules/stats', 'Reset Content'),
            206 => Yii::t('app/modules/stats', 'Partial Content'),
            207 => Yii::t('app/modules/stats', 'Multi-Status'), // WebDAV; RFC 4918
            208 => Yii::t('app/modules/stats', 'Already Reported'), // WebDAV; RFC 5842
            226 => Yii::t('app/modules/stats', 'IM Used'), // RFC 3229
            300 => Yii::t('app/modules/stats', 'Multiple Choices'),
            301 => Yii::t('app/modules/stats', 'Moved Permanently'),
            302 => Yii::t('app/modules/stats', 'Found'),
            303 => Yii::t('app/modules/stats', 'See Other'), // since HTTP/1.1
            304 => Yii::t('app/modules/stats', 'Not Modified'),
            305 => Yii::t('app/modules/stats', 'Use Proxy'), // since HTTP/1.1
            306 => Yii::t('app/modules/stats', 'Switch Proxy'),
            307 => Yii::t('app/modules/stats', 'Temporary Redirect'), // since HTTP/1.1
            308 => Yii::t('app/modules/stats', 'Permanent Redirect'), // approved as experimental RFC
            400 => Yii::t('app/modules/stats', 'Bad Request'),
            401 => Yii::t('app/modules/stats', 'Unauthorized'),
            402 => Yii::t('app/modules/stats', 'Payment Required'),
            403 => Yii::t('app/modules/stats', 'Forbidden'),
            404 => Yii::t('app/modules/stats', 'Not Found'),
            405 => Yii::t('app/modules/stats', 'Method Not Allowed'),
            406 => Yii::t('app/modules/stats', 'Not Acceptable'),
            407 => Yii::t('app/modules/stats', 'Proxy Authentication Required'),
            408 => Yii::t('app/modules/stats', 'Request Timeout'),
            409 => Yii::t('app/modules/stats', 'Conflict'),
            410 => Yii::t('app/modules/stats', 'Gone'),
            411 => Yii::t('app/modules/stats', 'Length Required'),
            412 => Yii::t('app/modules/stats', 'Precondition Failed'),
            413 => Yii::t('app/modules/stats', 'Request Entity Too Large'),
            414 => Yii::t('app/modules/stats', 'Request-URI Too Long'),
            415 => Yii::t('app/modules/stats', 'Unsupported Media Type'),
            416 => Yii::t('app/modules/stats', 'Requested Range Not Satisfiable'),
            417 => Yii::t('app/modules/stats', 'Expectation Failed'),
            418 => Yii::t('app/modules/stats', 'I\'m a teapot'), // RFC 2324
            419 => Yii::t('app/modules/stats', 'Authentication Timeout'), // not in RFC 2616
            420 => Yii::t('app/modules/stats', 'Enhance Your Calm'), // Twitter
            420 => Yii::t('app/modules/stats', 'Method Failure'), // Spring Framework
            422 => Yii::t('app/modules/stats', 'Unprocessable Entity'), // WebDAV; RFC 4918
            423 => Yii::t('app/modules/stats', 'Locked'), // WebDAV; RFC 4918
            424 => Yii::t('app/modules/stats', 'Failed Dependency'), // WebDAV; RFC 4918
            424 => Yii::t('app/modules/stats', 'Method Failure'), // WebDAV)
            425 => Yii::t('app/modules/stats', 'Unordered Collection'), // Internet draft
            426 => Yii::t('app/modules/stats', 'Upgrade Required'), // RFC 2817
            428 => Yii::t('app/modules/stats', 'Precondition Required'), // RFC 6585
            429 => Yii::t('app/modules/stats', 'Too Many Requests'), // RFC 6585
            431 => Yii::t('app/modules/stats', 'Request Header Fields Too Large'), // RFC 6585
            444 => Yii::t('app/modules/stats', 'No Response'), // Nginx
            449 => Yii::t('app/modules/stats', 'Retry With'), // Microsoft
            450 => Yii::t('app/modules/stats', 'Blocked by Windows Parental Controls'), // Microsoft
            451 => Yii::t('app/modules/stats', 'Redirect'), // Microsoft
            451 => Yii::t('app/modules/stats', 'Unavailable For Legal Reasons'), // Internet draft
            494 => Yii::t('app/modules/stats', 'Request Header Too Large'), // Nginx
            495 => Yii::t('app/modules/stats', 'Cert Error'), // Nginx
            496 => Yii::t('app/modules/stats', 'No Cert'), // Nginx
            497 => Yii::t('app/modules/stats', 'HTTP to HTTPS'), // Nginx
            499 => Yii::t('app/modules/stats', 'Client Closed Request'), // Nginx
            500 => Yii::t('app/modules/stats', 'Internal Server Error'),
            501 => Yii::t('app/modules/stats', 'Not Implemented'),
            502 => Yii::t('app/modules/stats', 'Bad Gateway'),
            503 => Yii::t('app/modules/stats', 'Service Unavailable'),
            504 => Yii::t('app/modules/stats', 'Gateway Timeout'),
            505 => Yii::t('app/modules/stats', 'HTTP Version Not Supported'),
            506 => Yii::t('app/modules/stats', 'Variant Also Negotiates'), // RFC 2295
            507 => Yii::t('app/modules/stats', 'Insufficient Storage'), // WebDAV; RFC 4918
            508 => Yii::t('app/modules/stats', 'Loop Detected'), // WebDAV; RFC 5842
            509 => Yii::t('app/modules/stats', 'Bandwidth Limit Exceeded'), // Apache bw/limited extension
            510 => Yii::t('app/modules/stats', 'Not Extended'), // RFC 2774
            511 => Yii::t('app/modules/stats', 'Network Authentication Required'), // RFC 6585
            598 => Yii::t('app/modules/stats', 'Network read timeout error'), // Unknown
            599 => Yii::t('app/modules/stats', 'Network connect timeout error'), // Unknown
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

    public function getRobotInfo($robot_id, $cache_timeout = 3600)
    {
        $db = Robots::getDb();
        $this->robot = $db->cache(function ($db) use ($robot_id) {
            return Robots::find()->where(['id' => $robot_id])->one();
        }, $cache_timeout);
        return $this->robot;
    }

    public static function clearOldStats($period)
    {
        if(self::deleteAll("`datetime` <= '".$period."'"))
            return true;
        else
            return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser($user_id = null)
    {
        if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users']) && !$user_id)
            return $this->hasOne(\wdmg\users\models\Users::className(), ['id' => 'user_id']);
        else if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users']) && $user_id)
            return \wdmg\users\models\Users::findOne(['id' => intval($user_id)]);
        else
            return null;
    }
}