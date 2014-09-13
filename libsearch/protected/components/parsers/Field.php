<?php

class Field
{

	private $tag;
	private $parentField = null;
	private $name = '';
	private $ind1;
	private $ind2;

	private $isControl = false;
	private $isData = true;
	private $isLinkedEntry = false;

	private $fields = array();
	private $subfields = array();
	private $value;

	private $record = null;


	/**
	 * @param int $tag
	 * @return Field
	 */
	public static function getInstance($tag = 999)
	{
		$instance = new self;
		return $instance->setTag($tag);
	}

	/**
	 * @param string|int $tag
	 * @throws Exception
	 * @return self
	 */
	public function setTag($tag)
	{
		$tag = (int)$tag;
		if (!($tag > 0 && $tag < 1000)) {
			throw new Exception('Field::setTag');
		}
		$this->tag = $tag;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getTag()
	{
		return $this->tag;
	}

	/**
	 * @param Record $record
	 */
	public function setRecord(Record $record = null)
	{
		$this->record = $record;
		foreach ($this->getFields() as $fields) {
			/** @var Field $field */
			foreach ($fields as $field) {
				$field->setRecord($record);
			}
		}
	}

	/**
	 * @return Record
	 */
	public function getRecord()
	{
		return $this->record;
	}

	/**
	 * @param string $name
	 * @return Field
	 */
	public function setName($name)
	{
		$this->name = (string)$name;
		return $this;
	}

	/**
	 * @param array $inds
	 * @return self
	 */
	public function setInds($inds)
	{
		$this->setInd1(isset($inds[0]) ? $inds[0] : '');
		$this->setInd2(isset($inds[0]) ? $inds[0] : '');
		return $this;
	}

	/**
	 * @param string $ind1
	 * @throws Exception
	 */
	private function setInd1($ind1)
	{
		if (strlen($ind1) !== 1) {
			throw new Exception('Field::setInd1');
		}
		$this->ind1 = $ind1;
	}

	/**
	 * @param string $ind2
	 * @throws Exception
	 */
	private function setInd2($ind2)
	{
		if (strlen($ind2) !== 1) {
			throw new Exception('Field::setInd2');
		}
		$this->ind2 = $ind2;
	}

	/**
	 * @param int $num
	 * @return string|false
	 */
	public function getIndicator($num)
	{
		if (in_array($num, array(1, 2))) {
			return $this->{"ind$num"};
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return self
	 */
	public function setControlField()
	{
		$this->isData = $this->isLinkedEntry = false;
		$this->isControl = true;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isControlField()
	{
		return $this->isControl;
	}

	/**
	 * @return self
	 */
	public function setDataField()
	{
		$this->isControl = $this->isLinkedEntry = false;
		$this->isData = true;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isDataField()
	{
		return $this->isData;
	}

	/**
	 * @return self
	 */
	public function setLinkedEntryField()
	{
		$this->isLinkedEntry = true;
		$this->isControl = $this->isData = false;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isLinkedEntryField()
	{
		return $this->isLinkedEntry;
	}

	/**
	 * @param Subfield $subfield
	 * @return self
	 */
	public function addSubfield(Subfield $subfield)
	{
		$subfield->setField($this);
		$this->subfields[$subfield->getCode()][] = $subfield->setTag($this->getTag());
		return $this;
	}

	/**
	 * @param array $subfields
	 * @return self
	 */
	public function addSubfields($subfields)
	{
		foreach ($subfields as $subfield) {
			$this->addSubfield($subfield);
		}
		return $this;
	}

	/**
	 * @param string $code
	 * @return Subfield[]
	 */
	public function getSubfield($code)
	{
		return $this->subfields[$code];
	}

	/**
	 * @param Field $field
	 * @return self
	 */
	public function addField(Field $field)
	{
		$field->setParentField($this);
		$field->setRecord($this->getRecord());
		$this->fields[$field->getTag()][] = $field;
		return $this;
	}

	/**
	 * @param Field $field
	 * @throws Exception
	 */
	public function setParentField(Field $field)
	{
		if (null !== $this->parentField) {
			throw new Exception('Parent field already set');
		}
		$this->parentField = $field;
	}

	/**
	 * @return array
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/**
	 * @param int $tag
	 * @return array
	 */
	public function getField($tag)
	{
		return $this->fields[$tag];
	}

	/**
	 * @return Field|null
	 */
	public function getParentField()
	{
		return $this->parentField;
	}

	/**
	 * @return int|null
	 */
	public function getParentTag()
	{
		if ($parent = $this->getParentField()) {
			return $this->getTag();
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public function hasParent()
	{
		return ($this->getParentField() !== null);
	}

	/**
	 * @param mixed $value
	 * @return Field
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return array [code => Subfield[]]
	 */
	public function getSubfields()
	{
		return $this->subfields;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		$field = [];

		if ($this->isControl) {
			return ['a' => [$this->value]];
		}

		if ($this->isData) {
			foreach ($this->subfields as $cod => $subfields) {
				/** @var Subfield $subfield */
				foreach ($subfields as $subfield) {
					$field[$cod][] = $subfield->getValue();
				}
			}
		}

		if ($this->isLinkedEntry) {
			/** @var Field $linkedField */
			foreach ($this->getFields() as $cod => $linkedFields) {
				foreach ($linkedFields as $linkedField) {
					$field[$cod] = $linkedField->toArray();
				}
			}
		}

		return $field;
	}

}
