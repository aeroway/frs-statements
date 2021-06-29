<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustVedSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="vedjust-ved-search-ref-num">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'search_ref_num', [
            'template' => '<div class="input-group col-xs-5">{input}<span class="input-group-btn">' .
                '<div id="vedjustvedsearch-strict-search-ref-num" role="radiogroup">
                    <span data-toggle="buttons">
                        <label title="Поиск похожей записи" class="btn btn-default' . (!$model->isStrictSearchRefNum ? ' active' : '') . '">
                            <input type="radio" name="VedjustVedSearch[isStrictSearchRefNum]" value="0">Прим.</label>
                        <label title="Поиск по строгому соответствию" class="btn btn-default' . ($model->isStrictSearchRefNum ? ' active' : '') . '">
                            <input type="radio" name="VedjustVedSearch[isStrictSearchRefNum]" value="1"' . ($model->isStrictSearchRefNum ? ' checked' : '') . '>Точный</label>
                    </span>'
                    . Html::submitButton('<span class="glyphicon glyphicon-search"></span>', ['class' => 'btn btn-info', 'title' => 'Поиск']) .
                '</div>
            </span></div>',
        ])->textInput(['placeholder' => 'ТОЛЬКО по колонке № обращения']); 
    ?>

    <?php ActiveForm::end(); ?>

</div>
