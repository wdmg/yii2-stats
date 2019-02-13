<?php

namespace wdmg\stats;
use yii\web\AssetBundle;

class MainAsset extends AssetBundle
{
    public $sourcePath = '@app/vendor/wdmg/yii2-stats/assets';
    public $css = [
        'css/icons.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}

?>