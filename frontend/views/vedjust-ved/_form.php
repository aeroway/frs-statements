<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use frontend\models\VedjustStatus;
use frontend\models\VedjustArchiveUnit;
use frontend\models\User;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustVed */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="vedjust-ved-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if(strpos(Yii::$app->request->get("r"), 'create'))
    {
        echo $form->field($model, 'date_create')->hiddenInput(['value' => date('Y-m-d H:i:s')])->label(false);
        echo $form->field($model, 'status_id')->hiddenInput(['value' => 1])->label(false);
        echo $form->field($model, 'user_created_id')->hiddenInput(['value' => Yii::$app->user->identity->id])->label(false);
        echo $form->field($model, 'create_ip')->hiddenInput(['value' => ip2long(Yii::$app->request->userIP)])->label(false);
        $model->isNewRecord == 1 ? $model->target = 3 : $model->target;
        echo $form->field($model, 'target')->inline(false)->radioList(['1' => 'МФЦ', '2' => 'ЗКП', '3' => 'Росреестр']);
        echo $form->field($model, 'archive_unit_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(VedjustArchiveUnit::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите единицу архивного хранения',
                'value' => 1,
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        echo $form->field($model, 'ext_reg')->checkbox();
    }
    else
    {
        echo $form->field($model, 'status_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(VedjustStatus::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите Состояние',
                'value' => 3,
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
