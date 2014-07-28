<?php
// rewrite: use autoload
require_once 'Formats/Rusmarc.php';
require_once 'Extractors/FileExtractor.php';
require_once 'Extractors/ZebrasrvExtractor.php';
require_once 'Parsers/BinaryRecordParser.php';
require_once 'Validators/RusmarcValidator.php';

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

        $this->setParentAndChilds();
		$this->ganerateTree();

		return $this->recordsTreeMap;
    }

	private function setParentAndChilds(){

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

	public function display(){
		echo '<div class="record-list">';
        foreach ($this->records as $record) {
            echo '<div class="record" id="' . $record->getId() . '">';
            $this->displayRecord($record);
            echo '</div>';
         }
		echo '</div>';
    }

	public function displayTree($node = null) {

		$node = $node ?: $this->recordsTreeMap;
		echo '<div class="record-list">';
        foreach ($node as $id => $elems) {
			$id =  is_array($elems) ? $id : $elems;
			echo '<div class="record" id="' . $id . '">';
			$this->displayRecord($this->records[$id]);
			is_array($elems) ? $this->displayTree($elems) : '';
			echo '</div>';
		}
		echo '</div>';
	}

	private function displayRecord(Record $record){
        echo '<div class="record-short">' . $record->getId() . '</div>';

        echo '<div class="record-full">';

            $errors = (array)$record->getErrors();
            $info = (array)$record->getInfo();

            echo '<div class="record-error">';
            foreach ($errors as $msg){
                echo '<div class="record-msg">' . $msg . '</div>';
            }
            echo '</div>';

            echo '<div class="record-info">';
            foreach ($info as $msg){
                echo '<div class="record-msg">' . $msg . '</div>';
            }
            echo '</div>';

            foreach ($record->getFields() as $tag=>$fields){
                echo '<div class="field" tag="' . $tag . '">';
                foreach ($fields as $field) {
                    $this->displayField($field);
                }
                echo '</div>';
            }

        echo '</div>';
    }

    private function displayField(Field $field){

		$errors = (array)$field->getErrors();
        $info = (array)$field->getInfo();

        echo '<div class="field-error">';
        foreach ($errors as $msg){
            echo '<div class="field-msg">' . $msg . '</div>';
        }
        echo '</div>';

        echo '<div class="field-info">';
        foreach ($info as $msg){
            echo '<div class="field-msg">' . $msg . '</div>';
        }
        echo '</div>';

        foreach ($field->getFields() as $tag=>$linked_fields){
            echo '<div class="linked-fields" tag="' . $tag . '">';
            foreach ($linked_fields as $linked_field){
                $this->displayLinkedField($linked_field);
            }
            echo '</div>';
        }

        $prefix = $this->getIndicatorsAndTag($field);
        foreach ($field->getSubfields() as $code=>$subfields){
            echo '<div code="' . $code . '">';
            foreach ($subfields as $subfield){
                echo $prefix . $code . ' | ';
                $this->displaySubfield($subfield);
            }
            echo '</div>';
        }

        if($value = $field->getValue()){
            echo $prefix;
            echo $value;
        }
    }

    private function displayLinkedField(Field $field){

		$prefix = $this->getIndicatorsAndTag($field->getParentField())
                . $this->getIndicatorsAndTag($field);

        foreach ($field->getSubfields() as $code=>$subfields){
            echo '<div code="' . $code . '">';
            foreach ($subfields as $subfield){
                echo $prefix . $code . ' | ';
                $this->displaySubfield($subfield);
            }
            echo '</div>';
        }
    }

    private function displaySubfield(Subfield $subfield){
        echo $subfield->getValue();
    }

    private function getIndicatorsAndTag(Field $field){
        return  str_replace(' ', '#', $field->getIndicator(1) . $field->getIndicator(2))
                . ' | ' . str_pad($field->getTag(), 3, '0', STR_PAD_LEFT) . ' | ';
    }
}