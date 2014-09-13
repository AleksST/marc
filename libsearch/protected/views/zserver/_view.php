<?php
/* @var $this ZserverController */
/* @var $data Zserver */
?>

<div class="view">

	<b><?= CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?= CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?= CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?= CHtml::encode($data->name); ?>
	<br />

	<b><?= CHtml::encode($data->getAttributeLabel('library')); ?>:</b>
	<?= CHtml::encode($data->library); ?>
	<br />

	<b><?= CHtml::encode($data->getAttributeLabel('host')); ?>:</b>
	<?= CHtml::encode($data->host); ?>
	<br />

	<b><?= CHtml::encode($data->getAttributeLabel('port')); ?>:</b>
	<?= CHtml::encode($data->port); ?>
	<br />

	<b><?= CHtml::encode($data->getAttributeLabel('db')); ?>:</b>
	<?= CHtml::encode($data->db); ?>
	<br />

	<b><?= CHtml::encode($data->getAttributeLabel('format')); ?>:</b>
	<?= CHtml::encode($data->format); ?>
	<br />

	<b><?= CHtml::encode($data->getAttributeLabel('encode')); ?>:</b>
	<?= CHtml::encode($data->encode); ?>
	<br />

	<b><?= CHtml::encode($data->getAttributeLabel('is_active')); ?>:</b>
	<?= CHtml::encode($data->is_active); ?>
	<br />


</div>
