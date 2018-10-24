<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VedjustStorageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['vedjust-ved/index']];
$this->title = 'Архивохранилище';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-storage-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //echo Html::a('Создать', ['create'], ['class' => 'btn btn-success']); ?>
        <?= Html::a('Справочник архивохранилища', Url::to('index.php?r=vedjust-archive/index'), ['class' => 'btn btn-info']); ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'hall',
            'rack',
            'locker',
            'shelf',
            'position',
            'ved_id',
            [
                'attribute' => 'archive_id',
                'value' => 'archive.name',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
