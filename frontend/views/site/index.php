<?php

/* @var $this yii\web\View */

$this->title = 'Ведомости';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Ведомости</h1>

        <p class="lead">Начните обрабатывать ведомости правоустанавливающих документов.</p>

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
            <!--
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/extensions/">Yii Extensions &raquo;</a></p>
            </div>
            -->
        </div>
    </div>

</div>
