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

        if (YII_DEBUG || YII_ENV == 'dev' || Yii::$app->request->isAjax)
            return;

        echo '<!-- start_counter -->';

    }

}