<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustStorageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vedjust-storage-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'hall') ?>

    <?= $form->field($model, 'rack') ?>

    <?= $form->field($model, 'locker') ?>

    <?= $form->field($model, 'shelf') ?>

    <?php // echo $form->field($model, 'position') ?>

    <?php // echo $form->field($model, 'ved_id') ?>

    <?php // echo $form->field($model, 'archive_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
