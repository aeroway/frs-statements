<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustVedSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vedjust-ved-search-num-ved">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'search_num_ved', [
        'template' => '<div class="input-group col-xs-5">{input}<span class="input-group-btn">'
            . Html::submitButton(
                '<span class="glyphicon glyphicon-search"></span>'
                , ['class' => 'btn btn-info', 'title' => 'Поиск по строгому соответствию']
            )
            . '</span></div>',
    ])->textInput(['placeholder' => '№ ведомости']); ?>

    <div class="form-group">
        <?php // Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php // Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
