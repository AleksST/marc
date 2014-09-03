<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="language" content="ru">

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?= Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection">
	<link rel="stylesheet" type="text/css" href="<?= Yii::app()->request->baseUrl; ?>/css/print.css" media="print">
	<link rel="stylesheet" type="text/css" href="<?= Yii::app()->request->baseUrl; ?>/css/main.css">
	<script type="text/javascript" src="<?= Yii::app()->request->baseUrl; ?>/js/angular.min.js"></script>

	<title><?= CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

	<div id="header">
		<div id="logo"><?= 'test'; ?></div>
	</div><!-- header -->

    <div class="content">
	    <?= $content; ?>
    </div>

	<div class="clear"></div>

	<div id="footer">
		Copyright &copy; <?= date('Y'); ?> by Alex Tushin.<br/>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>