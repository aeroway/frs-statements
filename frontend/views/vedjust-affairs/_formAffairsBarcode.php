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

<div class="vedjust-affairs-form-affairs-barcode">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'barcode')->textInput(['onChange' => 'changeBarcode();', 'autofocus' => 'autofocus', 'style'=>'width: 300px;']) ?>

    <?= $form->field($model, 'ved_id')->hiddenInput(['value' => $vedId])->label(false) ?>

    <div class="form-group">
        <?= Html::a('Назад', ['index', 'id' => $vedId], ['class' => 'btn btn-info']); ?>
        <?= Html::submitButton('Проверить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
function changeBarcode() {
    document.getElementById("vedjustaffairs-barcode").value = document.getElementById("vedjustaffairs-barcode").value.replace(/\./g, "/");
    document.getElementById("vedjustaffairs-barcode").value = document.getElementById("vedjustaffairs-barcode").value.replace(/ЬАС/ig, "MFC");
}
</script>