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
                'options' => ['placeholder' => 'Выберите архивохранилище', 'onchange' => 'allowFields(this.value)'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    }
    ?>

    <?php if (strpos(Yii::$app->request->get("r"), 'update')): ?>
        <?= $form->field($model, 'hall')->textInput(['type' => 'number', 'min' => 1, 'max' => $model->archive->hall_max]) ?>
        <?= $form->field($model, 'rack')->textInput(['type' => 'number', 'min' => 1, 'max' => $model->archive->rack_max]) ?>
        <?= $form->field($model, 'locker')->textInput(['type' => 'number', 'min' => 1, 'max' => $model->archive->locker_max]) ?>
        <?= $form->field($model, 'shelf')->textInput(['type' => 'number', 'min' => 1, 'max' => $model->archive->shelf_max]) ?>
        <?= $form->field($model, 'position')->textInput(['type' => 'number', 'min' => 1, 'max' => $model->archive->position_max]) ?>
    <?php endif; ?>

    <?php if (strpos(Yii::$app->request->get("r"), 'create')): ?>
        <?= $form->field($model, 'hall')->textInput(['type' => 'number', 'min' => 1, 'value' => '', 'readOnly' => true]) ?>
        <?= $form->field($model, 'rack')->textInput(['type' => 'number', 'min' => 1, 'value' => '', 'readOnly' => true]) ?>
        <?= $form->field($model, 'locker')->textInput(['type' => 'number', 'min' => 1, 'value' => '', 'readOnly' => true]) ?>
        <?= $form->field($model, 'shelf')->textInput(['type' => 'number', 'min' => 1, 'value' => '', 'readOnly' => true]) ?>
        <?= $form->field($model, 'position')->textInput(['type' => 'number', 'min' => 1, 'value' => '', 'readOnly' => true]) ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
document.getElementById('vedjuststorage-archive_id').value = '';

function allowFields(value) {
    if(value) {
        setArchiveValues('hall_max', 'vedjuststorage-hall');
        setArchiveValues('rack_max', 'vedjuststorage-rack');
        setArchiveValues('locker_max', 'vedjuststorage-locker');
        setArchiveValues('shelf_max', 'vedjuststorage-shelf');
        setArchiveValues('position_max', 'vedjuststorage-position');

        setArchiveInitValues('hall', 'vedjuststorage-hall');
        setArchiveInitValues('rack', 'vedjuststorage-rack');
        setArchiveInitValues('locker', 'vedjuststorage-locker');
        setArchiveInitValues('shelf', 'vedjuststorage-shelf');
        setArchiveInitValues('position', 'vedjuststorage-position');
    }
}

function setArchiveValues(name, attr) {
    $.ajax({
        type: 'GET',
        url: 'index.php?r=vedjust-archive/max-size-archive',
        data: 'id=' + document.getElementById('vedjuststorage-archive_id').value + '&name=' + name,
        success: function(data) {
            if (data) {
                document.getElementById(attr).readOnly = false;
                document.getElementById(attr).max = data;
            }
        }
    });
}

function setArchiveInitValues(name, attr) {
    $.ajax({
        type: 'GET',
        url: 'index.php?r=vedjust-storage/last-value-archive',
        data: 'id=' + document.getElementById('vedjuststorage-archive_id').value + '&name=' + name,
        success: function(data) {
            if (data) {
                document.getElementById(attr).readOnly = false;
                document.getElementById(attr).value = data;
            }
        }
    });
}

</script>