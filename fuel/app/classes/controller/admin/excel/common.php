<?php
/**
 * Created by PhpStorm.
 * User: spyros
 * Date: 12/28/15
 * Time: 12:02 AM
 */
use Fuel\Core\Config;
use Fuel\Core\Session;
use Fuel\Core\Upload;
use Fuel\Core\Response;
use Fuel\Core\File;
use Fuel\Core\Str;

class Controller_Admin_Excel_Common extends Controller_Admin{

    private $title;
    private $now;
    private $styleArrayRegular;
    private $styleArrayBold;
    private $styleArraySmall;
    private $defaultStyle;
    private $objPHPExcel;


    /**
     * Controller_Admin_Excel_Common constructor.
     * @param $title
     */
    public function __construct($title){

        //set timezone
        date_default_timezone_set(\Fuel\Core\Config::get('timezone'));
        $this->title = $title;


    }

    /**
     * Initialize the export operation
     */
    public function initializeExcelExport(){

        if (!Auth::has_access('file.export')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/'.strtolower($this->title));
        }


        $this->now = date("Y-m-d H_i_s");
        //Set styles
        $this->styleArrayRegular = array(
            'font'  => array(
                'size'  => 10,
                'name'  => 'Arial'
            ));

        $this->styleArrayBold = array(
            'font'  => array(
                'bold' => true,
                'size'  => 10,
                'name'  => 'Arial'
            ));

        $this->styleArraySmall = array(
            'font'  => array(
                'size'  => 7,
                'name'  => 'Arial'
            ));
        $this->defaultStyle = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        //Create Object
        $this->objPHPExcel = new PHPExcel();

        //File title, subject, description, keywords...
        $this->objPHPExcel->getProperties()->setCreator("Scheduler")
            ->setLastModifiedBy("Scheduler")
            ->setTitle($this->title . " " .$this->now)
            ->setSubject($this->title . " Dump")
            ->setDescription($this->title . " Data Export - Everything")
            ->setKeywords($this->title . " di uoa gr")
            ->setCategory($this->title);
    }

    /**
     * @return mixed
     */
    public function getStyleArrayRegular()
    {
        return $this->styleArrayRegular;
    }

    /**
     * @return mixed
     */
    public function getStyleArrayBold()
    {
        return $this->styleArrayBold;
    }

    /**
     * @return mixed
     */
    public function getStyleArraySmall()
    {
        return $this->styleArraySmall;
    }

    /**
     * @return mixed
     */
    public function getDefaultStyle()
    {
        return $this->defaultStyle;
    }

    /**
     * @return mixed
     */
    public function getObjPHPExcel()
    {
        return $this->objPHPExcel;
    }


    /**
     * @param $timestamp
     * @return bool|string
     * Return the date string or nothing
     */
    protected static function formatDate($timestamp){
        if(is_null($timestamp)){
            return "-";
        }else{
            return date('d/m/Y H:i:s',$timestamp);
        }
    }

    /**
     * @param $phoneString
     * @return mixed
     * Remove telephone formating.
     */
    protected static function unformatTelephone($phoneString){
        return preg_replace('/[ -]+/','',$phoneString);
    }

    /**
     * @param $phoneString
     * @return string
     * Return formatted telephone data.
     */
    protected static function formatTelephone($phoneString){

        //echo $phoneString.'<br>';
        /**
         * Check if we have a country code.
         */
        $countryCode = '';
        if(preg_match('/^00\d{2}/',$phoneString,$matches)){
            $countryCode = $matches[0];
        }elseif(preg_match('/^\+\d{2}/',$phoneString,$matches)){
            $countryCode = $matches[0];
        }

        //echo($countryCode."<br>");

        /**
         * If we have a country code, remove it.
         */
        if(!empty($countryCode)){
            $codeLength = strlen($countryCode);
            $phoneString = substr($phoneString,$codeLength);
        }

        /**
         * Split the remaining string in 4 groups
         */
        $string2return = $countryCode . '  ' . substr($phoneString, 0, 3) . '-' . substr($phoneString, 3, 4) . ' ' . substr($phoneString, 7);

        //echo $string2return.'<br>';
        return $string2return;

    }

    protected function setHeadersAndDownload(){

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $this->title . ' Data Dump '. $this->now . '.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * @param null $caller
     * Page is the name of the controller used when this was called.
     */
    public function action_upload($caller = null){


        if(is_null($caller)){
            $caller = '';
        }

        if (!Auth::has_access('file.import')){
            Session::set_flash('error', e('You do not have access to this function!'));
            Response::redirect('admin/'.$caller);
        }

        //Get user_id safely (old php versions fuck up with this).
        $user_id = Auth::get_user_id();
        $user_id = $user_id[1];

        /**
         * Load the upload directory.
         */
        Config::load('upload',true);
        $upload_path = Config::get('upload.upload_path');



        /**
         * Custom configuration for this upload
         */
        $config = array(
            'path' => $upload_path
        );

        /**
         * process the uploaded files in $_FILES
         */
        try{
            Upload::process($config);
        }catch(exception $e){
            Session::set_flash('error', e('File upload error. '.$e->getMessage()));
            Response::redirect('admin/'.$caller);
        }


        /**
         * if there are any valid files
         */
        if (Upload::is_valid())
        {
            /**
             * save them according to the config
             */
            Upload::save();


            $friendlyName = 'ERROR';
            $filePath = null;
            /**
             * get the list of successfully uploaded files, should be one.
             */
            foreach(Upload::get_files() as $file)
            {

                //Friendly name - the name the user will see.
                $friendlyName = \Fuel\Core\Security::xss_clean(Str::truncate($file['basename'],255)) . '.' . $file['extension'];

                //The real file path
                $filePath = $file['saved_to'].$file['saved_as'];

                // Get the properties ready for a call to a model method updating the database
                $props = array(
                    'friendly_name' => $friendlyName,
                    'file_name' => $file['saved_as'],
                    'file_path' => $filePath,
                    'used' => 1,
                    'uploaderId' => $user_id,
                    'size' => $file['size'],
                );

                /**
                 * Save the file properties in DB.
                 */
                try{
                    $uploaded_file = new Model_Upload($props);
                    $uploaded_file->save();

                }catch(exception $e){
                    Session::set_flash('error', e('File upload error. '.$e->getMessage()));
                    Response::redirect('admin/'.$caller);
                }
            }

            /**
             * Like a callBack...
             * Call the function importExcel from the caller class.
             */
            try{
                $callClass = 'Controller_Admin_Excel_'.ucfirst($caller);
                $callClass::importExcel($filePath);

                Session::set_flash('success', e('Uploaded "'.$friendlyName.'" and imported it successfully.'));
                Response::redirect('admin/'.$caller);

            }catch(exception $e){
                Session::set_flash('error', e('File import error. '.$e->getMessage()));
                Response::redirect('admin/'.$caller);
            }





        }
        else{

            /**
             * Process any errors
             */

            $message = '';

            foreach (Upload::get_errors() as $file)
            {
                // $file is an array with all file information,
                // $file['errors'] contains an array of all errors occurred
                // each array element is an an array containing 'error' and 'message'
                foreach($file['errors'] as $error){
                    $message.= $error['error'].' : '.$error['message']." ";
                }

            }
            Session::set_flash('error', e($message));
            Response::redirect('admin/'.$caller);
        }


    }


}