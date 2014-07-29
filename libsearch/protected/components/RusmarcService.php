<?php

/**
 * Generate rusmarc xml structure
 * Structure short example:
 * <cod>
 * <?xml ... ?>
 * <record>
 *  <leader length="24" >
 *      <position name="record length" start="0" length="5" />
 *      <position name="directory plan" start="20" length="4" >
 *          <option name="new" value="n">
 *          <option name="delete" value="d">
 *          ......
 *      </position>
 *  </leader>
 *  <controlfields>
 *      <field tag="001" mandatory="1" repeatable="0" name="id">
 *      <field tag="005" mandatory="0" repeatable="0" name="data" >
 *       .............
 *  </controlfields>
 *  <fields>
 *      <field tag="010" name="isbn" type="string" repeatable="0" mandatory="0">
 *          <indicators>
 *              <ind1>
 *                  <option value="#" name="not defined">
 *              </ind1>
 *              <ind2 name="same name" >
 *                  <option value="0" name="test" >
 *                  ........
 *              </ind2>
 *          </indicators>
 *          <subfields>
 *              <subfield code="a" repeatable="0" mandatory="1" name="test">
 *                  <position name="test" start="1" length="4" />
 *                  <position name="test" start="1" length="4" >
 *                      <option name="test1" value="aa">
 *                      <option name="test2" value="zz">
 *                      .......
 *                  </position>
 *              </subfield>
 *          </subfields>
 *      </field>
 *  </fields>
 *  <linkedfields>
 *  </linkedfields>
 * </record>
 * </cod>
 */
class RusmarcStructureService
{

    private $leader = null;
    private $fields = null;

    public function generateFromXml($fileName = 'RUSMARC20130619.xml') {
        //$fileName = 'http://www.rusmarc.ru/soft/RUSMARC20130619.xml'
        //$fileName = 'http://www.bookmarc.pt/tvs/examples/Unimarc0.xml';

        // http://www.rba.ru/rusmarc/soft/rusmarc_slim.xsd
        //http://cyberdoc.univ-lemans.fr/PUB/CfU/Journee_UNIMARC_Lyon/archive/doc/FORMAT.html

        $rmXML = simplexml_load_file($fileName);
        $this->parseLeader($rmXML->LEADER);
        $this->parseFieldsBlock($rmXML->BLOCK);
        $prettyXml = $this->generatePrettyXml();
        return $prettyXml->asXml();
    }

