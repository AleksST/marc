<?php

abstract class AbstractRecordParser
{

    /**
     * @var Record
     */
    protected $record;

    /**
     * @return Record
     */
    abstract function parse($record = '');
}