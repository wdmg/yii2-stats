<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use wdmg\stats\MainAsset;

/* @var $this yii\web\View */
/* @var $searchModel wdmg\stats\models\RobotsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/stats', 'Robots');
$this->params['breadcrumbs'][] = $this->context->module->name;
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

$robotsTypes = $searchModel::getRobotsTypeList();

?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="stats robots-index">
    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            'id',
            'name',
            'regexp',
            [
                'attribute' => 'type',
                'format' => 'html',
                'filter' => true,
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function ($data) use ($robotsTypes) {
                    return $robotsTypes[$data->type];
                },
            ],
            [
                'attribute' => 'is_badbot',
                'format' => 'html',
                'filter' => true,
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function ($data) {
                    if ($data->is_badbot) {
                        return '<span class="label label-danger">Yes</span>';
                    } else {
                        return '<span class="label label-success">No</span>';
                    }
                },
            ],
            [
                'class' => \yii\grid\ActionColumn::className(),
                'buttons'=> [
                    'view' => function() {
                        return false;
                    },
                    'update' => function($url, $data, $key) use ($module) {
                        $url = Yii::$app->getUrlManager()->createUrl([$module->routePrefix . '/stats/robots/update', 'id' => $data['id']]);
                        return Html::a('<span class="glyphicon glyphicon-edit"></span>', $url, [
                            'class' => 'robots-update-link',
                            'title' => Yii::t('yii', 'Update'),
                            'data-toggle' => 'modal',
                            'data-target' => '#robotsUpdate',
                            'data-id' => $key,
                            'data-pjax' => '1'
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
    <div>
        <?= Html::a(
                Yii::t('app/modules/stats', 'Add new robot'),
                Yii::$app->getUrlManager()->createUrl([$module->routePrefix . '/stats/robots/update', 'id' => 0]),
                [
                    'class' => 'btn btn-success pull-right robots-update-link',
                    'title' => Yii::t('yii', 'Add new'),
                    'data-toggle' => 'modal',
                    'data-target' => '#robotsUpdate',
                    'data-pjax' => '1'
                ]
            );
        ?>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php $this->registerJs(<<< JS
$('body').delegate('.robots-update-link', 'click', function(event) {
    event.preventDefault();
    $('#robotsUpdate').remove();
    $.get(
        $(this).attr('href'),
        function (data) {
            $('body').append(data);
            $('#robotsUpdate').modal();
        }  
    );
});
JS
); ?>

<?php echo $this->render('../_debug'); ?>