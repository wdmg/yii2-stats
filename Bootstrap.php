<?php

namespace wdmg\stats;

/**
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 */

use yii\base\BootstrapInterface;
use Yii;
use wdmg\stats\behaviors\ViewBehavior;
use wdmg\stats\behaviors\ControllerBehavior;


class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        // Get the module instance
        $module = Yii::$app->getModule('stats');

        // Get URL path prefix if exist
        if (isset($module->routePrefix)) {
            $app->getUrlManager()->enableStrictParsing = true;
            $prefix = $module->routePrefix . '/';
        } else {
            $prefix = '';
        }

        // Add module URL rules
        $app->getUrlManager()->addRules(
            [
                $prefix . '<module:stats>/' => '<module>/visitors/index',
                /*$prefix . '<module:stats>/view' => '<module>/visitors/view',*/
                $prefix . '<module:stats>/<controller:(visitors|robots)>/' => '<module>/<controller>',
                $prefix . '<module:stats>/<controller:(visitors|robots)>/<action:\w+>' => '<module>/<controller>/<action>',
                [
                    'pattern' => $prefix . '<module:stats>/',
                    'route' => '<module>/visitors/index',
                    'suffix' => '',
                ], /*[
                    'pattern' => $prefix . '<module:stats>/view',
                    'route' => '<module>/visitors/view',
                    'suffix' => '',
                ],*/ [
                    'pattern' => $prefix . '<module:stats>/<controller:(visitors|robots)>/',
                    'route' => '<module>/<controller>',
                    'suffix' => '',
                ], [
                    'pattern' => $prefix . '<module:stats>/<controller:(visitors|robots)>/<action:\w+>',
                    'route' => '<module>/<controller>/<action>',
                    'suffix' => '',
                ],
            ],
            true
        );

        if(!($app instanceof \yii\console\Application) && $module) {

            // View behavior to render counter
            $app->get('view')->attachBehavior('behaviors/ViewBehavior', [
                'class' => ViewBehavior::class,
            ]);

            // Controller behavior to write stat data
            if($module->collectStats) {
                $app->attachBehavior('behaviors/ControllerBehavior', [
                    'class' => ControllerBehavior::class,
                ]);
            }
        }
    }
}
