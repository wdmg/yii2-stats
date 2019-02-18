<?php

namespace wdmg\stats;
use yii\web\AssetBundle;

class MainAsset extends AssetBundle
{
    public $sourcePath = '@app/vendor/wdmg/yii2-stats/assets';
    public $css = [
        'css/stats.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public function init()
    {
        parent::init();
        /*
        $this->publishOptions['beforeCopy'] = function ($from, $to) {
            $dirname = basename(dirname($from));
            return $dirname === 'fonts' || $dirname === 'css';
        };
        */
    }

}

?>