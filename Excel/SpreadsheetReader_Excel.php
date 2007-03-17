<?php
//require_once '../SpreadsheetReader.php';
class SpreadsheetReader_Excel extends SpreadsheetReader {
    private static $jxlCommand = FALSE;

    /**
     * Constructor
     *
     * @param $path     $path['java'] - Path of java
     *                  $path['jxl'] - Path of jxl.jar
     *
     * @access public
     */
    public function __construct($path = FALSE) {
        if ( !self::$jxlCommand ) {
            $javaPath = (isset($path['java'])
                ? $path['java']
                : 'java'
            );
            $jxlPath = (isset($path['jxl'])
                ? $path['jxl']
                : dirname(__FILE__) . '/jxl.jar'
            );
            self::$jxlCommand = $javaPath . ' -jar "' . $jxlPath . '" -xml';
        }
    }
    
    /**
     * $sheets = read('~/example.xls');
     * $sheet = 0;
     * $row = 0;
     * $column = 0;
     * echo $sheets[$sheet][$row][$column];
     *
     * @param $xlsFilePath  File path of Excel sheet file.
     * @param $returnType   Type of return value.
     *                      'array':  Array. This is default.
     *                      'string': XML string.
     * @return FALSE or an array contains sheets.
     */
    public function &read($xlsFilePath, $returnType = 'array') {
        $ReturnFalse = FALSE;

        if ( !is_readable($xlsFilePath) ) {
            return $ReturnFalse;
        }

        @exec(self::$jxlCommand . ' "' . $xlsFilePath . '"', $output);
        if ($output[0] != '<?xml version="1.0" ?>') {
            return $ReturnFalse;
        }

        $xmlString = implode('', $output);
        if ($returnType == 'string') {
            return $xmlString;
        }
        return $this->_toArray($xmlString);
    }
}
?>
