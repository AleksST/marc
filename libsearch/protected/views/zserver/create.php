<?php
/* @var $this ZserverController */
/* @var $model Zserver */

$this->breadcrumbs=array(
	'Zservers'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Zserver', 'url'=>array('index')),
	array('label'=>'Manage Zserver', 'url'=>array('admin')),
);
?>

<h1>Create Zserver</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>