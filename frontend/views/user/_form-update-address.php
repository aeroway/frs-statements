<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use frontend\models\VedjustAddress;

/* @var $this yii\web\View */
/* @var $model frontend\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-update-address">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'style' => 'width: 500px;']]); ?>

    <?= $form->field($model, 'address_id')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(VedjustAddress::find()->where(['subdivision_id' => Yii::$app->user->identity->subdivision_id])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
            'language' => 'ru',
            'options' => ['placeholder' => 'Выберите адрес'],
            'pluginOptions' => [
                'allowClear' => false,
            ],
        ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
