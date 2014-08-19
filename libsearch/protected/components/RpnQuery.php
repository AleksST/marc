<?php

/**
*  Wrapper for rpn-query genaration in user frendly form
*/
class RpnQuery
{

	const OPERATOR_AND = 'and';
	const OPERATOR_OR = 'or';
	const OPERATOR_NOT = 'not';
	const OPERATOR_PROX = 'prox';

	public $fields = [
		1 => 'author',
		4 => 'title',
		7 => 'isbn',
		8 => 'issn',
		1035 => 'anywhere',
		1018 => 'publisher',
	];

	protected $conditions = [];
	protected $operators = [];

	/*private $limit = 10;
	private $maxLimit = 100;*/

	public function __construct()
	{
		$this->operators = [
			self::OPERATOR_AND,
			self::OPERATOR_OR,
			self::OPERATOR_NOT,
			self::OPERATOR_PROX
		];
	}

	public function addCondition($condition, $type = 'and')
	{
		if (!in_array($type, $this->operators)) {
			$type = self::OPERATOR_AND;
		}

		$this->conditions[] = array('type' => $type, 'condition' => $condition);
	}

	public function combineConditions(array $conditions = array())
	{
		$string = '';
		foreach ($conditions as $condition) {
			$string = /*$condition['type'] . ' ' .*/ $condition['condition'];
		}

		return $string;
	}

	public function getTitleCondition($term = '')
	{
		return '@attr ' . '1=' .  array_search('title', $this->fields) . ' ' . $this->prepareTerm($term);
	}

	public function addTitleCondition($term = '', $type = '')
	{
		$this->addCondition( $this->getTitleCondition($term), $type );
		return $this;
	}

	public function getAuthorCondition($term = '')
	{
		return '@attr ' . '1=' .  array_search('author', $this->fields) . ' ' . $this->prepareTerm($term);
	}

	public function addAuthorCondition($term = '', $type = '')
	{
		$this->addCondition( $this->getAuthorCondition($term), $type );
		return $this;
	}

	public function addAnywhereCondition($term = '', $type = '')
	{
		$this->addCondition( $this->getAnywhereCondition($term), $type );
		return $this;
	}

	public function getAnywhereCondition($term)
	{
		return '@attr ' . '1=' .  array_search('anywhere', $this->fields) . ' ' . $this->prepareTerm($term);
	}

	private function prepareTerm($term = '')
	{
		return '"' . $term . '"';
	}

	public function __toString()
	{
		return $this->combineConditions($this->conditions);
	}
}

?>