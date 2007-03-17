<?php
class SpreadsheetReader {
    protected function &_toArray(&$xmlString) {
        if (FALSE === ($xml = simplexml_load_string($xmlString))) {
            return $ReturnFalse; //FALSE
        }

        $results = array();
        $indexOfSheet = 0;
        foreach ($xml->sheet as $sheet) {
            $results[$indexOfSheet] = array();
            $indexOfRow = 0;
            foreach ($sheet->row as $row) {
                $results[$indexOfSheet][$indexOfRow] = array();
                $indexOfCol = 0;
                foreach ($row->col as $col) {
                    $results[$indexOfSheet][$indexOfRow][$indexOfCol] = trim((string)$col);
                    ++$indexOfCol;
                }
                ++$indexOfRow;
            }
            ++$indexOfSheet;
        }
        return $results;
    }

    public function &read($filePath, $returnType = 'array') {
        $returnFalse = FALSE;
        
        if (!is_readable($filePath)) {
            return $returnFalse;
        }
        $xmlString = file_get_contents($filePath);

        if ($returnType == 'string') {
            return $xmlString;
        }
        return $this->_toArray($xmlString);
    }
}
?>
