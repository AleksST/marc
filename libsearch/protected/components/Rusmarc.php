<?php

class Rusmarc extends Marc
{
    public function getPretty(Record $record)
    {
        return [
            'authors'   => array_merge([$record->getValueByTagCode(200, 'f')], $record->getValuesByTagCode(200, 'g')),
            'title'     => $record->getValueByTagCode(200, 'a'),
            'publisher' => $record->getValueByTagCode(210, 'a'),
            'year'      => $record->getValueByTagCode(210, 'd'),
            'series'    => $record->getValueByTagCode(215, 'a'),
            'isbn'      => $record->getValueByTagCode(10, 'a'),
        ];
    }
}