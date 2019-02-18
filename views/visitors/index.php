<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\bootstrap\ButtonGroup;
use wdmg\widgets\ChartJS;
use wdmg\stats\MainAsset;

/* @var $this yii\web\View */
/* @var $searchModel wdmg\stats\models\VisitorsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/stats', 'Statistics');
$this->params['breadcrumbs'][] = $this->title;
$bundle = MainAsset::register($this);

$this->registerJs(<<< JS

    /* To initialize BS3 tooltips set this below */
    $(function () { 
        $("[data-toggle='tooltip']").tooltip(); 
    });
    
    /* To initialize BS3 popovers set this below */
    $(function () { 
        $("[data-toggle='popover']").popover(); 
    });

JS
);

?>
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
    </div>
    <div class="stats visitors-index">

        <?php Pjax::begin(); ?>
        <?php /*echo $this->render('_search', ['model' => $searchModel]);*/ ?>

        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>

        <?php

            if($searchModel->period == 'today')
                $buttonClass['today'] = 'btn-primary';
            else
                $buttonClass['today'] = 'btn-default';

            if($searchModel->period == 'yesterday')
                $buttonClass['yesterday'] = 'btn-primary';
            else
                $buttonClass['yesterday'] = 'btn-default';

            if($searchModel->period == 'week')
                $buttonClass['week'] = 'btn-primary';
            else
                $buttonClass['week'] = 'btn-default';

            if($searchModel->period == 'month')
                $buttonClass['month'] = 'btn-primary';
            else
                $buttonClass['month'] = 'btn-default';

            if($searchModel->period == 'year')
                $buttonClass['year'] = 'btn-primary';
            else
                $buttonClass['year'] = 'btn-default';

        ?>

        <?= ButtonGroup::widget([
            'options' => [
                'class' => 'pull-right',
                'style' => 'display:block; margin: 15px 0px;'
            ],
            'buttons' => [
                Button::widget([
                    'label' => Yii::t('app/modules/stats', 'Today'),
                    'options' => [
                        'name' => 'period',
                        'type' => 'submit',
                        'value' => 'today',
                        'class' => 'btn ' . $buttonClass['today']
                    ]
                ]),
                Button::widget([
                    'label' => Yii::t('app/modules/stats', 'Yesterday'),
                    'options' => [
                        'name' => 'period',
                        'type' => 'submit',
                        'value' => 'yesterday',
                        'class' => 'btn ' . $buttonClass['yesterday']
                    ]
                ]),
                Button::widget([
                    'label' => Yii::t('app/modules/stats', 'Weekly'),
                    'options' => [
                        'name' => 'period',
                        'type' => 'submit',
                        'value' => 'week',
                        'class' => 'btn ' . $buttonClass['week']
                    ]
                ]),
                Button::widget([
                    'label' => Yii::t('app/modules/stats', 'Monthly'),
                    'options' => [
                        'name' => 'period',
                        'type' => 'submit',
                        'value' => 'month',
                        'class' => 'btn ' . $buttonClass['month']
                    ]
                ]),
                Button::widget([
                    'label' => Yii::t('app/modules/stats', 'Yearly'),
                    'options' => [
                        'name' => 'period',
                        'type' => 'submit',
                        'value' => 'year',
                        'class' => 'btn ' . $buttonClass['year']
                    ]
                ]),
            ]
        ]); ?>

        <?php ActiveForm::end(); ?>

        <?
            if ($module->useChart) {
                echo ChartJS::widget([
                    'type' => 'line',
                    'options' => [
                        'width' => 640,
                        'height' => 260
                    ],
                    'data' => $chartData
                ]);
            }

        ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
            'columns' => [

                'request_uri',
                //'remote_addr',
                //'remote_host',
                [
                    'attribute' => 'user_id',
                    'format' => 'html',
                    'filter' => false,
                    'value' => function($data) {
                        if ($data->user_id)
                            return $data->user_id;
                        else
                            return '&nbsp;';
                    },
                ],
                //'user_agent',
                [
                    'attribute' => 'referer_uri',
                    'format' => 'raw',
                    'filter' => false,
                    'value' => function($data) {
                        if ($data->referer_uri)
                            return Html::a($data->referer_uri, $data->referer_uri, ['target' => "_blank", 'title' => $data->referer_uri, 'data-toggle' => "tooltip", 'data-pajax' => 0]);
                        else
                            return '&nbsp;';
                    },
                ],
                /*[
                    'attribute' => 'referer_host',
                    'format' => 'html',
                    'filter' => false,
                    'value' => function($data) {
                        if ($data->referer_host)
                            return Html::a($data->referer_host, $data->referer_host, ['_target' => "blank"]);
                        else
                            return '&nbsp;';
                    },
                ],*/
                [
                    'label' => Yii::t('app/modules/stats', 'Client'),
                    'format' => 'raw',
                    'filter' => false,
                    'headerOptions' => [
                        'class' => 'text-center'
                    ],
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    'value' => function($data) use ($searchModel, $clientPlatforms, $clientBrowsers) {
                        $client_os = $searchModel->getClientOS($data->user_agent, $clientPlatforms);
                        $clinet_browser = $searchModel->getClientBrowser($data->user_agent, $clientBrowsers);
                        return '<span class="icon '.$client_os['icon'].'" data-toggle="tooltip" title="'.$client_os['title'].'"></span>' . '<span class="icon '.$clinet_browser['icon'].'" data-toggle="tooltip" title="'.$clinet_browser['title'].'"></span>';
                    },
                ],
                [
                    'attribute' => 'remote_addr',
                    'format' => 'raw',
                    'filter' => false,
                    'value' => function($data) use ($reader) {
                        /*if ($data->remote_addr && $data->remote_host && $data->remote_host !== 'localhost')
                            return Html::a($data->remote_addr, 'https://check-host.net/ip-info?host=' . $data->remote_addr, ['target' => "_blank", 'data-pajax' => 0]) . ' ('.$data->remote_host . ')';
                        else if ($data->remote_addr && $data->remote_addr !== '127.0.0.1' && $data->remote_addr !== '::1')
                            return Html::a($data->remote_addr, 'https://check-host.net/ip-info?host=' . $data->remote_addr, ['target' => "_blank", 'data-pajax' => 0]);
                        else if ($data->remote_addr)
                            return $data->remote_addr;
                        else
                            return 'Unknow IP';*/

                        /*if ($data->remote_addr && $data->remote_addr !== '127.0.0.1' && $data->remote_addr !== '::1')
                            return Html::a($data->remote_addr, 'https://check-host.net/ip-info?host=' . $data->remote_addr, ['target' => "_blank", 'data-pajax' => 0]);
                        else
                            return 'Unknow IP';
                        */

                        try {
                            if ($reader && $data->remote_addr && $data->remote_addr !== '127.0.0.1' && $data->remote_addr !== '::1') {
                                $record = $reader->country($data->remote_addr);
                                return Html::tag('span', '', ['class' => 'flag flag-'.strtolower($record->country->isoCode), 'data-toggle'=> "tooltip", 'title' => $record->country->name]) . ' ' . $data->remote_addr;
                            } else if ($data->remote_addr) {
                                return $data->remote_addr;
                            }
                        } catch (Exception $e) {
                            if ($data->remote_addr) {
                                return $data->remote_addr;
                            }
                        }
                        return 'Unknow IP';

                    },
                ],
                [
                    'attribute' => 'datetime',
                    'format' => 'datetime',
                    'headerOptions' => [
                        'class' => 'text-center'
                    ],
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    'value' => function($data) {
                        return date('d-m-Y h:i:s', $data->datetime);
                    },
                ],
                //'session',
                [
                    'attribute' => 'unique',
                    'format' => 'html',
                    'filter' => false,
                    'headerOptions' => [
                        'class' => 'text-center'
                    ],
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    'value' => function($data) {
                        if ($data->unique)
                            return '<span class="glyphicon glyphicon-check text-success"></span>';
                        else
                            return '<span class="glyphicon glyphicon-check text-muted"></span>';
                    },
                ],
                //'params',
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>

<?php echo $this->render('../_debug'); ?>