<?php

namespace wdmg\stats\behaviors;
use Yii;
use yii\web\View;

class ViewBehavior extends \yii\base\Behavior
{

    public function events()
    {
        return [
            View::EVENT_END_BODY => 'onEndBody'
        ];
    }

    public function onEndBody($event)
    {
        // Get stats module
        if (Yii::$app->hasModule('admin/stats'))
            $module = Yii::$app->getModule('admin/stats');
        else
            $module = Yii::$app->getModule('stats');

        if (($module->ignoreDev && (YII_DEBUG || YII_ENV == 'dev')) || ($module->ignoreAjax && Yii::$app->request->isAjax)) {
            return;
        }

        echo '<!-- start_counter -->';
    }

}