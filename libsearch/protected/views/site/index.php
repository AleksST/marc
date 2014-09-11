<?php /** @var Marc $marc */ ?>
<?php Yii::app()->clientScript->registerScriptFile('js/searchCtrl.js'); ?>
<div class="record-list"  ng-controller="searchCtrl">
    <div class="search-form">
        <form>
            <input type="text" name="condition" ng-model="search">
            <button ng-click="loadData()" class="submit">Find</button>
        </form>
    </div>
    <div ng-repeat="record in records">
        <div ng-repeat="(tag, field) in record">
            <div ng-repeat="(cod, subfield) in field">
                <div ng-repeat="value in subfield">
                    {{tag}}#{{cod}}|{{value}}
                </div>
            </div>
        </div>
    </div>

    <div>{{search}}</div>

</div>
