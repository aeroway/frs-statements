<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use frontend\models\VedjustVed;
use frontend\models\VedjustArchive;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustStorage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vedjust-storage-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if(strpos(Yii::$app->request->get("r"), 'create')) {
        if (!empty(Yii::$app->request->get('ved'))) {
            echo $form->field($model, 'ved_id')->hiddenInput(['value' => Yii::$app->request->get('ved')])->label(false);
        } else {
            echo $form->field($model, 'ved_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(VedjustVed::find()->orderBy(['id' => SORT_ASC])->all(), 'id', 'id'),
                    'language' => 'ru',
                    'options' => ['placeholder' => 'Выберите номер ведомости'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
        }

        echo $form->field($model, 'archive_id')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(VedjustArchive::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                'language' => 'ru',
                'options' => ['placeholder' => 'Выберите архивохранилище'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    }
    ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => '3', 'placeholder' => 'Например: Зал: 1; Стеллаж 2; Шкаф 3; Полка 4; Позиция 5;']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
