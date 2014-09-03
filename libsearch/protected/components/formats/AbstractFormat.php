<?php

abstract class AbstractFormat
{
    /**
     * @var AbstractValidator
     */
    protected $Validator;

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

    abstract public function getSubfieldName($tag, $code);

    abstract public function getIndicatorValueNames($tag, $num);

    abstract public function getIndicatorName($tag, $num);

    abstract public function getParentTags();

    abstract public function getChildTags();

    abstract public function getMandatoryFields();

    abstract public function getLeaderPositions();

    /**
     * @param AbstractValidator $validator
     */
    public function setValidator(AbstractValidator $validator) {
        $this->Validator = $validator->setFormat($this);
    }

    /**
     * @return AbstractValidator
     */
    public function getValidator()
    {
        return $this->Validator;
    }
}