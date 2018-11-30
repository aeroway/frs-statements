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

    <?php
    if(strpos(Yii::$app->request->get("r"), 'create'))
    {
        echo $form->field($model, 'date_create')->hiddenInput(['value' => date('Y-m-d H:i:s')])->label(false);
        echo $form->field($model, 'user_created_id')->hiddenInput(['value' => Yii::$app->user->identity->id])->label(false);
        echo $form->field($model, 'create_ip')->hiddenInput(['value' => ip2long(Yii::$app->request->userIP)])->label(false);
    }
    ?>

    <?php if(strpos(Yii::$app->request->get("r"), 'update')) : ?>
        <?php if ($model->ved->status_id === 3 && $model->user_accepted_id === Yii::$app->user->identity->id) : ?>
        <?php else : ?>
            <?= $form->field($model, 'kuvd')->textInput(['autofocus' => 'autofocus']); ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if(strpos(Yii::$app->request->get("r"), 'create')) : ?>
        <?= $form->field($model, 'kuvd')->textInput(['autofocus' => 'autofocus']) ?>
        <?= $form->field($model, 'ref_num')->textInput(['autofocus' => 'autofocus']) ?>
    <?php endif; ?>

    <?= $form->field($model, 'comment')->textArea() ?>

    <?php
    if(strpos(Yii::$app->request->get("r"), 'create')) {
        if (!empty(Yii::$app->request->get('id'))) {
            echo $form->field($model, 'ved_id')->hiddenInput(['value' => Yii::$app->request->get('id')])->label(false);
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
        echo $form->field($model, 'p_count')->textInput(['type' => 'number', 'min' => '1', 'step' => '1', 'value' => 1]);
    } else {
        echo $form->field($model, 'ved_id')->hiddenInput(['value' => $model->ved_id])->label(false);
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
