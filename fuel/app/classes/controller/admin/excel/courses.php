<?php
/**
 * Created by PhpStorm.
 * User: spyros
 * Date: 8/24/15
 * Time: 8:20 PM
 */

use Fuel\Core\DB;

/**
 * Class Controller_Admin_Excel_Courses
 * Handles the exporting/importing of courses data in excel sheets.
 */
class Controller_Admin_Excel_Courses extends Controller_Admin_Excel_Common implements Interfaces_Excel{

    const START_ROW = 2;
    const COLUMN_LOW = 'A';
    const COLUMN_HIGH = 'J';

    /**
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    public function action_export_excel(){

        $courses = Model_Course::find('all');

        parent::__construct('Courses');
        parent::initializeExcelExport();

        $objPHPExcel = $this->getObjPHPExcel();

        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle("Courses");

        /**
         * Formatting
         */
        $objPHPExcel->getActiveSheet()->getStyle('A:L')->applyFromArray($this->getStyleArrayRegular());


        $objPHPExcel->getActiveSheet()->getStyle('A:L')->applyFromArray($this->getDefaultStyle());
        $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($this->getStyleArrayBold());

        $objPHPExcel->getActiveSheet()->getStyle('K:L')->applyFromArray($this->getStyleArraySmall());
        $objPHPExcel->getActiveSheet()->getStyle('K1:L1')->applyFromArray($this->getStyleArrayBold());


        /**
         * Header Line
         */
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Official Course Id');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Code');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Code2');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Title');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Supervisors Number Winter');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Supervisors Number Summer');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Supervisors Number September');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Professors');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Professor IDs');
        $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Created At');
        $objPHPExcel->getActiveSheet()->setCellValue('L1', 'Updated At');

        $row = 2;

        foreach($courses as $object){

            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $object->id);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $object->special_id);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $object->code);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$row, $object->code2, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $object->title);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $object->number_of_supervisors_winter);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $object->number_of_supervisors_summer);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $object->number_of_supervisors_september);

            $tempLine = '';
            foreach($object->professors as $professor){
                $tempLine .= $professor->name . ' ' .$professor->surname . ', ';
            }
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $tempLine);

            $tempLine = '';
            foreach($object->professors as $professor){
                $tempLine .= $professor->id  . ',';
            }
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $tempLine);


            $objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $this->formatDate($object->created_at));
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$row, $this->formatDate($object->updated_at));


            $row++;
        }


        /**
         * Autosize to make the sheet readable.
         */
        foreach(range('A','L') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        /**
         * Return the file.
         */
        $this->setHeadersAndDownload();

    }

    public static function importExcel($inputFile)
    {
        /**
         * Check file exists and is readable.
         */
        if (!file_exists($inputFile) || !is_readable($inputFile)) {
            throw new \Exception("Cannot read file: $inputFile .");
        }

        /**
         * Read Excel file.
         */
        try {

            /**
             * Identify excel file type.
             */
            $inputFileType = PHPExcel_IOFactory::identify($inputFile);

            /**
             * Create reader for this file type.
             */
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);

            /**
             * Create an Instance of our Read Filter
             */
            $filterSubset = new Controller_Admin_Excel_Filters_Generic(self::START_ROW, self::COLUMN_LOW, self::COLUMN_HIGH);

            /**
             * Tell the Reader that we want to use the Read Filter
             */
            $objReader->setReadFilter($filterSubset);

            /**
             *  Advise the Reader that we only want to load cell data
             */
            $objReader->setReadDataOnly(true);

            /**
             * Load the excel file.
             */
            $objPHPExcel = $objReader->load($inputFile);

            /**
             * Get worksheet dimensions
             */
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();

            /**
             * Get all the rows and columns in an array
             */
            $range = self::COLUMN_LOW . self::START_ROW . ':' . self::COLUMN_HIGH . $highestRow;
            $excelAllRowsArray = $sheet->rangeToArray($range, NULL, true, false, true);

            /**
             * Get only all the ids
             */
            $rangeIds = self::COLUMN_LOW . self::START_ROW . ':' . self::COLUMN_LOW . $highestRow;
            $excelIdsArray = $sheet->rangeToArray($rangeIds, NULL, false, false, false);
            $excelIdsArray = array_values(array_map(array(__CLASS__,'filterIdExcel'),$excelIdsArray));



            /**
             * Fetch all the ids from the DB. In an array holding the ids (clean array of the form []->Id)
             */
            $query = DB::query('SELECT id FROM courses');
            $dbIdsArray = $query->execute()->as_array('id');
            $dbIdsArray = array_values(array_map(array(__CLASS__,'filterId'),$dbIdsArray));

            /**
             * Find the ids to delete from the db (those that do not exist in the excel).
             * Delete them.
             */
            $dbIdsTodelete = array_diff($dbIdsArray,$excelIdsArray);
            if(count($dbIdsTodelete) > 0){

                /**
                 * Delete the associations
                 */
                foreach($dbIdsTodelete as $courseId){
                    Model_Professorcourse::query()->where('course_id', $courseId)->delete();
                }

                /**
                 * Delete the courses
                 */
                $dbIdsTodelete = implode(', ',$dbIdsTodelete);

                //binding a parameter was not working because it added quotes
                DB::query("DELETE FROM courses WHERE id IN ( $dbIdsTodelete ) ")->execute();
            }

            /**
             * Create new or update.
             */
            foreach($excelAllRowsArray as $rowNumber => $rowData){


                $props = array(
                    'special_id' => $rowData['B'],
                    'code' => $rowData['C'],
                    'code2' => strval($rowData['D']),
                    'title' => $rowData['E'],
                    'number_of_supervisors_winter' => $rowData['F'],
                    'number_of_supervisors_summer' => $rowData['G'],
                    'number_of_supervisors_september' => $rowData['H']

                );

                $object = null;

                if(in_array($rowData['A'],$dbIdsArray)){
                    //update the existing and preserve their id.
                    $object = Model_Course::find($rowData['A']);

                    //If you find nothing, save the day and create a new one.
                    if(!is_null($object)){
                        $object->set($props);
                        $object->save();
                    }else{
                        //create new
                        $object = new Model_Course($props);
                        $object->save();
                    }

                }else{
                    //create new
                    $object = new Model_Course($props);
                    $object->save();
                }

                /**
                 * Add the supervisors.
                 */
                if(!is_null($object)){

                    //Find all the old professors and delete them
                    Model_Professorcourse::query()->where('course_id',$object->id)->delete();

                    //add the new ones
                    $associatedProfessors = explode(',',$rowData['J']);
                    if (is_array($associatedProfessors)) {
                        foreach ($associatedProfessors as $professorId) {
                            $props = array('professor_id' => $professorId, 'course_id' => $object->id);
                            $new_Association = new Model_Professorcourse($props);
                            $new_Association->save();
                        }
                    }
                }

            }

        }catch (\Exception $e) {
            throw new \Exception("Error loading file: $inputFile . Database is inconsistent!! Check the excel file and try again!");
        }
    }

    public static function filterId($complexArray){
        return $complexArray['id'];
    }

    public static function filterIdExcel($complexArray){
        return $complexArray[0];
    }


}