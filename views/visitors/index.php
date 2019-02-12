<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\bootstrap\ButtonGroup;
use wdmg\widgets\ChartJS;

/* @var $this yii\web\View */
/* @var $searchModel wdmg\stats\models\VisitorsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/stats', 'Statistics');
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
    </div>
    <div class="visitors-index">

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
                    'label' => 'Today',
                    'options' => [
                        'name' => 'period',
                        'type' => 'submit',
                        'value' => 'today',
                        'class' => 'btn ' . $buttonClass['today']
                    ]
                ]),
                Button::widget([
                    'label' => 'Yesterday',
                    'options' => [
                        'name' => 'period',
                        'type' => 'submit',
                        'value' => 'yesterday',
                        'class' => 'btn ' . $buttonClass['yesterday']
                    ]
                ]),
                Button::widget([
                    'label' => 'Weekly',
                    'options' => [
                        'name' => 'period',
                        'type' => 'submit',
                        'value' => 'week',
                        'class' => 'btn ' . $buttonClass['week']
                    ]
                ]),
                Button::widget([
                    'label' => 'Monthly',
                    'options' => [
                        'name' => 'period',
                        'type' => 'submit',
                        'value' => 'month',
                        'class' => 'btn ' . $buttonClass['month']
                    ]
                ]),
                Button::widget([
                    'label' => 'Yearly',
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

        <?= ChartJS::widget([
            'type' => 'line',
            'options' => [
                'width' => 640,
                'height' => 260
            ],
            'data' => $chartData
        ]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
            'columns' => [

                'request_uri',
                //'remote_addr',
                //'remote_host',
                'user_id',
                //'user_agent',
                [
                    'attribute' => 'referer_uri',
                    'format' => 'html',
                    'filter' => false,
                    'value' => function($data) {
                        if ($data->referer_uri)
                            return Html::a($data->referer_uri, $data->referer_uri, ['_target' => "blank"]);
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
                    'attribute' => 'https',
                    'format' => 'html',
                    'filter' => false,
                    'headerOptions' => [
                        'class' => 'text-center'
                    ],
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    'value' => function($data) {
                        if ($data->https)
                            return '<span class="glyphicon glyphicon-check text-success"></span>';
                        else
                            return '<span class="glyphicon glyphicon-check text-muted"></span>';
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