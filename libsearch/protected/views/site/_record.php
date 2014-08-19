<?php
/** @var Record $record */
$errors = (array)$record->getErrors();
$info = (array)$record->getInfo();
?>

<div class="record-id">
    <?= $record->getId(); ?>
</div>
<div class="record-full">

    <!--<div class="record-error">
        <?php foreach ($errors as $msg): ?>
            <div class="record-msg"><?= $msg; ?></div>
        <?php endforeach; ?>
    </div>

    <div class="record-info">
        <?php foreach ($info as $msg): ?>
            <div class="record-msg"><?= $msg ?></div>
        <?php endforeach; ?>
    </div>-->

    <?php foreach ($record->getFields() as $tag => $fields): ?>
    <div class="field" tag="<?= $tag ?>">
        <?php
            foreach ($fields as $field) {
                $this->renderPartial('_field', ['field' => $field]);
            }
        ?>
    </div>
    <?php endforeach; ?>
</div>