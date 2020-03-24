<?php

namespace wdmg\stats;

/**
 * Yii2 Statistics
 *
 * @category        Module
 * @version         1.2.0
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-stats
 * @copyright       Copyright (c) 2019 - 2020 W.D.M.Group, Ukraine
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
    private $version = "1.2.0";

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
     * Flag, collect profiling data
     * @var boolean
     */
    public $collectProfiling = false;

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
     * @var Visitors::class instance (used in collect profiling)
     */
    private $_visitor;

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

        // Autoload system params
        $attributes = [
            'collectStats' => 'boolean',
            'collectProfiling' => 'boolean',
            'storagePeriod' => 'integer',
            'ignoreDev' => 'boolean',
            'ignoreAjax' => 'boolean',
            'useChart' => 'boolean',
            'ignoreRoute' => 'array',
            'ignoreListIp' => 'array',
            'ignoreListUA' => 'array',
            'cookieName' => 'string',
            'cookieExpire' => 'integer',
            'advertisingSystems' => 'array',
            'socialNetworks' => 'array',
            'searchEngines' => 'array',
            'clientPlatforms' => 'array',
            'clientBrowsers' => 'array',
        ];

        foreach ($attributes as $attribute => $type) {
            if (isset(Yii::$app->params["stats" . "." . $attribute]) && isset($this->$attribute)) {

                if (\gettype($this->$attribute) == $type)
                    $this->$attribute = Yii::$app->params["stats" . "." . $attribute];

            }
        }
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
                [
                    'label' => Yii::t('app/modules/stats', 'Load'),
                    'url' => [$this->routePrefix . '/stats/load/'],
                    'icon' => 'fa fa-fw fa-weight',
                    'active' => (in_array(\Yii::$app->controller->module->id, ['load']) &&  Yii::$app->controller->action->id == 'index'),
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
            if ($this->collectStats) {
                $app->attachBehavior('behaviors/ControllerBehavior', [
                    'class' => ControllerBehavior::class,
                ]);
            }

            // Collect profiling data
            if ($this->collectProfiling && $this->collectStats) {

                \yii\base\Event::on(\yii\web\Response::class, \yii\web\Response::EVENT_AFTER_SEND, function ($event) {
                    $db_profiling = Yii::getLogger()->getDbProfiling();
                    $elapsed_time = Yii::getLogger()->getElapsedTime();
                    $memory_usage = memory_get_peak_usage() / (1024 * 1024);
                    $results = [
                        'et' => round($elapsed_time, 4), // sec.
                        'mu' => round($memory_usage, 2), // MB
                        'dbq' => intval($db_profiling[0]), // queries
                        'dbt' => round($db_profiling[1], 4) // sec.
                    ];

                    if ($visitor = $this->getVisitor()) {
                        $visitor->params = serialize($results);
                        $visitor->update();
                    }

                    /*if ($cache = Yii::$app->getCache()) {
                        if (!$cache->exists('profiling')) {
                            $cache->buildKey('profiling');
                            $cache->set('profiling', [
                                'timestamp' => time(),
                                'items' => [$results]
                            ], -1);
                        } else {
                            $profiling = $cache->get('profiling');

                            if (isset($profiling['items'])) {
                                $cache->set('profiling', [
                                    'timestamp' => $profiling['timestamp'],
                                    'items' => array_merge((is_array($profiling['items'])) ? $profiling['items'] : [], [$results])
                                ], -1);
                            } else {
                                $cache->set('profiling', [
                                    'timestamp' => $profiling['timestamp'],
                                    'items' => [$results]
                                ], -1);
                            }
                        }

                        if ($cache->exists('profiling')) {
                            if ($profiling = $cache->get('profiling')) {
                                if (is_array($profiling['items'])) {
                                    $elapsed_time = 0;
                                    $memory_usage = 0;
                                    $db_queries = 0;
                                    $db_time = 0;

                                    $i = 1;
                                    foreach ($profiling['items'] as $data) {

                                        $elapsed_time += (isset($data['et'])) ? $data['et'] : 0;
                                        $elapsed_time_averg = $elapsed_time / $i;

                                        $memory_usage += (isset($data['mu'])) ? $data['mu'] : 0;
                                        $memory_usage_averg = $memory_usage / $i;

                                        $db_queries += (isset($data['dbq'])) ? $data['dbq'] : 0;
                                        $db_queries_averg = $db_queries / $i;

                                        $db_time += (isset($data['dbt'])) ? $data['dbt'] : 0;
                                        $db_time_averg = $db_time / $i;

                                        $i++;
                                    }

                                    $cache->set('profiling', array_merge([
                                        'timestamp' => $profiling['timestamp'],
                                        'items' => $profiling['items']
                                    ], [
                                        'summary' => [
                                            'et' => $elapsed_time, // sec.
                                            'mu' => $memory_usage, // MB
                                            'dbq' => $db_queries, // queries
                                            'dbt' => $db_time // sec.
                                        ],
                                        'average' => [
                                            'et' => round($elapsed_time_averg, 4), // sec.
                                            'mu' => round($memory_usage_averg, 2), // MB
                                            'dbq' => round($db_queries_averg, 4), // queries
                                            'dbt' => round($db_time_averg, 4) // sec.
                                        ],
                                    ]), -1);


                                    if ((time() - 60) >= $profiling['timestamp']) {
                                        // @TODO: Write data to DB
                                        $cache->delete('profiling');
                                    }

                                }
                            }
                        }
                    }*/

                });
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

    public function setVisitor($visitor = null) {
        if ($visitor instanceof \yii\db\ActiveRecord) {
            $this->_visitor = $visitor;
        }
    }
    public function getVisitor() {
        return $this->_visitor;
    }
}