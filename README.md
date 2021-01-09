[![Yii2](https://img.shields.io/badge/required-Yii2_v2.0.40-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Downloads](https://img.shields.io/packagist/dt/wdmg/yii2-stats.svg)](https://packagist.org/packages/wdmg/yii2-stats)
[![Packagist Version](https://img.shields.io/packagist/v/wdmg/yii2-stats.svg)](https://packagist.org/packages/wdmg/yii2-stats)
![Progress](https://img.shields.io/badge/progress-ready_to_use-green.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-stats.svg)](https://github.com/wdmg/yii2-stats/blob/master/LICENSE)

<img src="./docs/images/yii2-stats.png" width="100%" alt="Yii2 Stats" />

# Yii2 Statistics Module
Statistics module for Yii2. The module takes into account site visitors with details on IP, web browser and user agent. In addition, the module allows you to keep statistics of the load on the web server and database.

This module is an integral part of the [Butterfly.СMS](https://butterflycms.com/) content management system, but can also be used as an standalone extension.

Copyrights (c) 2019-2021 [W.D.M.Group, Ukraine](https://wdmg.com.ua/)

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.40 and newest
* [Yii2 Base](https://github.com/wdmg/yii2-base) module (required)
* [Yii2 ChartJS](https://github.com/wdmg/yii2-chartjs) widget
* [Yii2 SelectInput](https://github.com/wdmg/yii2-selectinput) widget
* [GeoIP2 PHP API](https://github.com/maxmind/GeoIP2-php)
* [Yii2 Users](https://github.com/wdmg/yii2-users) module (optionaly)

# Installation
To install the module, run the following command in the console:

`$ composer require "wdmg/yii2-stats"`

After configure db connection, run the following command in the console:

`$ php yii stats/init`

And select the operation you want to perform:
  1) Apply all module migrations
  2) Revert all module migrations
  3) Update MaxMind GeoLite2 database

# Migrations
In any case, you can execute the migration run the following command in the console:

`$ php yii migrate --migrationPath=@vendor/wdmg/yii2-stats/migrations`

# Configure

To add a module to the project, add the following data in your configuration file:

    'modules' => [
        ...
        'stats' => [
            'class' => 'wdmg\stats\Module',
            'collectStats' => true,
            'collectProfiling' => true,
            'detectLocation' => false,
            'routePrefix' => 'admin',
            'storagePeriod' => 0,
            'ignoreDev' => true,
            'ignoreAjax' => true,
            'useChart' => true,
            'ignoreRoute' => ['/admin', '/admin/'],
            'ignoreListIp' => ['::1', '127.0.0.1'],
            'ignoreListUA' => [],
            'cookieName' => 'yii2_stats',
            'cookieExpire' => 3110400,
            'maxmindLicenseKey' => false,
            'advertisingSystems' => ["gclid", "yclid", "fbclid", ...],
            'socialNetworks' => ["facebook", "instagram", "twitter", ...],
            'searchEngines' => ["google", "yandex", "yahoo", ...],
            'clientPlatforms' => [
                '/windows nt 10/i' => [
                    'title' => 'Windows 10',
                    'icon' => 'icon-win-10-os'
                ],
                ...
            ],
            'clientBrowsers' => [
                '/msie/i' => [
                    'title' => 'Internet Explorer',
                    'icon' => 'icon-ie-browser'
                ],
                ...
            ]
        ],
        ...
    ],

# Options

| Name                | Type    | Default       | Description                   |
|:------------------- |:-------:|:------------- |:----------------------------- |
| collectStats        | boolean | `true`        | Collect statistics with this module? |
| detectLocation      | boolean | `false`       | Detect GEO location by IP? |
| routePrefix         | string  | 'admin'       | Route prefix to the module control panel. |
| storagePeriod       | integer | 0             | Days, how many to store statistics. 0 - infinity. |
| useChart            | boolean | `true`        | Use charts when displaying statistics. |
| ignoreDev           | boolean | `true`        | Ignore activity in development mode. |
| ignoreAjax          | boolean | `true`        | Ignoring activity for Ajax requests. |
| ignoreRoute         | array   | ['/admin']    | Ignoring the activity at the specified routing. |
| ignoreListIp        | array   | ['127.0.0.1'] | Ignoring activity from specified IP addresses. |
| ignoreListUA        | array   | [...]         | Ignoring of activity at specified UserAgents. |
| cookieName          | string  | 'yii2_stats'  | The name of the cookie to store the visit ID. |
| cookieExpire        | integer | 3110400       | Cookie lifetime. |
| maxmindLicenseKey   | mixed   | `false`       | MaxMind LicenseKey for GeoLite2 databases. |
| advertisingSystems  | array   | [...]         | List to detect the transition from advertising sites. |
| socialNetworks      | array   | [...]         | List for detecting transition from social networks. |
| searchEngines       | array   | [...]         | List for detecting the transition from search engines. |
| clientPlatforms     | array   | [...]         | List for detecting the client's OS. |
| clientBrowsers      | array   | [...]         | Client's Browser detection list. |

# Routing
Use the `Module::dashboardNavItems()` method of the module to generate a navigation items list for NavBar, like this:

    <?php
        echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
            'label' => 'Modules',
            'items' => [
                Yii::$app->getModule('stats')->dashboardNavItems(),
                ...
            ]
        ]);
    ?>
    
# Status and version [ready to use]
* v.1.2.5 - RBAC implementation
* v.1.2.4 - Update README.md
* v.1.2.3 - MaxMind License Key default property fixed and update MaxMind for Win platform fixed
* v.1.2.2 - Update README.md and dependencies versions
* v.1.2.1 - Added pagination, fixed updating GeoIP database from MaxMind.com
* v.1.2.0 - Collect profiling data

# Copyright and License
This product also includes GeoLite2 data created by MaxMind, available from [https://www.maxmind.com](https://www.maxmind.com)