    public function generatePrettyXml() {
        // sorry for this monster-function
        $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n<record></record>");
        $leader = $this->addAndGetChild($xml, 'leader');
        $leader->addAttribute('length', 24);
        foreach ($this->leader as $values) {
            $position = $this->addAndGetChild($leader, 'position');
            foreach ($values as $name => $value) {
                if (!is_array($value)) {
                    $position->addAttribute($name, $value);
                } else {
                    foreach ($value as $key => $disp) {
                        if ('' === trim($key)) {
                            continue;
                        }
                        $option = $this->addAndGetChild($position, 'option');
                        $option->addAttribute('value', trim($key));
                        $option->addAttribute('name', trim($this->formateStr($disp)));
                    }
                }
            }
        }
        // remove links
        unset($option);
        unset($position);

        $fields = $this->addAndGetChild($xml, 'fields');
        $controlFileds = $this->addAndGetChild($fields, 'controlfields');
        $dataFileds = $this->addAndGetChild($fields, 'datafields');
        $linkedFileds = $this->addAndGetChild($fields, 'linkedfields');

        foreach ($this->fields as $struct_field) {

            if ('4' === substr($struct_field['tag'], 0, 1)) {
                $field = $this->addAndGetChild($linkedFileds, 'field');
            } elseif (0 === sizeof($struct_field['indicators'])) {
                $field = $this->addAndGetChild($controlFileds, 'field');
            } else {
                $field = $this->addAndGetChild($dataFileds, 'field');
            }

            $field->addAttribute('tag', $struct_field['tag']);
            $field->addAttribute('mandatory', ($struct_field['mandatory'] == 'y') ? 1 : 0);
            $field->addAttribute('repeatable', ($struct_field['repeatable'] == 'y') ? 1 : 0);
            $field->addAttribute('name', $this->formateStr($struct_field['name']));

            if (sizeof($struct_field['indicators'])) {
                $field->addChild('indicators');
                foreach ($struct_field['indicators'] as $num => $values) {
                    $ind = $this->addAndGetChild($field->indicators, 'ind' . $num);

                    foreach ($values as $key => $value) {
                        if ('' === trim($key)) {
                            continue;
                        }

                        if (strpos($value, ' :: ')) {
                            list($name, $value) = explode(' :: ', $value);
                            $ind->addAttribute('name', $this->formateStr($name));
                        } else {
                            if (substr($value, 0, 1) !== '#') {
                                $ind->addAttribute('name', $this->formateStr($value));
                            }
                        }

                        $opt = $this->addAndGetChild($ind, 'option');
                        $opt->addAttribute('value', trim($key));
                        $opt->addAttribute('name', $this->formateStr($value));
                    }
                }
            }

            if (sizeof($struct_field['subfields'])) {
                $subfields = $this->addAndGetChild($field, 'subfields');
                foreach ($struct_field['subfields'] as $code => $subfield_struct) {
                    $subfield = $this->addAndGetChild($subfields, 'subfield');
                    $subfield->addAttribute('code', $code);
                    $subfield->addAttribute('repeatable', ($subfield_struct['repeatable'] == 'y') ? 1 : 0);
                    $subfield->addAttribute('mandatory', ($subfield_struct['mandatory'] == 'y') ? 1 : 0);

                    if (sizeof($subfield_struct['psubfield'])) {
                        $len = 0;
                        foreach ($subfield_struct['psubfield'] as $pos) {
                            $len = ($len > $pos['end'] + 1) ? $len : $pos['end'] + 1;
                            $position = $this->addAndGetChild($subfield, 'position');
                            $position->addAttribute('start', $pos['start']);
                            $position->addAttribute('length', $pos['end'] - $pos['start'] + 1);
                            $position->addAttribute('mandatory', ($pos['mandatory'] == 'y') ? 1 : 0);
                            $position->addAttribute('repeat', $pos['repetitions']);
                            $position->addAttribute('name', $this->formateStr($pos['name']));
                            if (sizeof($pos['vocabulary'])) {
                                foreach ($pos['vocabulary'] as $key => $name) {
                                    if ('' === trim($key)) {
                                        continue;
                                    }
                                    $option = $this->addAndGetChild($position, 'option');
                                    $option->addAttribute('value', trim($key));
                                    $option->addAttribute('name', $this->formateStr($name));
                                }
                            }
                        }
                        $subfield->addAttribute('length', $len);
                    }

                    $subfield->addAttribute('name', $this->formateStr($subfield_struct['name']));
                }
            }

        }

        return $xml;
    }

    private function formateStr($str) {
        return $this->mb_ucfirst(strtolower(trim($str)), 'utf-8');
    }

    function mb_ucfirst($string, $encoding) {
        return mb_strtoupper(mb_substr($string, 0, 1, $encoding), $encoding)
        . mb_substr($string, 1, mb_strlen($string, $encoding) - 1, $encoding);
    }

    /**
     * @param SimpleXMLElement $elem
     * @param string $childName
     * @return SimpleXMLElement
     */
    private function &addAndGetChild(SimpleXMLElement $elem, $childName) {
        $pos = count($elem->{$childName});
        $elem->addChild($childName);
        return $elem->{$childName}[$pos];
    }

    public function generateFromArray($array) {
        $this->leader = $array['leader'];
        $this->fields = $array['fields'];
    }

    public function getLeader() {
        return $this->leader;
    }

    public function getFields() {
        return $this->fields;
    }

