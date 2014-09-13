<?php
/* @var $this ZserverController */
/* @var $model Zserver */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'zserver-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?= $form->errorSummary($model); ?>

	<div class="row">
		<?= $form->labelEx($model,'name'); ?>
		<?= $form->textField($model,'name',array('size'=>60,'maxlength'=>1000)); ?>
		<?= $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?= $form->labelEx($model,'library'); ?>
		<?= $form->textField($model,'library',array('size'=>60,'maxlength'=>1000)); ?>
		<?= $form->error($model,'library'); ?>
	</div>

	<div class="row">
		<?= $form->labelEx($model,'host'); ?>
		<?= $form->textField($model,'host',array('size'=>60,'maxlength'=>100)); ?>
		<?= $form->error($model,'host'); ?>
	</div>

	<div class="row">
		<?= $form->labelEx($model,'port'); ?>
		<?= $form->numberField($model,'port'); ?>
		<?= $form->error($model,'port'); ?>
	</div>

	<div class="row">
		<?= $form->labelEx($model,'db'); ?>
		<?= $form->textField($model,'db',array('size'=>50,'maxlength'=>50)); ?>
		<?= $form->error($model,'db'); ?>
	</div>

	<div class="row">
		<?= $form->labelEx($model,'format'); ?>
		<?= $form->textField($model,'format'); ?>
		<?= $form->error($model,'format'); ?>
	</div>

	<div class="row">
		<?= $form->labelEx($model,'encode'); ?>
		<?= $form->textField($model,'encode'); ?>
		<?= $form->error($model,'encode'); ?>
	</div>

	<div class="row">
		<?= $form->labelEx($model,'is_active'); ?>
		<?= $form->textField($model,'is_active'); ?>
		<?= $form->error($model,'is_active'); ?>
	</div>

	<div class="row buttons">
		<?= CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
