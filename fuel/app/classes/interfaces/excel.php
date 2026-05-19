<?php
/**
 * Created by PhpStorm.
 * User: spyros
 * Date: 12/28/15
 * Time: 4:58 AM
 */

interface Interfaces_Excel
{
    public function action_export_excel();
    public static function importExcel($inputFile);
}