<?php

abstract class AbstractValidator
{
    /**
     * @var AbstractFormat
     */
    protected $Format;

    /**
     * @var Record
     */
    protected $record;

    abstract function validateCodedSubfield(Subfield $subfield);

    public function setFormat(AbstractFormat $format) {
        $this->Format = $format;
        return $this;
    }

    public function validate(Record $record) {
        $this->validateLeader($record);
        $this->validateData($record);
        $this->validateMandatoryFields($record);
    }

    protected function validateMandatoryFields(Record $record) {
        $mandatory_fields = $this->Format->getMandatoryFields();

        foreach ($mandatory_fields as $tag => $name) {
            if (!$record->isFieldExists($tag)) {
                $record->setError(sprintf("Обязательное поле '%3d' (%s) отсутствует.", $tag, $name));
            }
        }
    }

    protected function validateLeader(Record $record) {

        $leader = $record->getLeader();

        foreach ($this->Format->getLeaderPositions() as $position) {

            $leader_val = substr($leader, $position['start'], $position['length']);

            $msg = $position['name'] . " '$leader_val' (" . $position['start'] . '-'
                . ($position['start'] + $position['length'] - 1) . ") ";

            if (array_key_exists($leader_val, $position['options'])) {
                if (count($position['options']) > 1) {
                    $record->setInfo($msg . ": " . $position['options'][$leader_val]);
                }
            } else {
                $record->setError($msg . "не соответствует формату." .
                    ' Возможные значения: ' . implode(', ', array_keys($position['options'])) . '.');
            }
        }
    }

    protected function validateData(Record $record) {

        foreach ($record->getFields() as $tag => $fields) {
            if (count($fields) > 1 && !$this->Format->isFieldRepeatable($tag)) {
                $record->setError(sprintf("Поле '%3d' не должно повторяться.", $tag));
            }

            foreach ($fields as $field) {
                $this->validateField($field);
            }
        }
    }

    protected function validateControlField(Field $field) {

        // TODO: add other field validation
        if ($field->hasParent() && $field->getTag() < 10) {
            return $this->validateLinkedControlField($field);
        }
    }

    protected function validateDataField(Field $field) {

        $tag = (int)$field->getTag();
        $this->validateIndicators($field);

        // TODO: validate mandatory subfields

        foreach ($field->getSubfields() as $code => $subfields) {
            if (count($subfields) > 1 && !$this->Format->isSubfieldRepeatable($tag, $code)) {
                $field->setError(sprintf("Подполе '%s' поля '%3d' не должно повторяться.", $code, $tag));
            }

            foreach ($subfields as $subfield) {
                $this->validateSubfield($subfield);
            }
        }
    }

    protected function validateField(Field $field) {

        $tag = (int)$field->getTag();
        $field->setName($this->Format->getFieldName($tag));

        if ($this->Format->isDataField($tag)) {
            $this->validateDataField($field);

        } elseif ($this->Format->isControlField($tag)) {
            $this->validateControlField($field);

        } elseif ($this->Format->isLinkedEntryField($tag)) {
            $this->validateLinkedEntryField($field);

        } elseif (!$this->Format->isFieldExists($tag)) {
            $field->setInfo(sprintf("Поле '%3d' отсутствует в стандарте.", $tag));
            $this->validateDataField($field);
        }
    }

    protected function validateIndicators(Field $field) {

        $tag = $field->getTag();
        foreach (array(1, 2) as $num) {
            $indicator = $field->getIndicator($num);
            $indValues = $this->Format->getIndicatorValueNames($tag, $num);
            if (!$this->Format->isIndicatorExists($tag, $num, $indicator)) {
                $field->setError(
                    sprintf("Индикатор '%s' поля '%3d' не соответствует стандарту. Возможные значения: %s.",
                        str_replace(' ', '#', $indicator), $tag, str_replace("' '", "'#'", "'" . implode("', '", array_keys($indValues)) . "'")));
            } elseif (' ' !== $indicator) {
                $field->setInfo(
                    sprintf("Индикатор '%s' поля '%3d'. %s.",
                        str_replace(' ', '#', $indicator), $tag,
                        $this->Format->getIndicatorName($tag, $num) . ": " . $indValues[$indicator]));
            }
        }
    }

    protected function validateSubfield(Subfield $subfield) {

        $tag = $subfield->getTag();
        $code = $subfield->getCode();

        $subfield->setName($this->Format->getSubfieldName($tag, $code));

        if (!$this->Format->isSubfieldExists($tag, $code)) {
            $subfield->setInfo(sprintf("Подполе '%s' поля '%3d' не соответствует стандарту.", $tag, $code));
        }

        $this->validateValue($subfield);
    }

    protected function validateValue(Subfield $subfield) {

        if (!$this->Format->isValueCoded($subfield->getTag(), $subfield->getCode())) {
            return;
        }

        $this->validateCodedSubfield($subfield);
        $positions = $this->Format->getSubfieldPositions($subfield->getTag(), $subfield->getCode());

        foreach ($positions as $position) {
            $options = $position['options'];

            if (count($options)) {
                $values = str_split(
                    substr($subfield->getValue(), $position['start'], $position['length']),
                    strlen(current(array_keys($options)))
                );
                foreach ($values as $value) {
                    if (array_key_exists($value, $options)) {
                        $subfield->setInfo($position['name'] . ': ' . $options[$value]);
                    } elseif (!in_array($value, array(' ', '|', '#'))) {
                        $subfield->setError(sprintf(
                            "Невернле значение '%s' поля '%3d' подполя '%s' на позициях '%s'.",
                            $value, $subfield->getTag(), $subfield->getCode(),
                            $position['start'] . '-' . ($position['start'] + $position['length'] - 1)
                        ));
                    }
                }
            }
        }
    }

    protected function validateLinkedEntryField(Field $mainfield) {

        foreach ($mainfield->getFields() as $fields) {
            foreach ($fields as $field) {
                $this->validateField($field);
            }
        }
    }

    protected function validateLinkedControlField(Field $field) {

        if (1 === $field->getTag()) {
            if (in_array($field->getParentTag(), $this->Format->getParentTags())) {
                $field->getRecord()->setParentId($field->getValue());
            } elseif (in_array($field->getParentTag(), $this->Format->getChildTags())) {
                $field->getRecord()->setChildId($field->getValue());
            } else {
                $field->getRecord()->setRelevantId($field->getValue());
            }
        }
    }

}