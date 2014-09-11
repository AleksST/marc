<?php

class SiteController extends AppController
{
    private $source =  [
        'host' => '193.233.14.5',
        'port' => '9999',
        'database' => 'katb',
        'charset' => 'windows-1251',
        'syntax' => 'rusmarc',
    ];

    public function actionIndex() {
        /** @var Rusmarc $rm */
//        $rm = Marc::factory('rusmarc');
//        $rm->setRecordsLimit(1);
//		$request = new RpnQuery;
//		$request->addAnywhereCondition('любовь');
//		$rm->parseZServer($this->source, $request);
//        $this->render('index', ['marc' => $rm]);
        $this->render('index');
    }

    public function actionSearch() {
        $search = Yii::app()->request->getQuery('q', null);
        /** @var Rusmarc $rm */
        $rm = Marc::factory('rusmarc');
        $rm->setRecordsLimit(5);
        $request = new RpnQuery;
        $request->addAnywhereCondition($search);
        $rm->parseZServer($this->source, $request);
        echo $rm->getRecordsJson();
        Yii::app()->end();
    }
}
