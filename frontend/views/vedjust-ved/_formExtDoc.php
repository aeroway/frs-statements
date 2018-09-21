<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use frontend\models\VedjustArea;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustVed */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="vedjust-ved-ext-doc-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'area_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(VedjustArea::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
        'language' => 'ru',
        'options' => ['placeholder' => 'Выберите район', 'value' => 1],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
