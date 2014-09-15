<?php /** @var Marc $marc */ ?>
<?php Yii::app()->clientScript->registerScriptFile('js/searchCtrl.js'); ?>

<div class="record-list"  ng-controller="searchCtrl" data-ng-init="init()">
    <div class="search-form">
        <form>
            <input type="text" name="condition" ng-model="search">
	        <select ng-model="selectedSources" ng-options="key for (key, servers) in sources" style="width: 100px;"></select>
            <button ng-click="loadData()" class="submit">Find</button>
        </form>
    </div>
	<div ng-repeat="record in records">
        {{record.title}}|{{record.year}}|{{record.isbn}}|{{record.authors}}
    </div>
</div>
