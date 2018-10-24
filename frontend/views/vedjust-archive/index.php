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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            [
                'attribute' => 'user_created_id',
                'value' => 'userCreated.username',
            ],
            [
                'attribute' => 'agency_id',
                'value' => 'agency.name',
            ],
            [
                'attribute' => 'subject_id',
                'value' => 'subject.name',
            ],
            'municipality',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php //Pjax::end(); ?>
</div>
