<?php

/* @var $this yii\web\View */

$this->title = 'Ведомости';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Ведомости</h1>

        <p class="lead">Начните обрабатывать ведомости правоустанавливающих документов.</p>
        <?php if(Yii::$app->session->hasFlash('deny')): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo Yii::$app->session->getFlash('deny'); ?>
        </div>
        <?php endif;?>

        <p><a class="btn btn-lg btn-success" href="/vedjust-ved/index">Приступить к обработке</a></p>
    </div>

    <div class="body-content">
        <div class="row">
            <div class="col-lg-4">
                <h4>Вы вошли как:</h4>
                <p class=""><?= !Yii::$app->user->isGuest ?  'ФИО: ' . Yii::$app->user->identity->full_name : '.'; ?></p>
                <p class=""><?= !Yii::$app->user->isGuest ?  'Должность: ' . Yii::$app->user->identity->position : '.'; ?></p>
                <p class=""><?= !Yii::$app->user->isGuest ?  'Субъект: ' . Yii::$app->user->identity->subject->name : '.'; ?></p>
                <p class=""><?= !Yii::$app->user->isGuest ?  'Орган: ' . Yii::$app->user->identity->agency->name : '.'; ?></p>
                <p class=""><?= !Yii::$app->user->isGuest ?  'Отдел: ' . Yii::$app->user->identity->subdivision->name : '.'; ?></p>
            </div>
            <div class="col-lg-4">
                <h4>Инструкция</h4>
                <p><a href="doc/instruction-ved.docx">Скачать руководство</a>.</p>
                <p>После регистрации необходимо отправить список пользователей с необходимыми полномочиями на почту, указанную в руководстве.</p>
            </div>
        </div>
    </div>

</div>
