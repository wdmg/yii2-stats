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
        /*$app->getUrlManager()->addRules(
            [
                $prefix . '<module:stats>/' => '<module>/items/index',
                $prefix . '<module:stats>/<controller:(list|item)>/' => '<module>/<controller>',
                $prefix . '<module:stats>/<controller:(list|item)>/<action:\w+>' => '<module>/<controller>/<action>',
            ],
            true
        );*/

        if(!($app instanceof ConsoleApplication)) {

            // Controller behavior to write stat data
            $app->attachBehavior('behaviors/ControllerBehavior', [
                'class' => ControllerBehavior::class,
            ]);
        }

    }
}
