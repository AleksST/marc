<?php

class SiteController extends AppController
{

    public function actionIndex() {
        $source = [
            'host' => '193.233.14.5',
            'port' => '9999',
            'database' => 'katb',
            'charset' => 'windows-1251',
            'syntax' => 'rusmarc',
        ];
        /** @var Rusmarc $rm */
        $rm = Marc::factory('rusmarc');
        $rm->setRecordsLimit(5);
		$request = new RpnQuery;
		$request->addAnywhereCondition('любовь');
		$rm->parseZServer($source, $request);
        $this->render('index', ['marc' => $rm]);
    }
}
