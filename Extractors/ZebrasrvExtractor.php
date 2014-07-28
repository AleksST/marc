<?php

require_once 'AbstractSourceExtractor.php';

class ZebrasrvExtractor extends AbstractSourceExtractor{

    private $resource = null;

    private $OIDs = [
                'sutrs' => '1.2.840.10003.5.101',
                'grs-1' => '1.2.840.10003.5.105',
                'html'  => '1.2.840.10003.5.109.3',
                'xml'   => '1.2.840.10003.5.109.10',
                'rtf'   => '1.2.840.10003.5.1000.155.1',
                'usmarc'  => '1.2.840.10003.5.10',
                'rusmarc' => '1.2.840.10003.5.28',
                'unimarc' => '1.2.840.10003.5.1',
            ];

    private $yazOutputTypes = [
        'string', 'array', 'xml', 'raw'
    ];

    private $responseFormat = 'rusmarc';
    private $charset = 'utf-8';

    public function getRecords() {
        ;
    }

    /**
     * @return bool|Record
     */
    public function getNextRecord() {

        if ($this->recordsRead >= $this->recordsLimit && $this->recordsLimit !== 0) {
            return false;
        }

        if ($record_raw = $this->getNextRaw()) {
            $this->recordsRead++;
            return $this->recordParser->parse($record_raw);
        }

        return false;
    }

    public function setSource($source = array(), $conditions = array()) {

        // todo: check host, port, database, charset, syntax;
        $this->responseFormat = (array_key_exists($source['syntax'], $this->OIDs))
                                ? $source['syntax'] : 'rusmarc';
        $this->charset = $source['charset'];
        $zurl = $source['host'] . ':' . $source['port'] . '/' . $source['database'];

        $this->connect($zurl);
        $this->search($conditions);
        return $this;
    }

    private function connect($zurl) {

        if ( !$this->resource = yaz_connect($zurl, ['charset'=> $this->charset]) ) {
            throw new Exception('Cann\'t connect to yaz server: ' .$zurl);
        }

        return true;
    }

    public function search($conditions) {

        if (!is_resource($this->resource)) {
            throw new Exeption('Cann\'t use search before connect to yaz seerver');
        }

        yaz_syntax($this->resource, $this->OIDs[$this->responseFormat]);
        yaz_search($this->resource, 'rpn', $this->createQuery($conditions));
        yaz_wait();

        return yaz_hits($this->resource);
    }

    /**
     * $rpn - rpn-format query, example '@attr 1=1035 "terms to search"'
     * @return string $rpn
     */
    private function createQuery($conditions = '') {
        return iconv('UTF-8', $this->charset, '@attr 1=1035 история');
    }

    /**
     *
     * @param string $type - possible output formst
     * @return type
     */
    private function getNextRaw() {

        /**
         * @todo: set type according response format.
         *        for marc formats use "raw"
         */
        $type = 'raw';
        return yaz_record($this->resource, 1 + $this->recordsRead, $type);
    }
}
