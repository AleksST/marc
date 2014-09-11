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

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>1000)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'library'); ?>
		<?php echo $form->textField($model,'library',array('size'=>60,'maxlength'=>1000)); ?>
		<?php echo $form->error($model,'library'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'host'); ?>
		<?php echo $form->textField($model,'host',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'host'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'port'); ?>
		<?php echo $form->textField($model,'port'); ?>
		<?php echo $form->error($model,'port'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'db'); ?>
		<?php echo $form->textField($model,'db',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'db'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'is_rusmarc'); ?>
		<?php echo $form->textField($model,'is_rusmarc'); ?>
		<?php echo $form->error($model,'is_rusmarc'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->