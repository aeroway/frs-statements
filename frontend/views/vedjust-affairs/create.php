<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustAffairs */

$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['vedjust-ved/index']];
$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Дела', 'url' => ['index', 'id' => !empty(Yii::$app->request->get('id')) ? Yii::$app->request->get('id') : '',]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-affairs-create">

    <h1><?php //echo Html::encode($this->title) ?></h1>
    <div id="successAffairs" style="display: <?= Yii::$app->session->getFlash('successAffairs'); ?>;">
        <div class="alert alert-success">Запись добавлена.</div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'vedId' => $vedId,
    ]) ?>

</div>
<script type="text/javascript">
    setTimeout(function () {
            document.getElementById('successAffairs').style.display = 'none';
        }, 2000);
</script>