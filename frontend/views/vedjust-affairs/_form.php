<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use frontend\models\VedjustVed;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustAffairs */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vedjust-affairs-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if(strpos(Yii::$app->request->pathInfo, 'update')) : ?>
        <?php if ($model->ved->status_id === 1 && $model->user_created_id === Yii::$app->user->identity->id) : ?>
            <?= $form->field($model, 'ref_num')->textInput(['autofocus' => 'autofocus']); ?>
            <?= $form->field($model, 'kuvd')->textInput(); ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if(strpos(Yii::$app->request->pathInfo, 'create')) : ?>
        <?= $form->field($model, 'date_create')->hiddenInput(['value' => date('Y-m-d H:i:s')])->label(false); ?>
        <?= $form->field($model, 'user_created_id')->hiddenInput(['value' => Yii::$app->user->identity->id])->label(false); ?>
        <?= $form->field($model, 'create_ip')->hiddenInput(['value' => ip2long(Yii::$app->request->userIP)])->label(false); ?>
    <?php endif; ?>

    <?php if(strpos(Yii::$app->request->pathInfo, 'create')) : ?>
        <?= $form->field($model, 'ref_num')->textInput(['autofocus' => 'autofocus', 'onChange' => 'changeRefnumValue();', 'placeholder' => 'MFC-XXXX/' . date("Y") . '-XXXXX']) ?>
        <?= $form->field($model, 'kuvd')->textInput(['onChange' => 'changeKuvdValue();', 'placeholder' => 'КУВД-XXX/' . date("Y") . '-XXXX ИЛИ 23/XXX/XXX/XXX/' . date("Y") . '-XXXX']) ?>
    <?php endif; ?>

    <?= $form->field($model, 'comment')->textArea(['placeholder' => 'Комментарий или номер пакета'])->label(false); ?>

    <?php
    if(strpos(Yii::$app->request->pathInfo, 'create')) {
        if (!empty($vedId)) {
            echo $form->field($model, 'ved_id')->hiddenInput(['value' => $vedId])->label(false);
        } else {
            echo $form->field($model, 'ved_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(VedjustVed::find()->orderBy(['num_ved' => SORT_ASC])->all(), 'id', 'num_ved'),
                    'language' => 'ru',
                    'options' => ['placeholder' => 'Выберите номер ведомости'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
        }
    } else {
        echo $form->field($model, 'ved_id')->hiddenInput(['value' => $model->ved_id])->label(false);
    }
    ?>

    <div class="form-group">
        <?= Html::a('Назад', ['index', 'id' => $vedId], ['class' => 'btn btn-info']); ?>
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
function changeKuvdValue() {
    document.getElementById("vedjustaffairs-kuvd").value = document.getElementById("vedjustaffairs-kuvd").value.replace(/\./g, "/");
	document.getElementById("vedjustaffairs-kuvd").value = document.getElementById("vedjustaffairs-kuvd").value.replace(/\|/g, "/");
}

function changeRefnumValue() {
    document.getElementById("vedjustaffairs-ref_num").value = document.getElementById("vedjustaffairs-ref_num").value.replace(/\./g, "/");
    document.getElementById("vedjustaffairs-ref_num").value = document.getElementById("vedjustaffairs-ref_num").value.replace(/ЬАС/ig, "MFC");
}
</script>