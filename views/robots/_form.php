<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\bootstrap\Modal;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */

?>
<?php $form = ActiveForm::begin([
    'id' => 'update-form',
    'enableAjaxValidation' => false
]); ?>
<?php Modal::begin([
    'id' => 'robotsUpdate',
    'header' => '<h4 class="modal-title">'.Yii::t('app/modules/stats', 'Add (or update)').'</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">'.Yii::t('app/modules/stats', 'Close').'</a>' . Html::submitButton(Yii::t('app/modules/stats', 'Save'), ['class' => 'btn btn-success pull-right']),
    'clientOptions' => [
        'show' => false
    ]
]); ?>
<?= $form->field($model, 'name')->textInput() ?>
<?= $form->field($model, 'regexp')->textInput() ?>
<?= $form->field($model, 'type')->widget(SelectInput::class, [
    'items' => $model::getRobotsTypeList(),
    'options' => [
        'class' => 'form-control'
    ]
]); ?>
<?= $form->field($model, 'hosts')->textarea()->hint(Yii::t('app/modules/stats', 'Each value from a new line')) ?>
<?= $form->field($model, 'is_badbot')->checkbox()?>
<?php Modal::end(); ?>
<?php ActiveForm::end(); ?>