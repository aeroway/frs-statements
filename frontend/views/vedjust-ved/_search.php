<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustVedSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="vedjust-ved-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php /* echo $form->field($model, 'ref_num_affairs', [
            'template' => '<div class="input-group col-xs-4">{input}<span class="input-group-btn"></span></div>',
        ])->textInput(['placeholder' => '№ обращения']); */ 
    ?>

    <?php /* echo $form->field($model, 'kuvd_affairs', [
            'template' => '<div class="input-group col-xs-4">{input}<span class="input-group-btn">' .
            Html::submitButton('Поиск', ['class' => 'btn btn-default']) . '</span></div>',
        ])->textInput(['placeholder' => 'КУВД']); */ 
    ?>

    <?= $form->field($model, 'search_ref_num_kuvd_comment', [
            'template' => '<div class="input-group col-xs-5">{input}<span class="input-group-btn">' .
                '<div id="vedjustvedsearch-strict" role="radiogroup">
                    <span data-toggle="buttons">
                        <label title="Поиск похожей записи" class="btn btn-default' . (!$model->strict ? ' active' : '') . '">
                            <input type="radio" name="VedjustVedSearch[strict]" value="0">Прим.</label>
                        <label title="Поиск по строгому соответствию" class="btn btn-default' . ($model->strict ? ' active' : '') . '">
                            <input type="radio" name="VedjustVedSearch[strict]" value="1"' . ($model->strict ? ' checked' : '') . '>Точный</label>
                    </span>'
                    . Html::submitButton('<span class="glyphicon glyphicon-search"></span>', ['class' => 'btn btn-info', 'title' => 'Поиск']) .
                '</div>
            </span></div>',
        ])->textInput(['placeholder' => '№ обращения, КУВД, комментарий ведомости или дела']); 
    ?>

    <?php // echo $form->field($model, 'date_create') ?>

    <?php // echo $form->field($model, 'num_ved') ?>

    <?php // $form->field($model, 'status_id') ?>

    <?php // $form->field($model, 'date_reception') ?>

    <?php // echo $form->field($model, 'date_formed') ?>

    <?php // echo $form->field($model, 'user_created_id') ?>

    <?php // echo $form->field($model, 'user_accepted_id') ?>

    <div class="form-group">
        <?php // Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php // Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
