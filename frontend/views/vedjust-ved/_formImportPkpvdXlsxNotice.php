<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustVed */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="vedjust-ved-form-import-pkpvd-xlsx-notice">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'style' => 'width: 500px;']]); ?>
        <?= $form->field($model, 'pkpvd_xlsx_notice')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Имортировать', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>