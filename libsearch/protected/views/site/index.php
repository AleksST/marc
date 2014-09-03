<?php /** @var Marc $marc */ ?>
<div class="record-list" ng-app="">
<?php foreach ($marc->getRecords() as $record) : ?>
    <div class="record" id="<?= $record->getId() ?>">
        <?= $this->renderPartial('_record', ['record' => $record]); ?>
    </div>
<?php endforeach; ?>
</div>
