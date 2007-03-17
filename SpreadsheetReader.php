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
                    if (($attrs = $col->attributes()) and isset($attrs['number'])) {
                        $number = $attrs['number'];
                        while ($number > $indexOfCol)
                            $results[$indexOfSheet][$indexOfRow][$indexOfCol++] = '';
                        // attribute['number'] is the column number of cell.
                        // For save space, it might ignore empty cells.
                        // example: values of column 2nd and 3rd are empty.
                        //   <col number="0">4</col>
                        //   <col number="3">Dman</col>
                        // Therefore we need put those empty cells back according to attribute['number'].
                    }
                    $results[$indexOfSheet][$indexOfRow][$indexOfCol++] = trim((string)$col);
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

//$reader = new SpreadsheetReader;
//$sheets = $reader->read('Excel/jxl_test.xml');
?>
