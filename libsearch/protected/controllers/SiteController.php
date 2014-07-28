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
        $rm->setRecordsLimit(50);
        $records_count = count($rm->parseZServer($source, []));
        $this->render('index', ['marc' => $rm]);
    }
}
