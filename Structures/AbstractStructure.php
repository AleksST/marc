<?php
abstract class AbstractStructure {

    abstract function isFieldRepeatable($tag);

    abstract function isLinkedField($tag);

    public function isControlField($tag) {
        return ($tag < 10);
    }

    public function isDataField($tag){
        return !($this->isControlfield($tag) || $this->isLinkedField($tag));
    }

    abstract function setValidator(/*AbstractValidator $validator*/);
}
