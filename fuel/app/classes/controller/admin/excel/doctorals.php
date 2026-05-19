<?php
/**
 * Created by PhpStorm.
 * User: spyros
 * Date: 8/24/15
 * Time: 8:20 PM
 */

use Fuel\Core\DB;

/**
 * Class Controller_Admin_Excel_Doctorals
 * Handles the exporting of doctoral data in excel sheets.
 */
class Controller_Admin_Excel_Doctorals extends Controller_Admin_Excel_Common implements Interfaces_Excel
{

    const START_ROW = 2;
    const COLUMN_LOW = 'A';
    const COLUMN_HIGH = 'O';


    public function action_export_excel()
    {

        $doctorals = Model_Doctoral::find('all');

        parent::__construct('Doctorals');
        parent::initializeExcelExport();

        $objPHPExcel = $this->getObjPHPExcel();

        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle("Doctorals");


        /**
         * Formatting
         */
        $objPHPExcel->getActiveSheet()->getStyle('A:N')->applyFromArray($this->getStyleArrayRegular());
        $objPHPExcel->getActiveSheet()->getStyle('O')->applyFromArray($this->getStyleArraySmall());


        $objPHPExcel->getActiveSheet()->getStyle('A:O')->applyFromArray($this->getDefaultStyle());
        $objPHPExcel->getActiveSheet()->getStyle('A1:O1')->applyFromArray($this->getStyleArrayBold());

        $objPHPExcel->getActiveSheet()->getStyle('P:Q')->applyFromArray($this->getStyleArraySmall());
        $objPHPExcel->getActiveSheet()->getStyle('P1:Q1')->applyFromArray($this->getStyleArrayBold());

        /**
         * Header Line
         */
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'AM');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Surname');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Name');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Email');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Registration Date');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Telephone');

        $objPHPExcel->getActiveSheet()->setCellValue('P1', 'Created At');
        $objPHPExcel->getActiveSheet()->setCellValue('Q1', 'Updated At');

        $row = 2;

        foreach ($doctorals as $object) {

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $object->id);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $object->am);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $object->surname);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $object->name);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $object->email);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $object->registrationdate);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $this->formatTelephone($object->telephone));
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $object->graduated);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $object->sendemail);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $object->suspended);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $object->hours_remaining);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $object->hours_completed);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $object->max_assignments);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $object->bonus_weight);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $object->comment);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $this->formatDate($object->created_at));
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $row, $this->formatDate($object->updated_at));


            $row++;
        }


        foreach (range('A', 'Q') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        /**
         * Return the file.
         */
        $this->setHeadersAndDownload();

    }

    public function action_export_graduated_excel()
    {
        $doctorals = Model_Doctoral::find('all', array(
            'where' => array(
                array('graduated', '=', 1),
                array('deleted_at', '=', null),
            ),
            'order_by' => array('surname' => 'asc'),
            'related' => array('professors')
        ));

        parent::__construct('Doctorals_Graduated');
        parent::initializeExcelExport();

        $objPHPExcel = $this->getObjPHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle("Graduated");

        $sheet->getStyle('A:J')->applyFromArray($this->getDefaultStyle());
        $sheet->getStyle('A:J')->applyFromArray($this->getStyleArrayRegular());
        $sheet->getStyle('A1:J1')->applyFromArray($this->getStyleArrayBold());
        $sheet->getStyle('I:J')->applyFromArray($this->getStyleArraySmall());

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'AM');
        $sheet->setCellValue('C1', 'Surname');
        $sheet->setCellValue('D1', 'Name');
        $sheet->setCellValue('E1', 'Professors');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'Registration Date');
        $sheet->setCellValue('H1', 'Telephone');
        $sheet->setCellValue('I1', 'Created At');
        $sheet->setCellValue('J1', 'Updated At');

        $row = 2;

        foreach ($doctorals as $object) {

            $professors = '-';
            if (!empty($object->professors)) {
                $names = array_map(function($p) {
                    return $p->surname . ' ' . $p->name;
                }, $object->professors);

                $professors = implode(', ', $names);
            }

            $sheet->setCellValue('A' . $row, $object->id);
            $sheet->setCellValue('B' . $row, $object->am);
            $sheet->setCellValue('C' . $row, $object->surname);
            $sheet->setCellValue('D' . $row, $object->name);
            $sheet->setCellValue('E' . $row, $professors);
            $sheet->setCellValue('F' . $row, $object->email);
            $sheet->setCellValue('G' . $row, $object->registrationdate);
            $sheet->setCellValue('H' . $row, $this->formatTelephone($object->telephone));
            $sheet->setCellValue('I' . $row, $this->formatDate($object->created_at));
            $sheet->setCellValue('J' . $row, $this->formatDate($object->updated_at));

            $row++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->setHeadersAndDownload();
    }

    public function action_export_deleted_excel()
    {
        $doctorals = Model_Doctoral::find('all', array(
            'where' => array(
                array('deleted_at', '!=', null),
            ),
            'order_by' => array('surname' => 'asc'),
            'related' => array('professors')
        ));

        parent::__construct('Doctorals_Deleted');
        parent::initializeExcelExport();

        $objPHPExcel = $this->getObjPHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle("Deleted");

        $sheet->getStyle('A:J')->applyFromArray($this->getDefaultStyle());
        $sheet->getStyle('A:J')->applyFromArray($this->getStyleArrayRegular());
        $sheet->getStyle('A1:J1')->applyFromArray($this->getStyleArrayBold());
        $sheet->getStyle('I:J')->applyFromArray($this->getStyleArraySmall());

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'AM');
        $sheet->setCellValue('C1', 'Surname');
        $sheet->setCellValue('D1', 'Name');
        $sheet->setCellValue('E1', 'Professors');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'Registration Date');
        $sheet->setCellValue('H1', 'Telephone');
        $sheet->setCellValue('I1', 'Created At');
        $sheet->setCellValue('J1', 'Updated At');

        $row = 2;

        foreach ($doctorals as $object) {

            $professors = '-';
            if (!empty($object->professors)) {
                $names = array_map(function($p) {
                    return $p->surname . ' ' . $p->name;
                }, $object->professors);

                $professors = implode(', ', $names);
            }

            $sheet->setCellValue('A' . $row, $object->id);
            $sheet->setCellValue('B' . $row, $object->am);
            $sheet->setCellValue('C' . $row, $object->surname);
            $sheet->setCellValue('D' . $row, $object->name);
            $sheet->setCellValue('E' . $row, $professors);
            $sheet->setCellValue('F' . $row, $object->email);
            $sheet->setCellValue('G' . $row, $object->registrationdate);
            $sheet->setCellValue('H' . $row, $this->formatTelephone($object->telephone));
            $sheet->setCellValue('I' . $row, $this->formatDate($object->created_at));
            $sheet->setCellValue('J' . $row, $this->formatDate($object->updated_at));

            $row++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

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
            $query = DB::query('SELECT id FROM doctorals');
            $dbIdsArray = $query->execute()->as_array('id');
            $dbIdsArray = array_values(array_map(array(__CLASS__,'filterId'),$dbIdsArray));

            /**
             * Find the ids to delete from the db (those that do not exist in the excel).
             * Delete them.
             */
            $dbIdsTodelete = array_diff($dbIdsArray,$excelIdsArray);
            if(count($dbIdsTodelete) > 0){

                $dbIdsTodelete = implode(', ',$dbIdsTodelete);

                //binding a parameter was not working because it added quotes
                DB::query("UPDATE doctorals SET deleted_at = datetime('now') WHERE id IN ( $dbIdsTodelete ) ")->execute();
               }
            /**
             * Create new or update.
             *
             */
            foreach($excelAllRowsArray as $rowNumber => $rowData){

                $props = array(
                    'am' => $rowData['B'],
                    'surname' => $rowData['C'],
                    'name' => $rowData['D'],
                    'email' => $rowData['E'],
                    'registrationdate' => $rowData['F'],
                    'telephone' => parent::unformatTelephone($rowData['G']),
                    'graduated' => $rowData['H'],
                    'sendemail' => $rowData['I'],
                    'suspended' => $rowData['J'],
                    'hours_remaining' => $rowData['K'],
                    'hours_completed' => $rowData['L'],
                    'max_assignments' => $rowData['M'],
                    'bonus_weight' => $rowData['N'],
                    'comment' => $rowData['O']
                );

                if(in_array($rowData['A'],$dbIdsArray)){
                    //update the existing and preserve their id.
                    $objectToUpdate = Model_Doctoral::find($rowData['A']);

                    //If you find nothing, save the day and create a new one.
                    if(!is_null($objectToUpdate)){
                        $objectToUpdate->set($props);
                        $objectToUpdate->save();
                    }else{
                        //create new
                        $objectToCreate = new Model_Doctoral($props);
                        $objectToCreate->save();
                    }

                }else{
                    //create new
                    $objectToCreate = new Model_Doctoral($props);
                    $objectToCreate->save();
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

    public function action_export_not_responded_excel()
    {
        if (!Auth::has_access('doctorals.read')) {
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        $doctorals = Model_Doctoral::find('all', array(
            'where' => array(
                array('deleted_at', '=', null),
                array('graduated', '=', 0),
            ),
            'order_by' => array('surname' => 'asc'),
            'related' => array('emailurls', 'professors')
        ));

        $not_responded = array();
        foreach ($doctorals as $doc) {
            $last_email = null;

            if (!empty($doc->emailurls)) {
                foreach ($doc->emailurls as $emailurl) {
                    if ($emailurl->sent) {
                        if (!$last_email || $emailurl->created_at > $last_email->created_at) {
                            $last_email = $emailurl;
                        }
                    }
                }
            }

            if (!$last_email || !$last_email->used) {
                $not_responded[] = $doc;
            }
        }

        parent::__construct('Doctorals_Not_Responded');
        parent::initializeExcelExport();

        $objPHPExcel = $this->getObjPHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle("Not Responded");

        $sheet->getStyle('A:J')->applyFromArray($this->getDefaultStyle());
        $sheet->getStyle('A:J')->applyFromArray($this->getStyleArrayRegular());
        $sheet->getStyle('A1:J1')->applyFromArray($this->getStyleArrayBold());
        $sheet->getStyle('I:J')->applyFromArray($this->getStyleArraySmall());

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'AM');
        $sheet->setCellValue('C1', 'Surname');
        $sheet->setCellValue('D1', 'Name');
        $sheet->setCellValue('E1', 'Professors');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'Registration Date');
        $sheet->setCellValue('H1', 'Telephone');
        $sheet->setCellValue('I1', 'Created At');
        $sheet->setCellValue('J1', 'Updated At');

        $row = 2;

        // 4. Заполнение данных
        foreach ($not_responded as $object) {

            $professors = '-';
            if (!empty($object->professors)) {
                $names = array_map(function($p) {
                    return $p->surname . ' ' . $p->name;
                }, $object->professors);

                $professors = implode(', ', $names);
            }

            $sheet->setCellValue('A' . $row, $object->id);
            $sheet->setCellValue('B' . $row, $object->am);
            $sheet->setCellValue('C' . $row, $object->surname);
            $sheet->setCellValue('D' . $row, $object->name);
            $sheet->setCellValue('E' . $row, $professors);
            $sheet->setCellValue('F' . $row, $object->email);
            $sheet->setCellValue('G' . $row, $object->registrationdate);
            $sheet->setCellValue('H' . $row, $this->formatTelephone($object->telephone));
            $sheet->setCellValue('I' . $row, $this->formatDate($object->created_at));
            $sheet->setCellValue('J' . $row, $this->formatDate($object->updated_at));

            $row++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->setHeadersAndDownload();
    }

    public function action_export_noncompliance_excel()
    {
        if (!Auth::has_access('doctorals.read')) {
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin');
        }

        $year   = Input::get('year', null);
        $season = Input::get('season', null);

        if (!$year || !$season) {
            Session::set_flash('error', 'Year and season are required for export.');
            return Response::redirect('admin/reports/examperiod_noncompliance');
        }

        // 1. Находим нужный период
        $examperiod = Model_Examperiod::find('first', array(
            'where' => array(
                array('academic_year', $year),
                array('season', $season),
            ),
        ));

        if (!$examperiod) {
            Session::set_flash('error', 'Exam period not found.');
            return Response::redirect('admin/reports/examperiod_noncompliance');
        }

        // 2. Получаем данные отчета
        $report = Model_Report::examperiod_noncompliance((int)$examperiod->id);

        // 3. Собираем уникальные ID всех докторантов для подгрузки их дополнительных данных (АМ, телефон)
        $doctoral_ids = array();
        foreach ($report as $prof) {
            if (!empty($prof['no_response'])) {
                foreach ($prof['no_response'] as $d) $doctoral_ids[$d['doctoral_id']] = $d['doctoral_id'];
            }
            if (!empty($prof['no_show'])) {
                foreach ($prof['no_show'] as $d) $doctoral_ids[$d['doctoral_id']] = $d['doctoral_id'];
            }
        }

        if (empty($doctoral_ids)) {
            Session::set_flash('error', 'No non-compliance doctorals to export for this period.');
            return Response::redirect('admin/reports/examperiod_noncompliance?year='.$year.'&season='.$season);
        }

        // Подгружаем полные данные из БД для связи
        $doctorals_db = Model_Doctoral::find('all', array(
            'where' => array(
                array('id', 'IN', array_values($doctoral_ids))
            )
        ));
        $doc_lookup = array();
        foreach ($doctorals_db as $doc) {
            $doc_lookup[$doc->id] = $doc;
        }

        // 4. Настройки Excel
        parent::__construct('Doctorals_Noncompliance');
        parent::initializeExcelExport();

        $objPHPExcel = $this->getObjPHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle("Noncompliance");

        $sheet->getStyle('A:L')->applyFromArray($this->getDefaultStyle());
        $sheet->getStyle('A:L')->applyFromArray($this->getStyleArrayRegular());
        $sheet->getStyle('A1:L1')->applyFromArray($this->getStyleArrayBold());

        // Добавляем новые колонки для экзаменов
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'AM');
        $sheet->setCellValue('C1', 'Surname');
        $sheet->setCellValue('D1', 'Name');
        $sheet->setCellValue('E1', 'Reporting Professor');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'Telephone');
        $sheet->setCellValue('H1', 'Issue Type');
        $sheet->setCellValue('I1', 'Exam Date');
        $sheet->setCellValue('J1', 'Exam Time');
        $sheet->setCellValue('K1', 'Course Code');
        $sheet->setCellValue('L1', 'Course Title');

        $row = 2;

        // 5. Заполнение файла на основе структуры отчета
        foreach ($report as $prof) {
            $prof_name = $prof['professor_name'];

            // Выгрузка тех, кто не ответил (No Response)
            if (!empty($prof['no_response'])) {
                foreach ($prof['no_response'] as $d) {
                    $doc_id = $d['doctoral_id'];
                    $db_doc = isset($doc_lookup[$doc_id]) ? $doc_lookup[$doc_id] : null;

                    $sheet->setCellValue('A' . $row, $doc_id);
                    $sheet->setCellValue('B' . $row, $db_doc ? $db_doc->am : '-');
                    $sheet->setCellValue('C' . $row, $db_doc ? $db_doc->surname : '-');
                    $sheet->setCellValue('D' . $row, $db_doc ? $db_doc->name : $d['doctoral_name']);
                    $sheet->setCellValue('E' . $row, $prof_name);
                    $sheet->setCellValue('F' . $row, $d['doctoral_email']);
                    $sheet->setCellValue('G' . $row, $db_doc ? $this->formatTelephone($db_doc->telephone) : '-');
                    $sheet->setCellValue('H' . $row, 'No Response');
                    $sheet->setCellValue('I' . $row, '-');
                    $sheet->setCellValue('J' . $row, '-');
                    $sheet->setCellValue('K' . $row, '-');
                    $sheet->setCellValue('L' . $row, '-');
                    $row++;
                }
            }

            // Выгрузка тех, кто не пришел на экзамен (No Show)
            if (!empty($prof['no_show'])) {
                foreach ($prof['no_show'] as $d) {
                    $doc_id = $d['doctoral_id'];
                    $db_doc = isset($doc_lookup[$doc_id]) ? $doc_lookup[$doc_id] : null;

                    // Создаем отдельную строку для каждого пропущенного экзамена
                    foreach ($d['events'] as $ev) {
                        $start = substr($ev['exam_start'], 0, 5);
                        $end   = substr($ev['exam_end'], 0, 5);
                        $time  = $start . ' - ' . $end;

                        $sheet->setCellValue('A' . $row, $doc_id);
                        $sheet->setCellValue('B' . $row, $db_doc ? $db_doc->am : '-');
                        $sheet->setCellValue('C' . $row, $db_doc ? $db_doc->surname : '-');
                        $sheet->setCellValue('D' . $row, $db_doc ? $db_doc->name : $d['doctoral_name']);
                        $sheet->setCellValue('E' . $row, $prof_name);
                        $sheet->setCellValue('F' . $row, $d['doctoral_email']);
                        $sheet->setCellValue('G' . $row, $db_doc ? $this->formatTelephone($db_doc->telephone) : '-');
                        $sheet->setCellValue('H' . $row, 'No Show');
                        $sheet->setCellValue('I' . $row, $ev['exam_day']);
                        $sheet->setCellValue('J' . $row, $time);
                        $sheet->setCellValue('K' . $row, $ev['course_code']);
                        $sheet->setCellValue('L' . $row, $ev['course_title']);
                        $row++;
                    }
                }
            }
        }

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->setHeadersAndDownload();
    }

}