[![Progress](https://img.shields.io/badge/required-Yii2_v2.0.13-blue.svg)](https://packagist.org/packages/yiisoft/yii2) [![Github all releases](https://img.shields.io/github/downloads/wdmg/yii2-stats/total.svg)](https://GitHub.com/wdmg/yii2-stats/releases/) [![GitHub version](https://badge.fury.io/gh/wdmg%2Fyii2-stats.svg)](https://github.com/wdmg/yii2-stats) ![Progress](https://img.shields.io/badge/progress-in_development-red.svg) [![GitHub license](https://img.shields.io/github/license/wdmg/yii2-stats.svg)](https://github.com/wdmg/yii2-stats/blob/master/LICENSE)

# Yii2 Statistics Module
Statistics module for Yii2

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.13 and newest
* [Yii2 ChartJS](https://github.com/wdmg/yii2-chartjs) widget
* [GeoIP2 PHP API](https://github.com/maxmind/GeoIP2-php)

# Installation
To install the module, run the following command in the console:

`$ composer require "wdmg/yii2-stats:dev-master"`

After configure db connection, run the following command in the console:

`$ php yii stats/init`

And select the operation you want to perform:
  1) Apply all module migrations
  2) Revert all module migrations
  3) Update MaxMind GeoIP2 DB

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
            'routePrefix' => 'admin'
        ],
        ...
    ],

If you have connected the module not via a composer add Bootstrap section:

`
$config['bootstrap'][] = 'wdmg\stats\Bootstrap';
`

# Status and version [in progress development]
* v.1.0.4 - Added bot detection, custom view options, storage period and clear old stats
* v.1.0.3 - Fixing tables names in migrations
* v.1.0.2 - MaxMind GeoIp and Charts.js
* v.1.0.1 - Added view for visitors statistics
* v.1.0.0 - First pre-release

# Copyright and License
This product also includes GeoLite2 data created by MaxMind, available from [https://www.maxmind.com](https://www.maxmind.com)