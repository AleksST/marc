<?php
/* @var $this ZserverController */
/* @var $model Zserver */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?= $form->label($model,'id'); ?>
		<?= $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?= $form->label($model,'name'); ?>
		<?= $form->textField($model,'name',array('size'=>60,'maxlength'=>1000)); ?>
	</div>

	<div class="row">
		<?= $form->label($model,'library'); ?>
		<?= $form->textField($model,'library',array('size'=>60,'maxlength'=>1000)); ?>
	</div>

	<div class="row">
		<?= $form->label($model,'host'); ?>
		<?= $form->textField($model,'host',array('size'=>60,'maxlength'=>100)); ?>
	</div>

	<div class="row">
		<?= $form->label($model,'port'); ?>
		<?= $form->textField($model,'port'); ?>
	</div>

	<div class="row">
		<?= $form->label($model,'db'); ?>
		<?= $form->textField($model,'db',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?= $form->label($model,'format'); ?>
		<?= $form->textField($model,'format'); ?>
	</div>

	<div class="row">
		<?= $form->label($model,'encode'); ?>
		<?= $form->textField($model,'encode'); ?>
	</div>

	<div class="row">
		<?= $form->label($model,'is_active'); ?>
		<?= $form->textField($model,'is_active'); ?>
	</div>

	<div class="row buttons">
		<?= CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
