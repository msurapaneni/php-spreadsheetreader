<?php
require_once 'SpreadsheetReader.php';
class SpreadsheetReaderFactory {
    private function __construct() {
        throw new Exception('Could not allocate an instance of ' . __CLASS__);
    }

    private static $classNameMap = array(
        'xls' => array(
            'name' => 'SpreadsheetReader_Excel',
            'path' => 'Excel/SpreadsheetReader_Excel'
        ),
        'ods' => array(
            'name' => 'SpreadsheetReader_OpenDocumentSheet',
            'path' => 'OpenDocumentSheet/SpreadsheetReader_OpenDocumentSheet'
        )
    );

    public static function &reader($filePath) {
        $returnFalse = FALSE;

        if (!is_readable($filePath)) {
            return $returnFalse;
        }

        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        if (isset(self::$classNameMap[$ext]['name'])) {
            $className = self::$classNameMap[$ext]['name'];
            require_once dirname(__FILE__) . '/' . self::$classNameMap[$ext]['path'] . '.php';
        }
        else {
            $className = 'SpreadsheetReader';
        }
        $sheetReader = new $className;
        return $sheetReader;
    }
}
?>
