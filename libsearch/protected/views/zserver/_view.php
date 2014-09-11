<?php
/* @var $this ZserverController */
/* @var $data Zserver */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('library')); ?>:</b>
	<?php echo CHtml::encode($data->library); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('host')); ?>:</b>
	<?php echo CHtml::encode($data->host); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('port')); ?>:</b>
	<?php echo CHtml::encode($data->port); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('db')); ?>:</b>
	<?php echo CHtml::encode($data->db); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_rusmarc')); ?>:</b>
	<?php echo CHtml::encode($data->is_rusmarc); ?>
	<br />


</div>