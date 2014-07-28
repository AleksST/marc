<?php

abstract class Marc {

    /**
     * @var AbstractValidator
     */
    protected $Validator;
    /**
     * @var AbstractStructure
     */
    protected $Structure;
    /**
     * @var AbstractSourceExtractor
     */
    protected $Extractor;

    /**
     * @var Record[]
     */
    protected $records = array();
    protected $recordsTreeMap = array();
    protected $errors = array();

    protected $recordsLimit = 0;

    abstract public function isIndicatorExists($tag, $num, $ind);
    abstract public function isFieldExists($tag);
    abstract public function isSubfieldExists($tag, $code);
    abstract public function isFieldRepeatable($tag);
    abstract public function isSubfieldRepeatable($tag, $code);
    abstract public function isControlField($tag);
    abstract public function isDataField($tag);
    abstract public function isLinkedEntryField($tag);
    abstract public function isValueCoded($tag, $code);
    abstract public function getSubfieldPositions($tag, $code);
    abstract public function getFieldName($tag);
    abstract public function getSubfieldName($tag, $code) ;
    abstract public function getIndicatorValueNames($tag, $num);
    abstract public function getIndicatorName($tag, $num) ;
    abstract public function getParentTags();
    abstract public function getChildTags();
    abstract public function getMandatoryFields();
    abstract public function getLeaderPositions();

    /**
     * Factory method
     * @param string $format
     * @return Marc
     * @throws Exception
     */
    public static function factory($format) {
        switch (strtolower($format)){
            case 'rusmarc':
                $marc =  new Rusmarc;
                $validator = new RusmarcValidator;
                $marc->setValidator($validator);
                return $marc;
            default : throw new Exception('Unknown format : ' . $format);
        }
    }

    public function getRecords() {
        return $this->records;
    }

    public function setRecordsLimit($limit = 0){
        $this->recordsLimit = (int) $limit;
    }

    public function parseFile($fname){
        $this->Extractor = new FileExtractor(new BinaryRecordParser);
        $this->Extractor->setSource($fname)->setLimit($this->recordsLimit);

        while ($record = $this->Extractor->getNextRecord()) {
            $this->Validator->validate($record);
            $this->records[$record->getId()] = $record->toUnicode();
        }

        return $this->createRecordsList();
    }

    public function parseZServer($options, $request){

        $this->Extractor = new ZebrasrvExtractor(new BinaryRecordParser);
        $this->Extractor->setSource($options, $request)->setLimit($this->recordsLimit);

        while ($record = $this->Extractor->getNextRecord()) {
            $record->setEncode($options['charset']);
            $this->Validator->validate($record);
            $this->records[$record->getId()] = $record->toUnicode();
        }

        return $this->createRecordsList();

    }

    public function setValidator(AbstractValidator $validator) {
        $this->Validator = $validator->setFormat($this);
    }

    protected function createRecordsList(){

        $this->setParentAndChildren();
		$this->ganerateTree();

		return $this->recordsTreeMap;
    }

	private function setParentAndChildren(){

        foreach ($this->records as $record){
            if(array_key_exists($record->getParentId(), $this->records)){
                $this->records[$record->getParentId()]->setChildId($record->getId());
            }
        }

        foreach ($this->records as $record){
            foreach ($record->getChildrenIds() as $child_id){
                if(array_key_exists($child_id, $this->records)){
                    $this->records[$child_id]->setParentId($record->getId());
                }
            }
        }
    }

	private function ganerateTree() {
		foreach ($this->records as $record) {
            $id = $record->getId();
			$parent_id = (array_key_exists($record->getParentId(), $this->records))
					? $record->getParentId() : null;
			$top_id = ($parent_id) ? $this->records[$parent_id]->getParentId() : null;

			if ($top_id) {
				$this->recordsTreeMap[$top_id][$parent_id][] = $id;
			} elseif ($parent_id && empty($this->recordsTreeMap[$parent_id][$id])) {
				$this->recordsTreeMap[$parent_id][] = $id;
			} elseif (empty($this->recordsTreeMap[$id])) {
				$this->recordsTreeMap[] = $id;
			}
        }
	}
}