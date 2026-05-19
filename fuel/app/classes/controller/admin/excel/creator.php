<?php
/**
 * Created by PhpStorm.
 * User: spyros
 * Date: 8/22/15
 * Time: 0:05 AM
 */
use Fuel\Core\Input;

class Controller_Admin_Excel_Creator extends Controller_Admin{

    private $examperiod_id;
    private $courses;
    private $doctorals;
    private $examcourses;
    private $examdays;
    private $examhours;
    private $examperiod;
    private $numberOfSupervisorsColumnName;


    public function action_create(){


        $this->fetch_data();

        $styleArrayRegular = array(
            'font'  => array(
                'size'  => 10,
                'name'  => 'Arial'
            ));

        $styleArrayBold = array(
            'font'  => array(
                'bold' => true,
                'size'  => 10,
                'name'  => 'Arial'
            ));

        $styleArraySmall = array(
            'font'  => array(
                'size'  => 7,
                'name'  => 'Arial'
            ));


        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator("Scheduler")
            ->setLastModifiedBy("Scheduler")
            ->setTitle("Assignments ".$this->examperiod->comment)
            ->setSubject("Auto Assignments")
            ->setDescription("Assignments generated using scheduler application")
            ->setKeywords("assignments di uoa gr")
            ->setCategory("Assignments");

        /***************************************************************************************************************/
        /*                                                Sheet 1                                                      */
        /***************************************************************************************************************/
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle("Assignments");

        //header
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Course');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Course id');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Course Date');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Course Time');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Total');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Filled');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Empty');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Assigned Doctorals');

        $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        foreach(range('B','G') as $columnID) {
            $objPHPExcel->getActiveSheet()->getStyle($columnID)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }



        $objPHPExcel->getActiveSheet()->getStyle('A:I')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A:I')->applyFromArray($styleArrayRegular);
        $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleArrayBold);


        $row = 2;



        foreach($this->examcourses as $ex){
            $assignments_array = array_filter(explode(',',$ex->assignments));
            $filled_spots = count($assignments_array);
            $empty_spots = ( int ) ( (int) $this->courses[$ex->course_id][$this->numberOfSupervisorsColumnName] - ( int ) $filled_spots );
            $doctorals_string = '';
            foreach($assignments_array as $d_id){
                $doctorals_string.= $this->doctorals[$d_id]['fullname'].', ';
                $this->doctorals[$d_id]['assigned_spots'] += 1;
            }
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $this->courses[$ex->course_id]['title']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $this->courses[$ex->course_id]['code']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $this->examdays[$ex->examday_id]);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $this->examhours[$ex->examhour_id]);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $this->courses[$ex->course_id][$this->numberOfSupervisorsColumnName]);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $filled_spots);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $empty_spots);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $doctorals_string);
            $row++;
        }
        foreach(range('A','H') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        /***************************************************************************************************************/
        /*                                                Sheet 2                                                      */
        /***************************************************************************************************************/
        $row = 1;


        $sheet2 = $objPHPExcel->createSheet();
        $sheet2->setTitle("Statistics");
        $objPHPExcel->setActiveSheetIndex(1);


        //header
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, 'Doctoral');
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, 'Current Schedule Assigned Spots');
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, 'Current Schedule Unassigned Spots');
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, 'Total Available Spots');
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, 'Global 3Hours Remaining');
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, 'Global 3Hours Before run');

        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        foreach(range('B','F') as $columnID) {
            $objPHPExcel->getActiveSheet()->getStyle($columnID)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }

        $objPHPExcel->getActiveSheet()->getStyle('A:F')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A:I')->applyFromArray($styleArrayRegular);
        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArrayBold);

        $row+=1;

        foreach($this->doctorals as $doc){

            $unassigned_spots = (int) ((int)$doc['total_spots'] - (int)$doc['assigned_spots']);
            $glob_hours_after_run = (int)( (int)$doc['hours_remaining'] - 3 * (int)$doc['assigned_spots'] );

            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $doc['fullname']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $doc['assigned_spots']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $unassigned_spots);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $doc['total_spots']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $glob_hours_after_run);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $doc['hours_remaining']);

            $row++;
        }

        foreach(range('A','F') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }


        /***************************************************************************************************************/
        /*                                                Sheet 3                                                      */
        /***************************************************************************************************************/
        $row = 1;


        $sheet2 = $objPHPExcel->createSheet();
        $sheet2->setTitle("Emails");
        $objPHPExcel->setActiveSheetIndex(2);


        //header
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, 'Doctoral');
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, 'Email');


        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        foreach(range('B','F') as $columnID) {
            $objPHPExcel->getActiveSheet()->getStyle($columnID)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }

        $objPHPExcel->getActiveSheet()->getStyle('A:F')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A:I')->applyFromArray($styleArrayRegular);
        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArrayBold);

        $row+=1;

        foreach($this->doctorals as $doc){

            if((int)$doc['assigned_spots'] > 0){
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $doc['fullname']);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $doc['email']);
                $row++;
            }


        }

        foreach(range('A','F') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }


        $objPHPExcel->setActiveSheetIndex(0);



        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Anatheseis_mathimata_'. $this->examperiod->getGreekSeason($this->examperiod->season).'_'.$this->examperiod->academic_year. '.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    private function fetch_data(){

        /**
         * Get the examPeriodId from $_GET
         */
        $this->examperiod_id = ( int ) Input::get('exid');

        /**
         * Get the examPeriod
         */
        $this->examperiod = Model_Examperiod::find($this->examperiod_id);

        /**
         * Check it.
         */
        if(is_null($this->examperiod)){
            exit;
        }

        /**
         * Decide which column of courses has the number of supervisors we need.
         */
        switch($this->examperiod->season){
            case 'winter':
                $this->numberOfSupervisorsColumnName = 'number_of_supervisors_winter';
                break;
            case 'summer':
                $this->numberOfSupervisorsColumnName = 'number_of_supervisors_summer';
                break;
            case 'september':
                $this->numberOfSupervisorsColumnName = 'number_of_supervisors_september';
                break;
            default:
                exit;
        }

        /**
         * Get the courses
         */
        $coursesDB = Model_Course::find('all');
        $this->courses = array();
        foreach($coursesDB as $c){

            /**
             * Just to be safe, store the column name
             */
            $tempColumnName = $this->numberOfSupervisorsColumnName;

            $tmp = array(
                'code' => $c->code,
                'title' => $c->title,
                $this->numberOfSupervisorsColumnName => $c->$tempColumnName,
            );

            $this->courses[$c->id] = $tmp;
        }

        /**
         * Get the doctorals
         */
        $doctoralsDB = Model_Doctoral::find('all');
        $this->doctorals = array();
        foreach($doctoralsDB as $d){

            $total_available_spots = Model_Preferencesavailable::query()
                ->where('examperiod_id', '=', $this->examperiod_id)
                ->and_where_open()
                ->where('doctoral_id', '=', $d->id)
                ->and_where_close()
                ->count();

            $tmp = array(
                'fullname' => $d->name. " ".$d->surname,
                'email' => $d->email,
                'max_assignments' => $d->max_assignments,
                'hours_remaining' => $d->hours_remaining,
                'hours_completed' => $d->hours_completed,
                'assigned_spots' => 0,
                'total_spots' => $total_available_spots
            );
            $this->doctorals[$d->id] = $tmp;
        }

        /**
         * Get the examCourses
         */
        $this->examcourses = Model_Examcourse::query()
            ->select('examday_id', 'examhour_id', 'course_id', 'assignments')
            ->where('examperiod_id', '=', $this->examperiod_id)
            ->order_by('examday_id','asc')
            ->order_by('examhour_id','asc')
            ->get();

        /**
         * Get examDays and examHours
         */
        $examdaysDB = Model_Examday::query()
            ->select('id','day')
            ->where('examperiod_id', '=', $this->examperiod_id)
            ->order_by('day','asc')
            ->get();

        $this->examdays = array();
        foreach($examdaysDB as $ed){
            $this->examdays[$ed->id] = $ed->day;
        }

        $examhoursDB = Model_Examhour::query()
            ->select('id','start', 'end')
            ->where('examperiod_id', '=', $this->examperiod_id)
            ->order_by('start','asc')
            ->get();

        $this->examhours = array();
        foreach($examhoursDB as $eh){
            $this->examhours[$eh->id] = $eh->start." - ".$eh->end;
        }



    }
}