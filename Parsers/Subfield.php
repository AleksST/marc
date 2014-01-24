<?php

class Subfield {

    private $code;
    private $tag;
    private $value;
    private $name;
    private $isRepeatable;

    private $errors;
    private $info;

    private $field = null;

    /**
     * @return Subfield
     */
    public static function getInstance($code = 'a', $tag = null){
        $instance = new self;
        return $instance->setTag($tag)->setCode($code);
    }

    public function setField(Field &$field = null){
        $this->field = $field;
    }

    /**
     * @return Field
     */
    public function getField(){
        return $this->field;
    }

    /**
     * @return Subfield
     */
    public function setTag($tag) {
        $this->tag = $tag;
        return $this;
    }

    public function getTag(){
        return $this->tag;
    }

    /**
     * @return Subfield
     */
    public function setCode($code){
        $this->code = $code;
        return $this;
    }

    public function getCode(){
        return $this->code;
    }

    /**
     * @return Subfield
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getName(){
        return $this->name;
    }

    /**
     * @return Subfield
     */
    public function setValue($value){
        $this->value = $value;
        return $this;
    }

    public function getValue(){
        return $this->value;
    }

    /**
     * @return Subfield
     */
    public function setIsRepeatable($isRepeatable){
        $this->isRepeatable = (bool) $isRepeatable;
        return $this;
    }

    public function isRepeatable(){
        return $this->isRepeatable;
    }

    public function setError($msg){
        $this->errors[] = $msg;
    }

    public function getErrors(){
        return $this->errors;
    }

    public function setInfo($msg){
        $this->info[] = $msg;
    }

    public function getInfo(){
        return $this->info;
    }

}