<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use frontend\models\VedjustVed;

/* @var $this yii\web\View */
/* @var $modelIssuance frontend\models\VedjustIssuance */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vedjust-affairs-form">

    <?= '<p>Выдано (' . $numIssuance . '/' . $p_count . ')</p>'; ?>
    <?php foreach ($nameIssuance as $key => $value) : ?>
        <?= $key + 1 . '. ' . $value["name"] . '<br>'; ?>
    <?php endforeach; ?>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($modelIssuance, 'date_issue')->hiddenInput(['value' => date('Y-m-d H:i:s')])->label(false); ?>
    <?= $form->field($modelIssuance, 'user_created_id')->hiddenInput(['value' => Yii::$app->user->identity->id])->label(false); ?>
    <?= $form->field($modelIssuance, 'create_ip')->hiddenInput(['value' => ip2long(Yii::$app->request->userIP)])->label(false); ?>
    <?= $form->field($modelIssuance, 'affairs_id')->hiddenInput(['value' => $idVed])->label(false); ?>
    <?= $form->field($modelIssuance, 'name')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($modelIssuance->isNewRecord ? 'Выдать' : 'Сохранить', ['class' => $modelIssuance->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
