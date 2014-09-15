<?php

class Record
{

	private $id;
	private $parentId;
	private $childrenIds = array();
	private $relevantIds = array();
	private $fields = array();
	private $leader;
	private $encode = null;

	/**
	 * @return Record
	 */
	public static function getInstance()
	{
		return new self;
	}

	/**
	 * @param int $tag
	 * @return array|Field|false
	 */
	public function getField($tag)
	{
		if (!$this->isFieldExists($tag)) {
			return false;
		}

		return $this->fields[$tag];
	}

	/**
	 * @param string|int $tag
	 * @return bool
	 */
	public function isFieldExists($tag)
	{
		return array_key_exists((int)$tag, $this->fields);
	}

	/**
	 * @param Field $field
	 * @return self
	 */
	public function addField(Field $field)
	{
		if (1 === ($tag = $field->getTag())) {
			$this->setId($field->getValue());
		}

		$field->setRecord($this);
		$this->fields[$tag][] = $field;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 * @return self
	 */
	public function setId($id)
	{
		$this->id = trim($id);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getParentId()
	{
		return $this->parentId;
	}

	/**
	 * @param string $id
	 * @return Record
	 */
	public function setParentId($id)
	{
		$this->parentId = trim($id);
		return $this;
	}

	/**
	 * @return array
	 */
	public function getChildrenIds()
	{
		return $this->childrenIds;
	}

	/**
	 * @param string $id
	 * @return self
	 */
	public function setChildId($id)
	{
		$this->childrenIds[] = trim($id);
		array_unique($this->childrenIds);
		return $this;
	}

	/**
	 * @return array
	 */
	public function getRelevantIds()
	{
		return $this->relevantIds;
	}

	/**
	 * @param string $id
	 * @return self
	 */
	public function setRelevantId($id)
	{
		$this->relevantIds[] = trim($id);
		array_unique($this->relevantIds);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLeader()
	{
		return $this->leader;
	}

	/**
	 * @param string $leader
	 * @return self
	 */
	public function setLeader($leader)
	{
		$this->leader = $leader;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/**
	 * @param string $encode
	 * @return self
	 */
	public function setEncode($encode)
	{
		$this->encode = $encode . '';
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getEncode()
	{
		return $this->encode;
	}

	/**
	 * @return self
	 */
	public function toUnicode()
	{
		return $this->convertToUnicode('utf-8');
	}

	/**
	 * @param string $encode
	 * @return self
	 */
	public function convertToUnicode($encode = 'utf-8')
	{

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

	/**
	 * Convert fields to $out_encode
	 * @param Field $field
	 * @param string $out_encode
	 */
	private function fieldToEncode(Field $field, $out_encode)
	{

		$field->setValue(iconv($this->encode, $out_encode, $field->getValue()));

		if (is_array($field->getFields())) {
			foreach ($field->getFields() as $linkedFields) {
				foreach ($linkedFields as $linkedField) {
					$this->fieldToEncode($linkedField, $out_encode);
				}
			}
		}

		if (is_array($field->getSubfields())) {
			foreach ($field->getSubfields() as $subfields) {
				/** @var Subfield $subfield  */
				foreach ($subfields as $subfield) {
					$subfield->setValue(iconv($this->encode, $out_encode, $subfield->getValue()));
				}
			}
		}
	}

    /**
     * @param int $tag
     * @param string $cod
     * @return mixed|null
     */
    public function getValueByTagCode($tag, $cod = 'a')
    {
        $tag = (int) $tag;
        $cod = substr($cod, 0,1);
        if (!is_array($fields = $this->getField($tag))) {
            return null;
        }

        /** @var Field $field */
        foreach ($fields as $field) {
            if (!is_array($subfields = $field->getSubfield($cod))) continue;
            /** @var Subfield $subfield */
            foreach ($subfields as $subfield) {
                return $subfield->getValue();
            }
        }

        return null;
    }

    /**
     * @param int $tag
     * @param string $cod
     * @return array
     */
    public function getValuesByTagCode($tag, $cod = 'a')
    {
        $tag = (int) $tag;
        $cod = substr($cod, 0,1);
        if (!is_array($fields = $this->getField($tag))) {
            return [];
        }

        $out = [];
        /** @var Field $field */
        foreach ($fields as $field) {
            if (!is_array($subfields = $field->getSubfield($cod))) continue;
            /** @var Subfield $subfield */
            foreach ($subfields as $subfield) {
                $out[] = $subfield->getValue();
            }
        }

        return $out;
    }

	/**
	 * @return array
	 */
	public function toArray()
	{
		$record = [];
		foreach ($this->fields as $tag => $fields) {
			/** @var Field $field */
			foreach ($fields as $field) {
				$record[$tag][] = $field->toArray();
			}
		}

		return $record;
	}

	/**
	 * @return string
	 */
	public function toJson()
	{
		return json_encode($this->toArray());
	}
}
