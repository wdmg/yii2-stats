<?php

namespace wdmg\stats;

use Yii;

/**
 * Statistics module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'wdmg\stats\controllers';

    /**
     * {@inheritdoc}
     */
    public $defaultRoute = 'list';

    /**
     * @var string the prefix for routing of module
     */
    public $routePrefix = "admin";

    /**
     * @var string the vendor name of module
     */
    public $vendor = "wdmg";

    /**
     * @var string the module version
     */
    public $version = "1.0.0";

    /**
     * @var array of strings missing translations
     */
    public $missingTranslation;

    /**
     * Cookie name
     * @var string
     */
    public $cookieName = 'yii2_stats';

    /**
     * Cookie expire time, 1 year
     * @var integer
     */
    public $cookieExpire = 3110400;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Set controller namespace for console commands
        if (Yii::$app instanceof \yii\console\Application)
            $this->controllerNamespace = 'wdmg\stats\commands';

        // Set current version of module
        $this->setVersion($this->version);

        // Register translations
        $this->registerTranslations();
    }

    /**
     * {@inheritdoc}
     */
    public function afterAction($action, $result)
    {

        // Log to debuf console missing translations
        if (is_array($this->missingTranslation) && YII_ENV == 'dev')
            Yii::warning('Missing translations: ' . var_export($this->missingTranslation, true), 'i18n');

        $result = parent::afterAction($action, $result);
        return $result;

    }

    // Registers translations for the module
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['app/modules/stats'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@vendor/wdmg/yii2-stats/messages',
            'on missingTranslation' => function($event) {

                if (YII_ENV == 'dev')
                    $this->missingTranslation[] = $event->message;

            },
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('app/modules/stats' . $category, $message, $params, $language);
    }
}