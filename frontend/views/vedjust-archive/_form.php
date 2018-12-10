<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\VedjustAgency;
use frontend\models\VedjustSubject;
use frontend\models\VedjustSubdivision;
use frontend\models\User;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustArchive */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vedjust-archive-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?php
    if(strpos(Yii::$app->request->get("r"), 'create'))
    {
        echo '<div class="form-group"><b>Пользователь</b>: ' . User::find()->where(['id' => Yii::$app->user->identity->id])->one()->username . '<div class="help-block"></div></div>';
        echo $form->field($model, 'user_created_id')->hiddenInput(['value' => Yii::$app->user->identity->id])->label(false);

        echo '<div class="form-group"><b>Орган</b>: ' . VedjustAgency::find()->where(['id' => Yii::$app->user->identity->agency_id])->one()->name . '<div class="help-block"></div></div>';
        echo $form->field($model, 'agency_id')->hiddenInput(['value' => Yii::$app->user->identity->agency_id])->label(false);

        echo '<div class="form-group"><b>Субъект РФ</b>: ' . VedjustSubject::find()->where(['id' => Yii::$app->user->identity->subject_id])->one()->name . '<div class="help-block"></div></div>';
        echo $form->field($model, 'subject_id')->hiddenInput(['value' => Yii::$app->user->identity->subject_id])->label(false);

        echo '<div class="form-group"><b>Отдел</b>: ' . VedjustSubdivision::find()->where(['id' => Yii::$app->user->identity->subdivision_id])->one()->name . '<div class="help-block"></div></div>';
        echo $form->field($model, 'subdivision_id')->hiddenInput(['value' => Yii::$app->user->identity->subdivision_id])->label(false);
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
