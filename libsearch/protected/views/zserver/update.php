<?php
/* @var $this ZserverController */
/* @var $model Zserver */

$this->breadcrumbs=array(
	'Zservers'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Zserver', 'url'=>array('index')),
	array('label'=>'Create Zserver', 'url'=>array('create')),
	array('label'=>'View Zserver', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Zserver', 'url'=>array('admin')),
);
?>

<h1>Update Zserver <?= $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>
