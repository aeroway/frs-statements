<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustAffairsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vedjust-affairs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'date_create') ?>

    <?= $form->field($model, 'date_status') ?>

    <?= $form->field($model, 'comment') ?>

    <?php // echo $form->field($model, 'kuvd') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
