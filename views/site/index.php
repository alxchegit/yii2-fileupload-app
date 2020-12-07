<?php

/* @var $this yii\web\View */
/* @var $model \app\models\LoginForm */

use yii\helpers\Html;

$this->title = Yii::$app->name;
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Приветствуем на <br>"<?= Yii::$app->name ?>"!</h1>
        
    </div>
    <?php if (Yii::$app->user->isGuest) { ?>
            <p class="lead">Чтобы начать пользоваться нашими супер-пупер мега новыми фичами, Вам необходимо сначала
                зарегистрироваться или войти под своим логином</p>

            <div class="index-login_form">
                <?= $this->render('_index_login_form', [
                    'model' => $model,
                ]) ?>
            </div>
        <?php } else { ?>
            <div class="body-content">
                <div class="row">
                    <p> <?= Html::a('Перейти к файлам', 'files/index', ['class' => 'btn btn-lg btn-success col-lg-3 col-lg-offset-5']) ?></p>
                </div>
            </div>
        <?php } ?>
</div>
