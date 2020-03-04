<?php

use wdmg\widgets\ChartJS;
use yii\widgets\DetailView;

?>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6">
        <h4><?= Yii::t('app/modules/stats', 'Web server load') ?></h4>
        <?php
        echo ChartJS::widget([
            'type' => 'line',
            'options' => [
                'width' => 640,
                'height' => 260
            ],
            'data' => $chartData['server']
        ]);
        ?>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6">
        <h4><?= Yii::t('app/modules/stats', 'DB server load') ?></h4>
        <?php
        echo ChartJS::widget([
            'type' => 'line',
            'options' => [
                'width' => 640,
                'height' => 260
            ],
            'data' => $chartData['db']
        ]);
        ?>
    </div>
</div>