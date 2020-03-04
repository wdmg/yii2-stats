<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\bootstrap\ButtonGroup;
use yii\bootstrap\Tabs;
use wdmg\widgets\DatePicker;
use wdmg\widgets\SelectInput;
use wdmg\stats\StatsAsset;

/* @var $this yii\web\View */
/* @var $searchModel wdmg\stats\models\VisitorsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/stats', 'Load monitoring');
$this->params['breadcrumbs'][] = $this->context->module->name;
$this->params['breadcrumbs'][] = Yii::t('app/modules/stats', 'Load');
$bundle = StatsAsset::register($this);

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
<div class="stats-loads-index">

    <?php Pjax::begin(); ?>
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

    <?php if ($module->useChart && $searchModel->viewChart) : ?>
    <div style="clear:both">
        <?= Tabs::widget([
            'tabContentOptions' => [
                'style' => "display: inilne-block;"
            ],
            'itemOptions' => [
                'style' => "display: inilne-block; margin-bottom: 2rem;"
            ],
            'items' => [
                [
                    'label' => Yii::t('app/modules/stats', 'Average load'),
                    'content' => $this->render('_chart', [
                        'module' => $module,
                        'searchModel' => $searchModel,
                        'chartData' => $chartDataAvrg
                    ]),
                    'active' => true
                ], [
                    'label' => Yii::t('app/modules/stats', 'Summary load'),
                    'content' => $this->render('_chart', [
                        'module' => $module,
                        'searchModel' => $searchModel,
                        'chartData' => $chartDataSumm
                    ])
                ]
            ]
        ]); ?>
    </div>
    <?php endif; ?>

    <?= DetailView::widget([
        'model' => $dataProvider,
        'attributes' => array_keys($dataProvider),
        'attributes' => [
            'elapsed_time_summ' => [
                'label' => Yii::t('app/modules/stats', 'Elapsed time (summary)'),
                'value' => $dataProvider['elapsed_time_summ'] . " ms",
            ],
            'elapsed_time_avrg' => [
                'label' => Yii::t('app/modules/stats', 'Elapsed time (average)'),
                'value' => $dataProvider['elapsed_time_avrg'] . " ms",
            ],
            'memory_usage_summ' => [
                'label' => Yii::t('app/modules/stats', 'Memory usage (summary)'),
                'value' => $dataProvider['memory_usage_summ'] . " Mb",
            ],
            'memory_usage_avrg' => [
                'label' => Yii::t('app/modules/stats', 'Memory usage (average)'),
                'value' => $dataProvider['memory_usage_avrg'] . " Mb",
            ],
            'db_queries_summ' => [
                'label' => Yii::t('app/modules/stats', 'DB queries (summary)'),
                'value' => $dataProvider['db_queries_summ'],
            ],
            'db_queries_avrg' => [
                'label' => Yii::t('app/modules/stats', 'DB queries (average)'),
                'value' => $dataProvider['db_queries_avrg'],
            ],
            'db_time_summ' => [
                'label' => Yii::t('app/modules/stats', 'DB time (summary)'),
                'value' => $dataProvider['db_time_summ'] . " ms",
            ],
            'db_time_avrg' => [
                'label' => Yii::t('app/modules/stats', 'DB time (average)'),
                'value' => $dataProvider['db_time_avrg'] . " ms",
            ]
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>