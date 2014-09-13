<?php

class ZServerService
{

	public static function checkActive()
	{
		$request = new RpnQuery;
		$request->addAnywhereCondition('a');

		/** @var Zserver $server */
		foreach (Zserver::model()->findAll('is_active is null') as $server) {
			if ('rusmarc' !== $server->format) continue;
			if ($server->is_active) continue;

			$rm = Marc::factory($server->format);
			$rm->setRecordsLimit(1);

			try {
				$rm->parseZServer($server, $request);
			} catch (Exception $e) {
				echo $e->getMessage() . '(' . $server->name . ': ' . $server->host . ':' . $server->port . ')';
			}

			/** @var Record $record */
			$record = current($rm->getRecords());
			$server->setAttribute('is_active', ($record instanceof Record));
			$server->save();
		}
	}

    public static function generate() {
	    $filename = Yii::app()->basePAth . '/components/formats/Z39.50_ru.html';
	    if (!is_file($filename) || !is_writable($filename)) {
		    throw new Exception ('Cannot open file');
	    }

	    $doc = new DOMDocument();
	    $doc->loadHTMLFile($filename);
	    $doc->encoding = 'UTF-8';
	    $tables = $doc->getElementsByTagName('table');
	    $data = [];
	    /** @var DOMElement $table */
	    foreach ($tables as $table) {
		    /** @var DOMElement $row */
		    foreach ($table->getElementsByTagName('tr') as $row){
			    $cols = $row->getElementsByTagName('td');
			    if (0 === $cols->length) continue;
			    $data[] = [
					'name' => self::cleanStr($cols->item(0)),
					'db' => self::cleanStr($cols->item(1)),
					'host' => self::cleanStr($cols->item(3)),
					'port' => self::cleanStr($cols->item(4)),
					'format' => stripos(self::cleanStr($cols->item(5)), 'rusmarc') ? 'rusmarc' : '',
			    ];
		    }
	    }

	    foreach ($data as $server) {
		    $zs = new Zserver();
		    $zs->attributes = $server;
		    $zs->save();
	    }

    }

	protected static function cleanStr(/*DOMElement*/ $node)
	{
		return trim(strip_tags($node->nodeValue));
	}

}
