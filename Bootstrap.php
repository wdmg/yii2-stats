<?php

namespace wdmg\stats;

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
        $prefix = (isset($module->routePrefix) ? $module->routePrefix . '/' : '');

        // Add module URL rules
        $app->getUrlManager()->addRules(
            [
                $prefix . '<module:stats>/' => '<module>/visitors/index',
                $prefix . '<module:stats>/<controller:visitors>/' => '<module>/<controller>',
                $prefix . '<module:stats>/<controller:(visitors|item)>/<action:\w+>' => '<module>/<controller>/<action>',
                [
                    'pattern' => $prefix . '<module:stats>/',
                    'route' => '<module>/visitors/index',
                    'suffix' => '',
                ], [
                    'pattern' => $prefix . '<module:stats>/<controller:visitors>/',
                    'route' => '<module>/<controller>',
                    'suffix' => '',
                ], [
                    'pattern' => $prefix . '<module:stats>/<controller:(visitors|item)>/<action:\w+>',
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
