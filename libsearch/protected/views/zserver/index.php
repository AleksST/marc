<?php
/* @var $this ZserverController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Zservers',
);

$this->menu=array(
	array('label'=>'Create Zserver', 'url'=>array('create')),
	array('label'=>'Manage Zserver', 'url'=>array('admin')),
);
?>

<h1>Zservers</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
