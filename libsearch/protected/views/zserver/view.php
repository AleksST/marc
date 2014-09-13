<?php
/* @var $this ZserverController */
/* @var $model Zserver */

$this->breadcrumbs = array(
	'Zservers' => array('index'),
	$model->name,
);

$this->menu = array(
	array('label' => 'List Zserver', 'url' => array('index')),
	array('label' => 'Create Zserver', 'url' => array('create')),
	array('label' => 'Update Zserver', 'url' => array('update', 'id' => $model->id)),
	array('label' => 'Delete Zserver', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm' => 'Are you sure you want to delete this item?')),
	array('label' => 'Manage Zserver', 'url' => array('admin')),
);
?>

<h1>View Zserver #<?= $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
		'id',
		'name',
		'library',
		'host',
		'port',
		'db',
		'format',
		'encode',
		'is_active',
	),
)); ?>
