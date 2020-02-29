<?php

namespace wdmg\stats;

/**
 * Yii2 Statistics
 *
 * @category        Module
 * @version         1.1.10
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-stats
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use wdmg\base\BaseModule;
use wdmg\stats\behaviors\ViewBehavior;
use wdmg\stats\behaviors\ControllerBehavior;

/**
 * Statistics module definition class
 */
class Module extends BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'wdmg\stats\controllers';

    /**
     * {@inheritdoc}
     */
    public $defaultRoute = "visitors/index";

    /**
     * @var string, the name of module
     */
    public $name = "Statistics";

    /**
     * @var string, the description of module
     */
    public $description = "Statistic module";

    /**
     * @var string the module version
     */
    private $version = "1.1.10";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 10;

    /**
     * @var array of strings missing translations
     */
    public $missingTranslation;

    /**
     * Flag, collect statistics
     * @var boolean
     */
    public $collectStats = true;

    /**
     * Statistics storage period, days
     * @var integer: 0 - infinity
     */
    public $storagePeriod = 0;

    /**
     * Flag, do not collect statistics in dev mode
     * @var boolean
     */
    public $ignoreDev = false;

    /**
     * Flag, do not collect statistics for ajax-requests
     * @var boolean
     */
    public $ignoreAjax = true;

    /**
     * Flag, use charts for statistic
     * @var boolean
     */
    public $useChart = true;

    /**
     * List of ignored routing
     * @var array
     */
    public $ignoreRoute = ['/admin', '/admin/', '/assets/', '/captcha/'];

    /**
     * List of ignored IP`s
     * @var array
     */
    public $ignoreListIp = [];

    /**
     * List of ignored User Agents
     * @var array
     */
    public $ignoreListUA = [];

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
     * Advertising Systems
     * @var array
     */
    public $advertisingSystems = ["gclid", "yclid", "fbclid"];

    /**
     * Social Networks
     * @var array
     */
    public $socialNetworks = ["facebook", "vk", "away.vk.com", "ok", "odnoklassniki", "instagram", "twitter", "linkedin", "pinterest", "tumblr", "tumblr", "tumblr", "flickr", "myspace", "meetup", "tagged", "ask.fm", "meetme", "classmates", "loveplanet", "badoo", "twoo", "tinder", "lovoo"];

    /**
     * Search Engines
     * @var array
     */
    public $searchEngines = ["google", "yandex", "mail", "rambler", "yahoo", "bing", "baidu", "aol", "ask", "duckduckgo"];

    /**
     * Client Platforms
     * @var array
     */
    public $clientPlatforms = [
        '/windows nt 10/i' => [
            'title' => 'Windows 10',
            'icon' => 'icon-win-10-os'
        ],
        '/windows nt 6.3/i' => [
            'title' => 'Windows 8.1',
            'icon' => 'icon-win-8-os'
        ],
        '/windows nt 6.2/i' => [
            'title' => 'Windows 8',
            'icon' => 'icon-win-8-os'
        ],
        '/windows nt 6.1/i' => [
            'title' => 'Windows 7',
            'icon' => 'icon-win-7-os'
        ],
        '/windows nt 6.0/i' => [
            'title' => 'Windows Vista',
            'icon' => 'icon-win-7-os'
        ],
        '/windows nt 5.2/i' => [
            'title' => 'Windows Server 2003/XP x64',
            'icon' => 'icon-win-xp-os'
        ],
        '/windows nt 5.1/i' => [
            'title' => 'Windows XP',
            'icon' => 'icon-win-xp-os'
        ],
        '/windows xp/i' => [
            'title' => 'Windows XP',
            'icon' => 'icon-win-xp-os'
        ],
        '/windows nt 5.0/i' => [
            'title' => 'Windows 2000',
            'icon' => 'icon-win-xp-os'
        ],
        '/windows me/i' => [
            'title' => 'Windows ME',
            'icon' => 'icon-win-xp-os'
        ],
        '/win98/i' => [
            'title' => 'Windows 98',
            'icon' => 'icon-win-xp-os'
        ],
        '/win95/i' => [
            'title' => 'Windows 95',
            'icon' => 'icon-win-xp-os'
        ],
        '/win16/i' => [
            'title' => 'Windows 3.11',
            'icon' => 'icon-win-xp-os'
        ],
        '/macintosh|mac os x/i' => [
            'title' => 'Mac OS X',
            'icon' => 'icon-os-x-os'
        ],
        '/mac_powerpc/i' => [
            'title' => 'Mac OS 9',
            'icon' => 'icon-mac-9-os'
        ],
        '/linux/i' => [
            'title' => 'Linux',
            'icon' => 'icon-linux-os'
        ],
        '/ubuntu/i' => [
            'title' => 'Ubuntu',
            'icon' => 'icon-ubuntu-os'
        ],
        '/iphone/i' => [
            'title' => 'iPhone',
            'icon' => 'icon-ios-os'
        ],
        '/ipod/i' => [
            'title' => 'iPod',
            'icon' => 'icon-ios-os'
        ],
        '/ipad/i' => [
            'title' => 'iPad',
            'icon' => 'icon-ios-os'
        ],
        '/android/i' => [
            'title' => 'Android',
            'icon' => 'icon-android-os'
        ],
        '/blackberry/i' => [
            'title' => 'BlackBerry',
            'icon' => 'icon-blackberry-os'
        ],
        '/webos/i' => [
            'title' => 'webOS',
            'icon' => 'icon-web-os'
        ]
    ];

    /**
     * Client Browsers
     * @var array
     */
    public $clientBrowsers = [
        '/msie/i' => [
            'title' => 'Internet Explorer',
            'icon' => 'icon-ie-browser'
        ],
        '/firefox/i' => [
            'title' => 'Firefox',
            'icon' => 'icon-firefox-browser'
        ],
        '/safari/i' => [
            'title' => 'Safari',
            'icon' => 'icon-safari-browser'
        ],
        '/chrome/i' => [
            'title' => 'Chrome',
            'icon' => 'icon-chrome-browser'
        ],
        '/edge/i' => [
            'title' => 'Edge',
            'icon' => 'icon-edge-browser'
        ],
        '/opera/i' => [
            'title' => 'Opera',
            'icon' => 'icon-opera-browser'
        ],
        '/netscape/i' => [
            'title' => 'Netscape',
            'icon' => 'icon-netscape-browser'
        ],
        '/maxthon/i' => [
            'title' => 'Maxthon',
            'icon' => 'icon-maxthon-browser'
        ],
        '/konqueror/i' => [
            'title' => 'Konqueror',
            'icon' => 'icon-konqueror-browser'
        ],
        '/ucbrowser/i' => [
            'title' => 'UC Browser',
            'icon' => 'icon-uc-browser'
        ],
        '/vivaldi/i' => [
            'title' => 'Vivaldi',
            'icon' => 'icon-vivaldi-browser'
        ],
        '/yabrowser/i' => [
            'title' => 'Yandex.Browser',
            'icon' => 'icon-yandex-browser'
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Set version of current module
        $this->setVersion($this->version);

        // Set priority of current module
        $this->setPriority($this->priority);

    }

    /**
     * {@inheritdoc}
     */
    public function dashboardNavItems($createLink = false)
    {
        return [
            'label' => $this->name,
            'url' => [$this->routePrefix . '/stats/'],
            'active' => in_array(\Yii::$app->controller->module->id, ['stats']),
            'items' => [
                [
                    'label' => Yii::t('app/modules/stats', 'Visitors'),
                    'url' => [$this->routePrefix . '/stats/'],
                    'icon' => 'fa fa-fw fa-user',
                    'active' => (in_array(\Yii::$app->controller->module->id, ['stats']) &&  Yii::$app->controller->action->id == 'index'),
                ],
                [
                    'label' => Yii::t('app/modules/stats', 'Robots'),
                    'url' => [$this->routePrefix . '/stats/robots'],
                    'icon' => 'fa fa-fw fa-user-secret',
                    'active' => (in_array(\Yii::$app->controller->module->id, ['stats']) &&  Yii::$app->controller->action->id == 'robots'),
                ],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);

        // Add stats behaviors for web app
        if(!($app instanceof \yii\console\Application) && $this->module) {

            // View behavior to render counter
            $app->get('view')->attachBehavior('behaviors/ViewBehavior', [
                'class' => ViewBehavior::class,
            ]);

            // Controller behavior to write stat data
            if($this->collectStats) {
                $app->attachBehavior('behaviors/ControllerBehavior', [
                    'class' => ControllerBehavior::class,
                ]);
            }
        }
    }

    /**
     * Updating GeoIP database from geolite.maxmind.com
     */
    public static function updateGeoIP() {

        $geolitePath = "https://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.tar.gz";

        $databasePath = __DIR__."/database/";
        if (!file_exists($databasePath) && !is_dir($databasePath))
            \yii\helpers\FileHelper::createDirectory($databasePath, $mode = 0775, $recursive = true);

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec("curl -sS ".$geolitePath." > ".$databasePath."/GeoLite2-Country.tar.gz");
            try {
                $phar = new \PharData($databasePath."/GeoLite2-Country.tar.gz");
                if ($phar->extractTo($databasePath, null, true)) {
                    $files = \yii\helpers\FileHelper::findFiles($databasePath, [
                        'only' => ['*.mmdb']
                    ]);
                    foreach($files as $file) {
                        $fileName = pathinfo(\yii\helpers\FileHelper::normalizePath($file), PATHINFO_BASENAME);
                        copy($file, $databasePath.$fileName);
                    }
                }
                unlink(__DIR__."/database/GeoLite2-Country.tar.gz");
                Yii::info("OK! GeoIP database updated successful.");
                return true;
            } catch (Exception $e) {
                Yii::warning("An error occurred while updating GeoIP database: {error}", ['error' => $e]);
                return false;
            }
        } else {
            try {
                exec("curl -sS ".$geolitePath." > ".$databasePath."GeoLite2-Country.tar.gz");
                exec("tar -xf ".$databasePath."GeoLite2-Country.tar.gz -C ".$databasePath." --strip-components 1");
                exec("rm ".$databasePath."GeoLite2-Country.tar.gz");
                Yii::info("OK! GeoIP database updated successful.");
                return true;
            } catch (Exception $e) {
                Yii::warning("An error occurred while updating GeoIP database: {error}", ['error' => $e]);
                return false;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function install() {
        return (parent::install() && self::updateGeoIP()) ? true : false;
    }
}