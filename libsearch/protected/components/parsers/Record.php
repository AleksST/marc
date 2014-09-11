<?php

class Record
{

    private $id;
    private $parentId;
    private $childrenIds = array();
    private $relevantIds = array();
    private $fields = array();
    private $leader;
    private $errors;
    private $info;
    private $encode = null;

    /**
     * @return Record
     */
    public static function getInstance() {
        return new self;
    }

    /**
     * @param string $msg
     * @return Record
     */
    public function setError($msg) {
        $this->errors[] = $msg;
        return $this;
    }

    /**
     * @param string $msg
     * @internal param string $type
     * @return Record
     */
    public function setInfo($msg) {
        $this->info[] = $msg;
        return $this;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getInfo() {
        return $this->info;
    }

    /**
     * @param int $tag
     * @return array, Field or false
     */
    public function getField($tag) {

        if (!$this->isFieldExists($tag)) {
            return false;
        }

        return $this->fields[$tag];
    }

    public function isFieldExists($tag) {
        return array_key_exists((int)$tag, $this->fields);
    }

    /**
     * @param Field $field
     * @return Record
     */
    public function addField(Field $field) {
        $tag = (int)$field->getTag();

        if ($tag === 1) {
            $this->setId($field->getValue());
        }

        $field->setRecord($this);
        $this->fields[$tag][] = $field;

        return $this;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Record
     */
    public function setId($id) {
        $this->id = trim($id);
        return $this;
    }

    public function getParentId() {
        return $this->parentId;
    }

    /**
     * @param string $id
     * @return Record
     */
    public function setParentId($id) {
        $this->parentId = trim($id);
        return $this;
    }

    public function getChildrenIds() {
        return $this->childrenIds;
    }

    /**
     * @param string $id
     * @return Record
     */
    public function setChildId($id) {
        $this->childrenIds[] = trim($id);
        array_unique($this->childrenIds);
        return $this;
    }

    public function getRelevantIds() {
        return $this->relevantIds;
    }

    /**
     * @param string $id
     * @return Record
     */
    public function setRelevantId($id) {
        $this->relevantIds[] = trim($id);
        array_unique($this->relevantIds);
        return $this;
    }

    public function getLeader() {
        return $this->leader;
    }

    /**
     * @param string $leader
     * @return Record
     */
    public function setLeader($leader) {
        $this->leader = $leader;
        return $this;
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

    public function setEncode($encode) {
        $this->encode = $encode;
        return $this;
    }

    public function getEncode() {
        return $this->encode;
    }

    public function toUnicode() {
        return $this->convertToUnicode('utf-8');
    }

    public function convertToUnicode($encode = 'utf-8') {

        if (!$this->encode || $this->encode == $encode) {
            return $this;
        }

        $this->setId(iconv($this->encode, $encode, $this->getId()));

        foreach ($this->getFields() as $fields) {
            foreach ($fields as $field) {
                $this->fieldToEncode($field, $encode);
            }
        }

        return $this->setEncode($encode);
    }

    private function fieldToEncode(Field $field, $out_encode) {

        $field->setValue(iconv($this->encode, $out_encode, $field->getValue()));

        if (is_array($field->getFields())) {
            foreach ($field->getFields() as $linkedfields) {
                foreach ($linkedfields as $linkedfield) {
                    $this->fieldToEncode($linkedfield, $out_encode);
                }
            }
        }

        if (is_array($field->getSubfields())) {
            foreach ($field->getSubfields() as $subfields) {
                foreach ($subfields as $subfield) {
                    $subfield->setValue(iconv($this->encode, $out_encode, $subfield->getValue()));
                }
            }
        }
    }
}