<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\bootstrap\ButtonGroup;
use yii\bootstrap\Modal;
use wdmg\widgets\ChartJS;
use wdmg\widgets\DatePicker;
use wdmg\widgets\SelectInput;
use wdmg\stats\MainAsset;

/* @var $this yii\web\View */
/* @var $searchModel wdmg\stats\models\VisitorsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/stats', 'Statistics');
$this->params['breadcrumbs'][] = $this->title;
$bundle = MainAsset::register($this);

$visitorTypes = $searchModel::getVisitorTypeList();
$statusCodes = $searchModel::getStatusCodeList();

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
        <h1>
            <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
    </div>
    <div class="stats visitors-index">

        <?php Pjax::begin(); ?>

        <?php echo $this->render('_options', ['model' => $searchModel]); ?>

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

            if($searchModel->period == 'custom')
                $buttonClass['custom'] = 'btn-primary';
            else
                $buttonClass['custom'] = 'btn-default';

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
                Button::widget([
                    'label' => Yii::t('app/modules/stats', 'Custom date'),
                    'options' => [
                        'name' => 'period',
                        'type' => 'submit',
                        'value' => 'custom',
                        'class' => 'btn ' . $buttonClass['custom']
                    ]
                ]),
            ]
        ]); ?>

        <?php ActiveForm::end(); ?>

        <?php if ($module->useChart && $searchModel->viewChart) {
            echo ChartJS::widget([
                'type' => 'line',
                'options' => [
                    'width' => 640,
                    'height' => 260
                ],
                'data' => $chartData
            ]);
        } ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
            'columns' => [
                [
                    'attribute' => 'request_uri',
                    'format' => 'raw',
                    'value' => function($data) {
                        if ($data->https)
                            return Html::a('<span class="glyphicon glyphicon-lock text-success" data-toggle="tooltip" title="HTTPS"></span>&nbsp;' . $data->request_uri, $data->request_uri, ['target' => "_blank", 'title' => $data->request_uri, 'data-toggle' => "tooltip", 'data-pajax' => 0]);
                        else
                            return Html::a('<span class="glyphicon glyphicon-lock text-muted" data-toggle="tooltip" title="HTTPS"></span>&nbsp;' . $data->request_uri, $data->request_uri, ['target' => "_blank", 'title' => $data->request_uri, 'data-toggle' => "tooltip", 'data-pajax' => 0]);
                    },
                ],
                [
                    'attribute' => 'robot',
                    'format' => 'html',
                    'filter' => true,
                    'visible' => $searchModel->viewRobots,
                    'value' => function ($data) use ($searchModel) {
                        $robot = $searchModel->getRobotInfo($data->robot_id);
                        if ($robot && count($robot) > 0) {
                            if ($robot->is_badbot) {
                                return '<span class="label label-danger">' . $robot->name . '</span>';
                            } else {
                                return '<span class="label label-default">' . $robot->name . '</span>';
                            }
                        } else {
                            return '&nbsp;';
                        }
                    },
                ],
                [
                    'attribute' => 'referer_uri',
                    'format' => 'raw',
                    'filter' => true,
                    'visible' => $searchModel->viewReferrerURI,
                    'value' => function($data) {
                        if ($data->referer_uri)
                            return Html::a($data->referer_uri, $data->referer_uri, ['target' => "_blank", 'title' => $data->referer_uri, 'data-toggle' => "tooltip", 'data-pajax' => 0]);
                        else
                            return '&nbsp;';
                    },
                ],
                [
                    'attribute' => 'referer_host',
                    'format' => 'html',
                    'filter' => Html::activeTextInput($searchModel, 'referer_host', [
                        'class' => 'form-control',
                    ]),
                    'visible' => $searchModel->viewReferrerHost,
                    'value' => function($data) {
                        if ($data->referer_host)
                            return '<img src="http://'.$data->referer_host.'/favicon.ico" style="width:18px;margin-right:4px;" /> '.Html::a($data->referer_host, $data->referer_host, ['_target' => "blank"]);
                        else
                            return '&nbsp;';
                    },
                ],
                [
                    'attribute' => 'user_id',
                    'format' => 'html',
                    'label' => Yii::t('app/modules/stats', 'User'),
                    'visible' => $searchModel->viewAuthUser,
                    'value' => function($data) {
                        if($data->user_id == $data->user['id'])
                            if($data->user['id'] && $data->user['username'])
                                return Html::a($data->user['username'], ['/' . $this->context->module->routePrefix . '/users/view/?id='.$data->user['id']], [
                                    'target' => '_blank',
                                    'data-pjax' => 0
                                ]);
                            else
                                return $data->user_id;
                        else
                            return $data->user_id;
                    }
                ],
                [
                    'label' => Yii::t('app/modules/stats', 'Client'),
                    'format' => 'raw',
                    'filter' => false,
                    'visible' => $searchModel->viewClientOS,
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
                    'filter' => true,
                    'visible' => $searchModel->viewClientIP,
                    'value' => function($data) use ($reader) {
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
                        return Yii::t('app/modules/stats', 'Unknow IP');
                    },
                ],
                [
                    'attribute' => 'datetime',
                    'format' => 'datetime',
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'datetime',
                        'options' => [
                            'class' => 'form-control',
                            'value' => date('d.m.Y H:i:s')
                        ],
                        'pluginOptions' => [
                            'className' => '.datepicker',
                            'input' => '.form-control',
                            'format' => 'DD.MM.YYYY HH:mm:ss',
                            'toggle' => '.input-group-btn > button',
                        ]
                    ]),
                    'headerOptions' => [
                        'class' => 'text-center'
                    ],
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    'value' => function($data) {
                        return date('d.m.Y H:i:s', $data->datetime);
                    },
                ],
                [
                    'attribute' => 'type',
                    'format' => 'html',
                    'filter' => SelectInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'type',
                        'items' => $visitorTypes,
                        'options' => [
                            'class' => 'form-control'
                        ]
                    ]),
                    'visible' => $searchModel->viewTransitionType,
                    'headerOptions' => [
                        'class' => 'text-center'
                    ],
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    'value' => function($data) use ($visitorTypes) {

                        if ($visitorTypes && $data->type !== null)
                            return $visitorTypes[$data->type];
                        else
                            return $data->type;
                    },
                ],
                [
                    'attribute' => 'code',
                    'format' => 'raw',
                    'filter' => Html::activeTextInput($searchModel, 'code', [
                        'class' => 'form-control',
                    ]),
                    'value' => function($data) {
                        if (intval($data->code) == 200)
                            return '<span class="label label-success">'.$data->code.'</span>';
                        elseif (intval($data->code) >= 300 && intval($data->code) < 400)
                            return '<span class="label label-info">'.$data->code.'</span>';
                        elseif (intval($data->code) >= 400 && intval($data->code) < 500)
                            return '<span class="label label-danger">'.$data->code.'</span>';
                        elseif (intval($data->code) >= 500 && intval($data->code) < 600)
                            return '<span class="label label-warning">'.$data->code.'</span>';
                        else
                            return '<span class="label label-default">'.$data->code.'</span>';
                    },
                ],
                [
                    'class' => \yii\grid\ActionColumn::className(),
                    'buttons'=> [
                        'view' => function($url, $data, $key) use ($module) {
                            $url = Yii::$app->getUrlManager()->createUrl([$module->routePrefix . '/stats/view', 'id' => $data['id']]);
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                'class' => 'visitor-details-link',
                                'title' => Yii::t('yii', 'View'),
                                'data-toggle' => 'modal',
                                'data-target' => '#visitorDetails',
                                'data-id' => $key,
                                'data-pjax' => '1'
                            ]);
                        },
                        'update' => function() {
                            return false;
                        },
                        'delete' => function() {
                            return false;
                        },
                    ],
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>

        <?= Html::a(Yii::t('app/modules/stats', 'Clear old data'), ['clear'], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('app/modules/stats', 'Are you sure?'),
                'method' => 'post',
            ]
        ]); ?>
    </div>

<?php $this->registerJs(<<< JS
$('body').delegate('.visitor-details-link', 'click', function(event) {
    event.preventDefault();
    $.get(
        $(this).attr('href'),
        function (data) {
            $('#visitorDetails .modal-body').html(data);
            $('#visitorDetails').modal();
        }  
    );
});
JS
); ?>

<?php Modal::begin([
    'id' => 'visitorDetails',
    'header' => '<h4 class="modal-title">'.Yii::t('app/modules/stats', 'Visit Information').'</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">'.Yii::t('app/modules/stats', 'Close').'</a>',
    'clientOptions' => [
        'show' => false
    ]
]); ?>
<?php Modal::end(); ?>

<?php echo $this->render('../_debug'); ?>