    public function getStructure() {
        return array(
            'leader' => $this->leader,
            'fields' => $this->fields
        );
    }

    private function parseLeader(SimpleXMLElement $leader) {
        foreach ($leader->children() as $child) {
            $attributes = $child->attributes();
            if (($length = (int)$attributes['length']) > 0) {
                $start = (int)$attributes['start'];
                $sys_name = str_replace('-', '_', strtolower($child->getName()));
                $name = (string)$attributes['name'];
                $options = array();
                foreach ($child->children() as $elem) {
                    if (strtolower($elem->getName()) == 'option') {
                        $attrs = $elem->attributes();
                        $key = ($attrs->value == ' ') ? '#' : $attrs->value . '';
                        $options[$key] = $attrs->name . '';
                    }
                }
                $this->leader[$sys_name] = compact('start', 'length', 'options', 'name');
            }
        }
    }

    private function parseFieldsBlock(SimpleXMLElement $blocks) {
        $fields = array();
        foreach ($blocks as $block) {
            // merge arrays
            $fields += $this->parseFields($block);
            $fields += $this->getBlockp($block);
        }
        $this->fields = $fields;
    }

    private function parseFields(SimpleXMLElement $block) {
        $fields = array();
        foreach ($block->FIELD as $field) {
            $tag = (int)$field->attributes()->tag;

            foreach ($field->attributes() as $key => $value) {
                $fields[$tag][$key] = (string)$value;
            }

            $fields[$tag]['indicators'] = $this->getIndicators($field);
            $fields[$tag]['subfields'] = $this->getSubfields($field);
        }
        return $fields;
    }

    private function getIndicators(SimpleXMLElement $field) {
        $indicators = array();
        // 2 indicators possible
        foreach (array(1, 2) as $id) {
            $name = 'IND' . $id;
            if ($field->{$name}) {
                foreach ($field->{$name}->children() as $ind_child) {
                    if (strtolower($ind_child->getName()) == 'option') {
                        $val = $field->{$name}->attributes()->name . ' :: ' . $ind_child->attributes()->name;
                        $indicator = str_replace(' ', '#', $ind_child->attributes()->value);
                        $indicators[$id][$indicator] = $val;
                    }
                }
                // default value set
                if (!array_key_exists($id, $indicators) && $field->{$name}->attributes()->name) {
                    $indicators[$id]['#'] = (string)$field->{$name}->attributes()->name;
                }
            }
        }
        return $indicators;
    }

    private function getSubfields(SimpleXMLElement $field) {
        $subfields = array();
        foreach ($field->SUBFIELD as $subfield) {

            $subtag = (string)$subfield->attributes()->tag;
            foreach ($subfield->attributes() as $key => $value) {
                $subfields[$subtag][$key] = (string)$value;
            }
            $subfields[$subtag]['psubfield'] = $this->getPsubfield($subfield);
        }
        return $subfields;
    }

    private function getPsubfield(SimpleXMLElement $subfield) {
        $i = 0;
        $psubfields = null;
        foreach ($subfield->PSUBFIELD as $psubfield) {
            foreach ($psubfield->attributes() as $key => $value) {
                $psubfields[$i][$key] = (string)$value;
            }
            if ($psubfield->VOCABULARY->ITEM instanceof SimpleXMLElement) {
                foreach ($psubfield->VOCABULARY->ITEM as $item) {
                    $code = (string)$item->attributes()->code;
                    $name = (string)$item->attributes()->name;
                    $psubfields[$i]['vocabulary'][$code] = $name;
                }
            }
            $i++;
        }
        return $psubfields;
    }

    private function getBlockp(SimpleXMLElement $block) {
        $block_data = array();
        foreach ($block->BLOCKP as $blockp) {
            foreach ($blockp->FIELD as $field) {
                $tag = (int)$field->attributes()->tag;
                foreach ($field->attributes() as $key => $val) {
                    $block_data[$tag][$key] = (string)$val;
                }
            }
        }
        return $block_data;
    }

}