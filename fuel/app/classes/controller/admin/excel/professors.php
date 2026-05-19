<?php
/**
 * Created by PhpStorm.
 * User: spyros
 * Date: 8/24/15
 * Time: 8:20 PM
 */

use Fuel\Core\DB;

/**
 * Class Controller_Admin_Excel_Professors
 * Handles the exporting/importing of professor data in excel sheets.
 */
class Controller_Admin_Excel_Professors extends Controller_Admin_Excel_Common implements Interfaces_Excel{

    const START_ROW = 2;
    const COLUMN_LOW = 'A';
    const COLUMN_HIGH = 'J';

    /**
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    public function action_export_excel(){

        $professors = Model_Professor::find('all');

        parent::__construct('Professors');
        parent::initializeExcelExport();

        $objPHPExcel = $this->getObjPHPExcel();

        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle("Professors");

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
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Surname');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Name');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Email');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Telephone');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Office');

        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Doctorals');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Courses');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Doctorals ids');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Courses ids');

        $objPHPExcel->getActiveSheet()->setCellValue('K1', 'Created At');
        $objPHPExcel->getActiveSheet()->setCellValue('L1', 'Updated At');

        $row = 2;

        foreach($professors as $object){

            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $object->id);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $object->surname);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $object->name);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $object->email);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $this->formatTelephone($object->telephone));
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $object->office);

            $tempLine = '';
            $tempLineIds = '';
            foreach($object->doctorals as $doctoral){
                $tempLine .= $doctoral->surname . ' ' . $doctoral->name .', ';
                $tempLineIds .= $doctoral->id . ',';
            }
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $tempLine);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $tempLineIds);

            $tempLine = '';
            $tempLineIds = '';
            foreach($object->courses as $course){
                $tempLine .= $course->title . ', ';
                $tempLineIds .= $course->id . ',';
            }
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $tempLine);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $tempLineIds);



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
            $query = DB::query('SELECT id FROM professors');
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
                foreach($dbIdsTodelete as $doctoralId){
                    Model_Professorcourse::query()->where('professor_id', $doctoralId)->delete();
                }

                foreach($dbIdsTodelete as $doctoralId){
                    Model_Doctoralsupervisor::query()->where('professor_id', $doctoralId)->delete();
                }

                /**
                 * Delete the professors
                 */
                $dbIdsTodelete = implode(', ',$dbIdsTodelete);

                //binding a parameter was not working because it added quotes
                DB::query("DELETE FROM professors WHERE id IN ( $dbIdsTodelete ) ")->execute();
            }

            /**
             * Create new or update.
             */
            foreach($excelAllRowsArray as $rowNumber => $rowData){

                $props = array(
                    'surname' => $rowData['B'],
                    'name' => $rowData['C'],
                    'email' => $rowData['D'],
                    'telephone' => parent::unformatTelephone($rowData['E']),
                    'office' => $rowData['F']
                );

                if(in_array($rowData['A'],$dbIdsArray)){
                    //update the existing and preserve their id.
                    $object = Model_Professor::find($rowData['A']);

                    //If you find nothing, save the day and create a new one.
                    if(!is_null($object)){
                        $object->set($props);
                        $object->save();
                    }else{
                        //create new
                        $object = new Model_Professor($props);
                        $object->save();
                    }

                }else{
                    //create new
                    $object = new Model_Professor($props);
                    $object->save();
                }


                if(!is_null($object)){

                    /**
                     * Add the Courses.
                     */
                    //Find all the old professors and delete them
                    Model_Professorcourse::query()->where('professor_id',$object->id)->delete();

                    //add the new ones
                    $associatedDoctorals = explode(',',$rowData['J']);
                    if (is_array($associatedDoctorals)) {
                        foreach ($associatedDoctorals as $doctoralId) {
                            $props = array('professor_id' => $object->id, 'course_id' => $doctoralId);
                            $new_Association = new Model_Professorcourse($props);
                            $new_Association->save();
                        }
                    }

                    /**
                     * Add the Doctorals.
                     */
                    //Find all the old ones and delete them
                    Model_Doctoralsupervisor::query()->where('professor_id',$object->id)->delete();

                    //add the new ones
                    $associatedDoctorals = explode(',',$rowData['I']);
                    if (is_array($associatedDoctorals)) {
                        foreach ($associatedDoctorals as $doctoralId) {
                            $props = array('professor_id' => $object->id, 'doctoral_id' => $doctoralId);
                            $new_Association = new Model_Doctoralsupervisor($props);
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