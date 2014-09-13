<?php

abstract class AbstractSourceExtractor
{
    protected $records = array();
    /**
     * @var AbstractRecordParser
     */
    protected $recordParser;

    /**
     * 0 - no limits, read all records
     * @var int
     */
    protected $recordsLimit = 0;
    protected $recordsRead = 0;

    public function __construct(AbstractRecordParser $recordParser) {
        $this->setRecordParser($recordParser);
    }

    public function setRecordParser(AbstractRecordParser $recordParser) {
        $this->recordParser = $recordParser;
        return $this;
    }

    public function setLimit($limit) {
        $this->recordsLimit = ($limit < 0) ? 0 : $limit;
        return $this;
    }

    abstract function setSource($source);

    /**
     * @return Record or false
     */
    abstract function getNextRecord();
}
