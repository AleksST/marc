<?php

class BinaryRecordParser extends AbstractRecordParser
{
	private $subfieldSeparator = "\x1F";
	private $fieldSeparator = "\x1E";
	private $linkedSeparator = "\x1F1";

	private $leaderLength = 24;
	private $tagLength = 3;
	private $indLength = 2;

	private $leader = array();
	private $directory = array();

	private $binaryRecord;

	/**
	 * Parse binary record
	 * @param string $binaryRecord
	 * @return Record
	 */
	public function parse($binaryRecord = '')
	{
		$this->leader = $this->directory = array();

		if (strlen($binaryRecord) < $this->leaderLength) {
			return false;
		}

		$this->binaryRecord = $binaryRecord;
		$this->record = Record::getInstance();

		$this->parseLeader();
		$this->parseDirectory();
		$this->parseData();

		return $this->record;
	}

	/**
	 * @param string $data
	 * @return bool
	 */
	private function isLinkedField($data)
	{
		return (substr($data, 2, 2) === $this->linkedSeparator);
	}

	/**
	 * @param int $tag
	 * @return bool
	 */
	private function isControlField($tag)
	{
		return ($tag < 10);
	}

	private function parseLeader()
	{
		$leader = substr($this->binaryRecord, 0, $this->leaderLength);
		$this->record->setLeader($leader);
		$this->leader = array(
			'length' => (int)substr($leader, 0, 5),
			'indicator_field_len' => ($this->indLength = (int)$leader[10]),
			'indicator_subfield_len' => (int)$leader[11],
			'base_adr' => (int)substr($leader, 12, 5),
			'directory_plan' => substr($leader, 20, 4),
		);
	}

	private function parseDirectory()
	{
		$dp = $this->leader['directory_plan'];
		$ba = $this->leader['base_adr'] - 1 - $this->leaderLength;

		$directory_str = substr($this->binaryRecord, $this->leaderLength, $ba);
		$directories = str_split($directory_str, $this->tagLength + $dp[0] + $dp[1]);
		foreach ($directories as $directory) {
			$this->directory[] = array(
				'tag' => substr($directory, 0, $this->tagLength),
				'len' => substr($directory, $this->tagLength, $dp[0]),
				'adr' => substr($directory, $this->tagLength + $dp[0]),
			);
		}
	}

	private function parseData()
	{
		foreach ($this->directory as $dir) {
			$pos = $this->leader['base_adr'] + $dir['adr'];
			$len = $dir['len'] - strlen($this->fieldSeparator);
			$this->parseField($dir['tag'], substr($this->binaryRecord, $pos, $len));
		}
	}

	/**
	 * @param int $tag
	 * @param string $data
	 */
	private function parseField($tag, $data)
	{
		if ($this->isControlField($tag)) {
			$this->parseControlField($tag, $data);
		} elseif ($this->isLinkedField($data)) {
			$this->parseLinkedField($tag, $data);
		} else {
			$this->parseDataField($tag, $data);
		}
	}

	/**
	 * @param int $tag
	 * @param string $data
	 */
	private function parseControlField($tag, $data)
	{
		$this->record->addField(Field::getInstance($tag)->setValue($data)->setControlField());
	}

	/**
	 * @param int $tag
	 * @param string $data
	 */
	private function parseLinkedField($tag, $data)
	{
		$fields_str = substr($data, $this->indLength + strlen($this->linkedSeparator));
		$inds = substr($data, 0, $this->indLength);

		foreach (explode($this->linkedSeparator, $fields_str) as $field_str) {
			$sub_tag = (int)substr($field_str, 0, $this->tagLength);
			$sub_inds = substr($field_str, $this->tagLength, $this->indLength);

			$subfield = Field::getInstance($sub_tag)->setInds($sub_inds);

			if ($this->isControlField($sub_tag)) {
				$subfield->setValue(substr($field_str, $this->tagLength))->setControlField();
			} else {
				$subfield->addSubfields($this->parseSubfields(substr($field_str, $this->tagLength)))->setDataField();
			}
			$this->record->addField(Field::getInstance($tag)->setInds($inds)->addField($subfield)->setLinkedEntryField());
		}
	}

	/**
	 * @param int $tag
	 * @param string $data
	 */
	private function parseDataField($tag, $data)
	{
		$inds = substr($data, 0, $this->indLength);
		$this->record->addField(
			Field::getInstance($tag)->setInds($inds)->addSubfields($this->parseSubfields($data))->setDataField()
		);
	}

	/**
	 * @param string $data
	 * @throws Exception
	 * @return array
	 */
	private function parseSubfields($data)
	{
		$subs = explode($this->subfieldSeparator, substr($data, $this->indLength + 1));
		$subfields = array();
		foreach ($subs as $subfield) {
			if (!strlen($subfield)) {
				throw new Exception ('Invalid subfield near "' . $data . '"');
			}
			$subfields[] = Subfield::getInstance($subfield[0])->setValue(substr($subfield, 1));
		}
		return $subfields;
	}
}
