<?php
class SpreadsheetReader {
    //MS Excel2k: <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
    protected static $excel2kNameSpace = 'urn:schemas-microsoft-com:office:spreadsheet';

    protected function &_excel2kXmlToArray(&$xml) {
        $results = array();
        $indexOfSheet = 0;
        foreach ($xml->Worksheet as $worksheet) {
            $sheet = $worksheet->Table;
            $results[$indexOfSheet] = array();
            $indexOfRow = 0;
            foreach ($sheet->Row as $row) {
                $results[$indexOfSheet][$indexOfRow] = array();
                $indexOfCol = 0;
                foreach ($row->Cell as $cell) {
                    $col = $cell->Data;
                    $cellAttrSet = $cell->attributes(self::$excel2kNameSpace);

                    if (isset($cellAttrSet['Index'])) {
                        $number = (int)$cellAttrSet['Index'] - 1;
                        while ($number > $indexOfCol)
                            $results[$indexOfSheet][$indexOfRow][$indexOfCol++] = '';
                        // attribute['Index'] is the column number of cell.
                        // For save space, it might ignore empty cells.
                        // example: values of column 2nd and 3rd are empty.
                        //   <Cell><Data>1</Data></Cell>
                        //   <Cell ss:Index="4"><Data>4</Data></Cell>
                        // Therefore we need put those empty cells back according to attribute['Index'].
                    }
                    $results[$indexOfSheet][$indexOfRow][$indexOfCol++] = trim((string)$col);
                }
                ++$indexOfRow;
            }
            ++$indexOfSheet;
        }
        return $results;
    }

    protected function &_jxlXmlToArray(&$xml) {
        $results = array();
        $indexOfSheet = 0;
        foreach ($xml->sheet as $sheet) {
            $results[$indexOfSheet] = array();
            $indexOfRow = 0;
            foreach ($sheet->row as $row) {
                $results[$indexOfSheet][$indexOfRow] = array();
                $indexOfCol = 0;
                foreach ($row->col as $col) {
                    if (isset($col['number'])) {
                        $number = (int)$col['number'];
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

    protected function &_toArray(&$xmlString) {
        if (FALSE === ($xml = simplexml_load_string($xmlString))) {
            return $ReturnFalse; //FALSE
        }

        $nameSpaces = $xml->getDocNamespaces();
        if (isset($nameSpaces[''])
            and $nameSpaces[''] == self::$excel2kNameSpace)
        {
            //XML of Excel 2K/XP
            $toArray = '_excel2kXmlToArray';
        }
        else {
            $toArray = '_jxlXmlToArray';
        }
        return $this->$toArray($xml);
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
