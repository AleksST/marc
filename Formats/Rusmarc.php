<?php

class Rusmarc extends Marc {

    protected $parentTags = array(461, 462);
    protected $childTags = array(463, 464);
    protected $relativeTags = array();

    /**
     * Xpath extracts all fields
     * @var string
     */
    public $fp = '/record/fields/*/field';

    /**
     * @var SimpleXMLElement
     */
    protected $Structure;

    public function __construct($structureFileName = null) {

        if(null === $structureFileName) {
            $structureFileName = './Formats/rusmarc.xml';
        }
        $this->Structure = simplexml_load_file($structureFileName);
    }

    public function getParentTags(){
        return $this->parentTags;
    }

    public function getChildTags(){
        return $this->childTags;
    }

    private function path($path) {
        return $this->Structure->xpath($path);
    }

    public function isIndicatorExists($tag, $num, $ind){
        return $this->path($this->fp . "[@tag=$tag]/indicators/ind$num/option[@value='$ind']");
    }

    public function isFieldExists($tag) {
        return count($this->path($this->fp . "[@tag=$tag]"));
    }

    public function isSubfieldExists($tag, $code) {
        return count($this->path($this->fp . "[@tag=$tag]/subfields/subfield[@code='$code']"));
    }

    public function isFieldRepeatable($tag) {
        return count($this->path($this->fp . "[@tag=$tag][@repeatable=1]"));
    }

    public function isSubfieldRepeatable($tag, $code) {
        return count($this->path($this->fp . "[@tag=$tag]/subfields/subfield[@code='$code'][@repeatable=1]"));
    }

    public function isControlField($tag) {
        return count($this->path("/record/fields/controlfields/field[@tag=$tag]"));
    }

    public function isDataField($tag) {
        return count($this->path("/record/fields/datafields/field[@tag=$tag]"));
    }

    public function isLinkedEntryField($tag) {
        return count($this->path("/record/fields/linkedfields/field[@tag=$tag]"));
    }

    public function isValueCoded($tag, $code){
        return count($this->path($this->fp ."[@tag=$tag]/subfields/subfield[@code='$code']/position"));
    }

    public function getSubfieldPositions($tag, $code){

        $positions = array();
        $pos = $this->path($this->fp ."[@tag=$tag]/subfields/subfield[@code='$code']/position");

        foreach($pos as $position) {
			$options = array();
			foreach ($position->option as $option){
				$options[(string)$option['value']] = (string)$option['name'];
			}

            $positions[] = array(
                'start' => (int)$position['start'],
                'length' => (int)$position['length'],
                'name' => (string)$position['name'],
                'options' => $options
            );
        }

        return $positions;
    }

    public function getFieldName($tag){

        $field = current($this->path($this->fp . "[@tag=$tag]"));
        if(!is_object($field)){
            return false;
        }
        $attrs = $field->attributes();
        return (string)$attrs['name'];
    }

    public function getSubfieldName($tag, $code) {

        $subfield = current($this->path($this->fp . "[@tag=$tag]/subfields/subfield[@code='$code']"));
        if(!is_object($subfield)){
            return false;
        }
        $attrs = $subfield->attributes();
        return (string)$attrs['name'];
    }

    public function getIndicatorValueNames($tag, $num) {

        $options = $this->path($this->fp . "[@tag=$tag]/indicators/ind$num/option");
        $out = array();
        foreach ($options as $option){
            $out[(string)$option['value']] = (string)$option['name'];
        }
        return $out;
    }

    public function getIndicatorName($tag, $num) {

        $indicator = current($this->path($this->fp . "[@tag=$tag]/indicators/ind$num"));
        return (string)$indicator['name'];
    }

    public function getMandatoryFields() {

        $fields = array();
        $mandatory_fields = $this->path($this->fp . "[@mandatory=1]");

        foreach ($mandatory_fields as $mfield){
            $attrs = $mfield->attributes();
            $fields[(string)$attrs['tag']] = (string)$attrs['name'];
        }

        return $fields;
    }

    public function getLeaderPositions(){

        $positions = array();
        foreach ($this->path('/record/leader/position') as $position) {
            if($position->count()) {
                $attr = $position->attributes();

                $options = array();
                foreach ($position as $option){
                    $options[(string)$option['value']] = (string)$option['name'];
                }

                $positions[] = array(
                    'start' => (int)$attr['start'],
                    'length' => (int)$attr['length'],
                    'name' => (string)$attr['name'],
                    'options' => $options,
                );
            }
        }

        return $positions;
    }
}