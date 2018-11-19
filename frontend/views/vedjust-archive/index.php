<?php

use yii\helpers\Html;
use yii\grid\GridView;
//use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VedjustArchiveSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['vedjust-ved/index']];
//$this->params['breadcrumbs'][] = ['label' => 'Архивохранилище', 'url' => ['vedjust-storage/index']];
$this->title = 'Справочник архивохранилища';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="vedjust-archive-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать запись', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    $buttons =
    [
        'class' => 'yii\grid\ActionColumn',
        'buttons' =>
        [
            'update' => function($url, $model, $key)
            {
                // редактировать может тот, кто принял
                if ($model->subdivision_id === Yii::$app->user->identity->subdivision_id)
                {
                    $customurl = Yii::$app->getUrlManager()->createUrl(['vedjust-archive/update','id' => $model['id']]);

                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $customurl, 
                            [
                                'title' => Yii::t('yii', 'Update'),
                                'aria-label' => Yii::t('yii', 'Update'),
                                'data-pjax' => '0',
                            ]);
                }
            },
        ],
        'template' => '{view} {update}',
    ];
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            [
                'label' => 'e-mail',
                'value' => 'userCreated.username',
            ],
            [
                'attribute' => 'user_created_id',
                'value' => 'userCreated.full_name',
            ],
            [
                'attribute' => 'agency_id',
                'value' => 'agency.name',
            ],
            [
                'attribute' => 'subject_id',
                'value' => 'subject.name',
            ],
            [
                'attribute' => 'subdivision_id',
                'value' => 'subdivision.name',
            ],

            $buttons,
        ],
    ]); ?>
    <?php //Pjax::end(); ?>
</div>
