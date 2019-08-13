<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use frontend\models\VedjustSubdivision;
use frontend\models\AuthAssignment;
use frontend\models\AuthItem;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model frontend\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'auth_key')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'password_hash')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'password_reset_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'updated_at')->hiddenInput(['value' => strtotime(date('Y-m-d'))])->label(false); ?>

    <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'subdivision_id')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(VedjustSubdivision::find()->orderBy(['name' => SORT_ASC])->where(['agency_id' => $model->agency_id])->all(), 'id', 'name'),
        'language' => 'ru',
        'options' => ['placeholder' => 'Выберите подразделение'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]);
    ?>

    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => 4, // the maximum times, an element can be added (default 999)
        'min' => 0, // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => $modelsAuthAssignment[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'item_name',
        ],
    ]); ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>
                <i class="glyphicon glyphicon-envelope"></i> Роли
                <button type="button" class="add-item btn btn-success btn-sm pull-right"><i class="glyphicon glyphicon-plus"></i> Add</button>
            </h4>
        </div>
        <div class="panel-body">
            <div class="container-items"><!-- widgetBody -->
            <?php foreach ($modelsAuthAssignment as $i => $modelAuthAssignment): ?>
                <div class="item panel panel-default"><!-- widgetItem -->
                    <div class="panel-heading">
                        <h3 class="panel-title pull-left">Роль</h3>
                        <div class="pull-right">
                            <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <?php
                            // necessary for update action.
                            if (! $modelAuthAssignment->isNewRecord) {
                                echo Html::activeHiddenInput($modelAuthAssignment, "[{$i}]id");
                            }
                        ?>
                        <?= $form->field($modelAuthAssignment, "[{$i}]item_name")->dropDownList(
                            ArrayHelper::map(AuthItem::find()->orderBy(['description' => SORT_ASC])->all(), 'name', 'description'));
                        ?>
                        <div class="row">
                            <div class="col-sm-6">
                                
                            </div>
                        </div><!-- .row -->
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div><!-- .panel -->
    <?php DynamicFormWidget::end(); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>