<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustVed */

$this->title = 'Экстерриториальные документы';
$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
.dummy {
    margin-top: 60%;
}
.thumbnail {
    position: absolute;
    top: 15px;
    bottom: 0;
    left: 15px;
    right: 15px;
    text-align:center;
    padding-top:calc(25% - 50px);
    overflow: hidden;
}
a.thumbnail {
    text-decoration: none;
}
</style>
<div class="vedjust-ved-index-ext-doc">

    <h2><?php //echo Html::encode("") ?></h2>

    <?php
    $associativeArea = [];

    foreach ($modelExtDoc->getExtDocs() as $value) {
        $associativeArea[$value["loc"]][$value["unit"]] = $value["ct"];
    }
    ?>

    <div class="row">
        <?php foreach ($associativeArea as $k1 => $v1): ?>
        <div class="col-md-3 col-sm-4 col-xs-6">
            <div class="dummy"></div>
            <a href="index.php?r=vedjust-ved/index-ext-doc-detailed&loc=<?= $k1 ?>" class="thumbnail purple"><b><?= $k1; ?></b><br><?php foreach ($v1 as $k2 => $v2): ?> <?= $v2 . ' ' . $k2 . '<br>'; ?><?php endforeach; ?></a>
        </div>
        <?php endforeach; ?>
    </div>

</div>
