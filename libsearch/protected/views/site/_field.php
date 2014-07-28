<?php
/** @var Field $field */
$errors = (array)$field->getErrors();
$info = (array)$field->getInfo();
?>
<div class="field-error">
    <?php foreach ($errors as $msg): ?>
        <div class="field-msg"><?= $msg; ?></div>
    <?php endforeach; ?>
    </div>

<div class="field-info">
    <?php foreach ($info as $msg): ?>
        <div class="field-msg"><?= $msg; ?></div>
    <?php endforeach; ?>
    </div>

<?php foreach ($field->getFields() as $tag=>$linked_fields): ?>
<div class="linked-fields" tag="<?= $tag; ?>">
    <?php foreach ($linked_fields as $linked_field): ?>
        <?php foreach ($field->getSubfields() as $code=>$subfields): ?>
            <div code="<?= $code ?>">
            <?php foreach ($subfields as $subfield): ?>
                <?= $prefix . $code . ' | ' . $subfield->getValue(); ?>
            <?php endforeach; ?>
           </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<?php
    $prefix = str_replace(' ', '#', $field->getIndicator(1) . $field->getIndicator(2))
              . ' | ' . str_pad($field->getTag(), 3, '0', STR_PAD_LEFT) . ' | ';
?>
<?php foreach ($field->getSubfields() as $code=>$subfields): ?>
    <div code="<?= $code; ?>">
    <?php foreach ($subfields as $subfield): ?>
        <?= $prefix . $code . '|' . $subfield->getValue(); ?>
    <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<?php if($value = $field->getValue()): ?>
    <?= $prefix . $value; ?>
<?php endif; ?>