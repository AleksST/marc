<?php

class Field
{

    private $tag;
    private $parantField = null;
    private $name = '';
    private $ind1;
    private $ind2;

    private $isControl = false;
    private $isData = true;
    private $isLinkedEntry = false;

    private $fields = array();
    private $subfields = array();
    private $value;

    private $errors;
    private $info;

    private $record = null;


    /**
     * @return Field
     */
    public static function getInstance($tag = 999) {
        $instance = new self;
        return $instance->setTag($tag);
    }

    /**
     * @return Field
     */
    public function setTag($tag) {
        $tag = (int)$tag;
        if (!($tag > 0 && $tag < 1000)) {
            throw new Exception('Field::setTag ');
        }
        $this->tag = $tag;
        return $this;
    }

    public function getTag() {
        return $this->tag;
    }

    public function setRecord(Record $record = null) {
        $this->record = $record;
        foreach ($this->getFields() as $fields) {
            foreach ($fields as $field) {
                $field->setRecord($record);
            }
        }
    }

    /**
     *
     * @return Record
     */
    public function getRecord() {
        return $this->record;
    }

    /**
     * @return Field
     */
    public function setName($name) {
        $this->name = (string)$name;
        return $this;
    }

    /**
     * @return Field
     */
    public function setInds($inds) {
        $this->setInd1($inds[0]);
        $this->setInd2($inds[1]);
        return $this;
    }

    private function setInd1($ind1) {
        if (strlen($ind1) !== 1) {
            throw new Exception('Field::setInd1');
        }
        $this->ind1 = $ind1;
    }

    private function setInd2($ind2) {
        if (strlen($ind2) !== 1) {
            throw new Exception('Field::setInd2');
        }
        $this->ind2 = $ind2;
    }

    public function getIndicator($num) {
        if (in_array($num, array(1, 2))) {
            return $this->{"ind$num"};
        }
        return false;
    }

    public function getName() {
        return $this->name;
    }

    /**
     * @return self
     */
    public function setControlField() {
        $this->isData = $this->isLinkedEntry = false;
        $this->isControl = true;
        return $this;
    }

    public function isControlField() {
        return $this->isControl;
    }

    /**
     * @return self
     */
    public function setDataField() {
        $this->isControl = $this->isLinkedEntry = false;
        $this->isData = true;
        return $this;
    }

    public function isDataField() {
        return $this->isData;
    }

    /**
     * @return self
     */
    public function setLinkedEntryField() {
        $this->isLinkedEntry = true;
        $this->isControl = $this->isData = false;
        return $this;
    }

    public function isLinkedEntryField() {
        return $this->isLinkedEntry;
    }

    /**
     * @return self
     */
    public function addSubfield(Subfield $subfield) {
        $subfield->setField($this);
        $this->subfields[$subfield->getCode()][] = $subfield->setTag($this->getTag());
        return $this;
    }

    /**
     * @return Field
     */
    public function addSubfields($subfields) {
        foreach ($subfields as $subfield) {
            $this->addSubfield($subfield);
        }
        return $this;
    }

    public function getSubfield($code) {
        return $this->subfields[$code];
    }

    /**
     * @return Field
     */
    public function addField(Field $field) {
        $field->setParentField($this);
        $field->setRecord($this->getRecord());
        $this->fields[$field->getTag()][] = $field;
        return $this;
    }

    public function setParentField(Field $field) {
        $this->parantField = $field;
    }

    /**
     * @return array
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * @return Field[]
     */
    public function getFieldsList() {
        $list = [];
        foreach ($this->fields as $fields) {
            foreach ($fields as $field) {
                $list[] = $field;
            }
        }

        return $list;
    }

    /**
     * @return Field[]
     */
    public function getField($tag) {
        return $this->fields[$tag];
    }

    /**
     *
     * @return Field
     */
    public function getParentField() {
        return $this->parantField;
    }

    public function getParentTag() {
        return $this->getParentField()->getTag();
    }

    public function hasParent() {
        return ($this->getParentField() !== null);
    }

    /**
     * @return Field
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    public function getValue() {
        return $this->value;
    }

    /**
     * @return array [code => Subfield[]]
     */
    public function getSubfields() {
        return $this->subfields;
    }

    /**
     * @return Subfield[]
     */
    public function getSubfieldsList() {
        $list = [];
        foreach ($this->subfields as $code => $subfields) {
            foreach ($subfields as $subfield) {
                $list[] = $subfield;
            }
        }

        return $list;
    }

    public function setError($msg) {
        $this->errors[] = $msg;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function setInfo($msg) {
        $this->info[] = $msg;
    }

    public function getInfo() {
        return $this->info;
    }

}