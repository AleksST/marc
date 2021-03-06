<?php

class SiteController extends AppController
{
	private $recordsLimit = 10;

    public function actionIndex() {
	    //ZServerService::generate();
	    //ZServerService::checkActive();
        $this->render('index');
    }

    public function actionSearch() {
		$search = Yii::app()->request->getQuery('q', null);
		$server_id = Yii::app()->request->getQuery('s', null);

	    /** @var Zserver $server */
	    if (!$server_id || !$server = Zserver::model()->findByPk($server_id)) {
		    //todo error
	    }

	    $request = new RpnQuery;
	    $request->addAnywhereCondition($search);

	    /** @var Marc $rm */
		$rm = Marc::factory($server->format);
		$rm->setRecordsLimit($this->recordsLimit);
//        $rm->parseFile('/home/aleks/www/marc/test_files/sven09.mrc');
		$rm->parseZServer($server, $request);
//        var_dump($pretty->convert(current($rm->getRecords())));
		echo $rm->toPrettyJson();
//		var_dump($rm->toArray());
		Yii::app()->end();
    }

	public function actionGetSources()
	{
		/** @var Zserver[] $servers */
		$servers = Zserver::model()->findAllByAttributes(array('is_active' => true));
		echo CJSON::encode($servers);
		Yii::app()->end();
	}
}
