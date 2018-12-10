<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use frontend\models\VedjustSubdivision;

/* @var $this yii\web\View */
/* @var $model frontend\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'auth_key')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'password_hash')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'password_reset_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?php //echo $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->hiddenInput(['value' => date('Y-m-d')])->label(false); ?>

    <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'subdivision_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(VedjustSubdivision::find()->orderBy(['name' => SORT_ASC])->where(['agency_id' => $model->agency_id])->all(), 'id', 'name'),
        'language' => 'ru',
        'options' => ['placeholder' => 'Выберите подразделение'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>