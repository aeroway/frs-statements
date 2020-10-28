<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustVedSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="ved-affairs-search-comment">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'search_comment_ved_affairs', [
            'template' => '<div class="input-group col-xs-5">{input}<span class="input-group-btn">' .
                '<div id="vedjustvedsearch-strict-search-comment-ved-affairs" role="radiogroup">
                    <span data-toggle="buttons">
                        <label title="Поиск похожей записи" class="btn btn-default' . (!$model->isStrictSearchCommentVedAffairs ? ' active' : '') . '">
                            <input type="radio" name="VedjustVedSearch[isStrictSearchCommentVedAffairs]" value="0">Прим.</label>
                        <label title="Поиск по строгому соответствию" class="btn btn-default' . ($model->isStrictSearchCommentVedAffairs ? ' active' : '') . '">
                            <input type="radio" name="VedjustVedSearch[isStrictSearchCommentVedAffairs]" value="1"' . ($model->isStrictSearchCommentVedAffairs ? ' checked' : '') . '>Точный</label>
                    </span>'
                    . Html::submitButton('<span class="glyphicon glyphicon-search"></span>', ['class' => 'btn btn-info', 'title' => 'Поиск']) .
                '</div>
            </span></div>',
        ])->textInput(['placeholder' => 'Комментарий ведомости или дела']);
    ?>

    <?php ActiveForm::end(); ?>

</div>
