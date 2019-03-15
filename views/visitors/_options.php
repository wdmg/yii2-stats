<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model wdmg\stats\models\VisitorsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h5 class="panel-title">
            <a data-toggle="collapse" href="#statsSearch">
                <span class="glyphicon glyphicon-search"></span> <?= Yii::t('app/modules/stats', 'View options') ?>
            </a>
        </h5>
    </div>
    <div id="statsSearch" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="stats-search">

                <?php $form = ActiveForm::begin([
                    'action' => Yii::$app->request->getUrl(),
                    'method' => 'get',
                    'options' => [
                        'data-pjax' => 1
                    ],
                ]); ?>

                <?= $form->field($model, 'viewChart')->checkbox()->label(''); ?>

                <?= $form->field($model, 'viewRobots')->checkbox()->label(''); ?>
                <?= $form->field($model, 'viewOnlyRobots')->checkbox()->label(''); ?>

                <?= $form->field($model, 'viewReferrerURI')->checkbox()->label(''); ?>
                <?= $form->field($model, 'viewReferrerHost')->checkbox()->label(''); ?>

                <?= $form->field($model, 'viewClientIP')->checkbox()->label(''); ?>
                <?= $form->field($model, 'viewClientOS')->checkbox()->label(''); ?>

                <?= $form->field($model, 'viewTransitionType')->checkbox()->label(''); ?>
                <?= $form->field($model, 'viewAuthUser')->checkbox()->label(''); ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app/modules/stats', 'Apply'), ['name' => 'viewOptions', 'value' => '1', 'class' => 'btn btn-primary']) ?>
                    <?= Html::resetButton(Yii::t('app/modules/stats', 'Reset'), ['class' => 'btn btn-default']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
