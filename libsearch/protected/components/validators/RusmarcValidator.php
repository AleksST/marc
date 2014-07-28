<?php
include_once 'AbstractValidator.php';

class RusmarcValidator extends AbstractValidator{

    private $encodes = array(
      '89' => 'cp1251',
      '79' => 'cp866',
      '99' => 'koi-8',
      '50' => 'utf-8',
    );

    public function validateCodedSubfield(Subfield $subfield) {
        if ('100a' === $subfield->getTag().$subfield->getCode()){
            $codes = str_split( substr($subfield->getValue(), 26, 4), 2);
            foreach ($codes as $code) {
                if(array_key_exists($code, $this->encodes)){
                    $subfield->getField()->getRecord()->setEncode($this->encodes[$code]);
                }
            }
        }
    }


}