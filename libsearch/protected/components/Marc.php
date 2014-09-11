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

    public function parseZServer($options, RpnQuery $request) {
        $this->Extractor = new ZebrasrvExtractor(new BinaryRecordParser);
        $this->Extractor->setSource($options, $request)->setLimit($this->recordsLimit);

        while ($record = $this->Extractor->getNextRecord()) {
            $record->setEncode($options['charset']);
            $this->Format->getValidator()->validate($record);
            $this->records[$record->getId()] = $record->toUnicode();
        }
    }

    public function getRecordsJson() {
        $data = [];
        foreach ($this->getRecords() as $record) {
            $data[] = $this->recordToArray($record);
        }

        return json_encode($data);
    }

    protected function recordToArray(Record $record) {
        $rec = [];
        foreach ($record->getFieldsList() as $field) {
            if ($field->isControlField()) {
                $rec[$field->getTag()]['a'][] = $field->getValue();
            }

            if ($field->isDataField()) {
                foreach ($field->getSubfieldsList() as $subfield) {
                    $rec[$field->getTag()][$subfield->getCode()][] = $subfield->getValue();
                }
            }

            if ($field->isLinkedEntryField()) {
                foreach ($field->getFieldsList() as $linkedField) {
                    foreach ($linkedField->getSubfieldsList() as $subfield) {
                        $rec[$field->getTag() . '#' .$linkedField->getTag()][$subfield->getCode()][] = $subfield->getValue();
                    }
                }
            }
        }

        return $rec;
    }
}