<?php

class Subfield
{

    private $code;
    private $tag = 999;
    private $value;
    private $name;
    private $isRepeatable = false;

    private $field = null;

	/**
	 * @param string $code
	 * @param string $tag
	 * @return Subfield
	 */
    public static function getInstance($code = 'a', $tag = null) {
        $instance = new self;
        return $instance->setTag($tag)->setCode($code);
    }

	/**
	 * @param Field $field
	 */
	public function setField(Field &$field = null) {
        $this->field = $field;
    }

    /**
     * @return Field
     */
    public function getField() {
        return $this->field;
    }

	/**
	 * @param int $tag
	 * @return self
	 */
    public function setTag($tag) {
        $this->tag = (int)$tag;
        return $this;
    }

	/**
	 * @return int
	 */
	public function getTag() {
        return $this->tag;
    }

	/**
	 * @param string $code
	 * @throws Exception
	 * @return self
	 */
    public function setCode($code) {
	    if (1 !== strlen($code)) {
		    throw new Exception ('Invalid code length');
	    }
        $this->code = $code;
        return $this;
    }

	/**
	 * @return string
	 */
	public function getCode() {
        return $this->code;
    }

	/**
	 * @param string $name
	 * @return self
	 */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

	/**
	 * @return string
	 */
	public function getName() {
        return $this->name;
    }

	/**
	 * @param mixed $value
	 * @return self
	 */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getValue() {
        return $this->value;
    }

	/**
	 * @param bool $isRepeatable
	 * @return self
	 */
    public function setIsRepeatable($isRepeatable = true) {
        $this->isRepeatable = (bool)$isRepeatable;
        return $this;
    }

	/**
	 * @return bool
	 */
	public function isRepeatable() {
        return $this->isRepeatable;
    }

}
