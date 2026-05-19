<?php
/**
 * Created by PhpStorm.
 * User: spyros
 * Date: 12/28/15
 * Time: 6:25 AM
 */

class Controller_Admin_Excel_Filters_Generic implements PHPExcel_Reader_IReadFilter{

    private $columnLow;
    private $columnHigh;
    private $startRow;

    /**
     * Controller_Admin_Excel_Filters_Generic constructor.
     * @param $startRow
     * @param $columnLow
     * @param $columnHigh
     */
    public function __construct($startRow, $columnLow, $columnHigh)
    {
        $this->columnLow = $columnLow;
        $this->columnHigh = $columnHigh;
        $this->startRow = $startRow;
    }

    /**
     * @param String $column
     * @param Row $row
     * @param string $worksheetName
     * @return bool
     */
    public function readCell($column, $row, $worksheetName = '') {

        /**
         * Read only columns and rows as defined during construction.
         */
        if ($row >= $this->startRow ) {
            if (in_array($column,range($this->columnLow,$this->columnHigh))) {
                return true;
            }
        }

        return false;
    }
}