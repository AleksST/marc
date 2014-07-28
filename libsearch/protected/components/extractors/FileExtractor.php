<?php
require_once 'AbstractSourceExtractor.php';

class FileExtractor extends AbstractSourceExtractor{

    private $recordsSeparator = "\x1D";
    private $maxBitesReadFromFile = 4096;
    private $maxRecordSize = 99999;

    private $prefix = '';
    private $tmpRecords = array();
    private $handle;

    /**
     *
     * @param string $fname
     * @return FileExtractor
     * @throws Exception
     */
    public function setSource($fname){
        if (is_file($fname) && is_readable($fname)) {
            $this->handle = fopen($fname, 'r');
            return $this;
        }

        throw new Exception("file '$fname' is not readable or exists");
    }

    /**
     * @return Record or false
     */
    public function getNextRecord() {

        if ($raw_record = $this->getNextRaw()) {
            $this->recordsRead++;
            return $this->recordParser->parse($raw_record);
        }

        return false;
    }

    private function getNextRaw(){

        if (!$raw_record = $this->loadNext()) {
            return false;
        }

        return $raw_record;
    }

    private function loadNext(){

        // check records limit
        if($this->recordsRead >= $this->recordsLimit && $this->recordsLimit !== 0){
            return false;
        }

        if(sizeof($this->tmpRecords)){
            return array_shift($this->tmpRecords);
        }

        if(!is_resource($this->handle)){
            return false;
        }

        if(!$data = fread($this->handle, $this->maxBitesReadFromFile)){
            $this->close();
            return false;
        }

        // add prefix to data from file (to glue record) and
        // get new prefix as a part of next record
        $this->tmpRecords = explode($this->recordsSeparator, $this->prefix . $data);
        $this->prefix = array_pop($this->tmpRecords);

        if(strlen($this->prefix) > $this->maxRecordSize){
            $this->close();
            throw new Exception("record #" . ($this->recordsRead+1) . " is too long");
        }

        return $this->loadNext();
    }

    private function close(){
        if(is_resource($this->handle)){
            fclose($this->handle);
        }
    }

}