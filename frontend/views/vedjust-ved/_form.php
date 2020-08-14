<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;
use kartik\select2\Select2;
use frontend\models\VedjustStatus;
use frontend\models\VedjustAgency;
use frontend\models\VedjustArchiveUnit;
use frontend\models\VedjustSubdivision;
use frontend\models\VedjustAddress;
use frontend\models\VedjustArea;
use frontend\models\User;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustVed */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="vedjust-ved-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'style' => 'width: 500px;']]); ?>

    <?php if (strpos(Yii::$app->request->pathInfo, 'create')) : ?>
        <?= $form->field($model, 'status_id')
            ->hiddenInput(['value' => 1])
            ->label(false);
        ?>
        <?= $form->field($model, 'date_create')
            ->hiddenInput(['value' => date('Y-m-d H:i:s')])
            ->label(false);
        ?>
        <?= $form->field($model, 'user_created_id')
            ->hiddenInput(['value' => Yii::$app->user->identity->id])
            ->label(false);
        ?>
        <?= $form->field($model, 'create_ip')
            ->hiddenInput(['value' => ip2long(Yii::$app->request->userIP)])
            ->label(false);
        ?>
    <?php endif; ?>

    <?= $form->field($model, 'comment')->textArea(); ?>

    <?= $form->field($model, 'target')
        ->inline(false)
        ->radioList(ArrayHelper::map(VedjustAgency::find()
            ->orderBy(['name' => SORT_ASC])
            ->all()
            , 'id', 'name')
        , ['onchange' => "changeVals()"]);
    ?>

    <?php
    $vedSubdivision = empty($model->subdivision_id) ? '' : 
        ArrayHelper::map(VedjustSubdivision::find()
            ->where(['agency_id' => $model->target])
            ->orderBy(['name' => SORT_ASC])
            ->all()
        , 'id', 'name');
    ?>

    <?= $form->field($model, 'subdivision_id')->widget(Select2::classname(), [
            'data' => $vedSubdivision,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите отдел',
                'onchange' => 'fillAddress();',
            ],
            'pluginOptions' => [
                'allowClear' => false,
                'tags' => false,
            ],
        ]);
    ?>

    <?php
    $vedAddresses = empty($model->address_id) ? '' :
        ArrayHelper::map(VedjustAddress::find()
            ->where(['subdivision_id' => $model->subdivision_id])
            ->orderBy(['name' => SORT_ASC])
            ->all()
        , 'id', 'name');
    ?>

    <?= $form->field($model, 'address_id')->widget(Select2::classname(), [
            'data' => $vedAddresses,
            'language' => 'ru',
            'options' => ['placeholder' => 'Выберите адрес', 'onchange' => 'fillArea(this.value)'],
            'pluginOptions' => [
                'allowClear' => false,
                'tags' => false,
            ],
        ]);
    ?>

    <?php
    $vedArea = empty($model->area_id) ? '' :
        ArrayHelper::map(VedjustArea::find()
            ->orderBy(['name' => SORT_ASC])
            ->all()
        , 'id', 'name');
    ?>

    <?= $form->field($model, 'area_id')->widget(Select2::classname(), [
            'data' => $vedArea,
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите район',
            ],
            'pluginOptions' => [
                'allowClear' => false,
                'tags' => false,
            ],
        ]);
    ?>

    <?= $form->field($model, 'archive_unit_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(VedjustArchiveUnit::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Выберите единицу архивного хранения',
                'value' => 4,
                'onchange' => 'changeExtReg(this.value);',
            ],
            'pluginOptions' => [
                'allowClear' => false,
            ],
        ]);
    ?>

    <?= $form->field($model, 'ext_reg')->checkbox(); ?>

    <?php if (empty($copy)): ?>
        <?php // echo $form->field($model, 'file')->fileInput(); ?>
        <?= $form->field($model, 'pkpvd_xlsx')->fileInput(); ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
/* Районы для Росреестра */
// $(".field-vedjustved-area_id").hide();

function changeVals() {
    $.ajax({
        type: 'GET',
        url: '/site/municipality',
        data: 'subject_id=1' + '&agency_id=' + $("input[name='VedjustVed[target]']:checked").val(),
        success: function(data) {
            if (data == 0) {
                //alert('Данные отсутствуют.');
                $("#vedjustved-subdivision_id").empty();
                $("#vedjustved-subdivision_id").append( $('<option value="">Нет данных</option>'));
            } else {
                //alert('Данные получены.');
                $("#vedjustved-subdivision_id").empty();
                $("#vedjustved-subdivision_id").append("<option disabled selected>Выберите отдел</option>");
                $("#vedjustved-subdivision_id").append($(data));
            }
        }
    });
}

function fillAddress() {
    $.ajax({
        type: 'GET',
        url: '/site/address',
        data: 'subdivision_id=' + $("select[name='VedjustVed[subdivision_id]']").val(),
        success: function(data) {
            if (data == 0) {
                //alert('Данные отсутствуют.');
                $("#vedjustved-address_id").empty();
                $("#vedjustved-address_id").append( $('<option value="">Нет данных</option>'));
            } else {
                //alert('Данные получены.');
                $("#vedjustved-address_id").empty();
                $("#vedjustved-address_id").append("<option disabled selected>Выберите адрес</option>");
                $("#vedjustved-address_id").append($(data));
            }
        }
    });
}

function fillArea(value) {
    if (value == 393) {
        $(".field-vedjustved-area_id").show();
        $.ajax({
            type: 'GET',
            url: '/vedjust-ved/fill-area',
            //data: 'subdivision_id=' + $("select[name='VedjustVed[subdivision_id]']").val(),
            success: function(data) {
                if (data == 0) {
                    //alert('Данные отсутствуют.');
                    $("#vedjustved-area_id").empty();
                    $("#vedjustved-area_id").append( $('<option value="">Нет данных</option>'));
                } else {
                    //alert('Данные получены.');
                    $("#vedjustved-area_id").empty();
                    $("#vedjustved-area_id").append("<option disabled selected>Выберите район</option>");
                    $("#vedjustved-area_id").append($(data));
                }
            }
        });
    } else {
        $(".field-vedjustved-area_id").hide();
        $("#vedjustved-area_id").empty();
    }
}

function changeExtReg(value) {
    if (value == 4) {
        document.getElementById('vedjustved-ext_reg').disabled = true;
    } else {
        document.getElementById('vedjustved-ext_reg').disabled = false;
    }
}
</script>
