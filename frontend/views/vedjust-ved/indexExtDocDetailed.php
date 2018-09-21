<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use frontend\models\VedjustExtDoc;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VedjustExtDocSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['vedjust-ved/index']];
$this->params['breadcrumbs'][] = ['label' => 'Экстерриториальные документы', 'url' => ['vedjust-ved/view-ext-doc']];
$this->title = $loc;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-index-ext-doc-detailed">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Экспорт в PDF', ['vedjust-ved/create-ext-doc-pdf', 'loc' => !empty($loc) ? $loc : '',], ['class' => 'btn btn-info']) . ' '; ?>
        <?php if(!Yii::$app->user->can('addAudit')): ?>
            <?= Html::a('Подтвердить', ['vedjust-ved/ext-doc-accepted', 'loc' => !empty($loc) ? $loc : '',], ['class' => 'btn btn-success']) . ' '; ?>
        <?php endif; ?>
    </p>

    <?php
    if (Yii::$app->user->can('addAudit')) {
        $buttons =
        [
            'class' => 'yii\grid\ActionColumn',
            'buttons' =>
            [
                'view' => function ($url, $model, $key)
                {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                            ['vedjust-ved/view-ext-doc-detailed', 'id' => $model->id],
                            [
                                'title' => Yii::t('yii', 'View'),
                                'aria-label' => Yii::t('yii', 'View'),
                                'data-pjax' => '0',
                            ]);
                },
            ],
            'template' => '{view}',
        ];
    } else {
        $buttons = [];
    }
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'affairs.kuvd',
            'affairs.comment',
            'affairs.ved.archiveUnit.name',

            $buttons,
        ],
    ]); ?>
</div>

<script>

</script>
