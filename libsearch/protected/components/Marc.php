<?php

abstract class Marc
{
    /**
     * @var AbstractSourceExtractor
     */
    protected $Extractor;

    /**
     * @var AbstractFormat
     */
    protected $Format;

    /**
     * @var Record[]
     */
    protected $records = array();
    protected $recordsLimit = 0;

    public function setFormat(AbstractFormat $format)
    {
        $this->Format = $format;
    }

    /**
     * Factory method
     * @param string $format
     * @return Marc
     * @throws Exception
     */
    public static function factory($format) {
        switch (strtolower($format)) {
            case 'rusmarc':
                $marc = new Rusmarc;
                $marcFormat = new RusmarcFormat;
                $validator = new RusmarcValidator;
                $marcFormat->setValidator($validator);
                $marc->setFormat($marcFormat);
                return $marc;
            default :
                throw new Exception('Unknown format : ' . $format);
        }
    }

	/**
	 * @return Record[]
	 */
    public function getRecords() {
        return $this->records;
    }

    public function setRecordsLimit($limit = 0) {
        $this->recordsLimit = (int)$limit;
    }

    public function parseFile($fname) {
        $this->Extractor = new FileExtractor(new BinaryRecordParser);
        $this->Extractor->setSource($fname)->setLimit($this->recordsLimit);

        while ($record = $this->Extractor->getNextRecord()) {
            //$this->Format->getValidator()->validate($record);
            $this->records[$record->getId()] = $record->toUnicode();
        }
    }

    public function parseZServer(Zserver $yazServer, RpnQuery $request) {
        $this->Extractor = new ZebrasrvExtractor(new BinaryRecordParser);
        $this->Extractor->setSource($yazServer, $request)->setLimit($this->recordsLimit);

        while ($record = $this->Extractor->getNextRecord()) {
            $record->setEncode($yazServer->encode);
//            $this->Format->getValidator()->validate($record);
            $this->records[$record->getId()] = $record->toUnicode();
        }
    }

	/**
	 * @return array
	 */
	public function toArray()
	{
		$out = [];
		foreach ($this->records as $record) {
			$out[] = $record->toArray();
		}

		return $out;
	}

	/**
	 * @return string
	 */
	public function toJson()
	{
		return json_encode($this->toArray());
	}

}
