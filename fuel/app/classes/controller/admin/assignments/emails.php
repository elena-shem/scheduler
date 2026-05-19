<?php
/**
 * Created by PhpStorm.
 * User: spyros
 * Date: 8/22/15
 * Time: 5:09 PM
 */

use Fuel\Core\Input;
use Email\Email;
use Fuel\Core\Package;

class Controller_Admin_Assignments_Emails extends Controller_Admin{

    private $examperiod_id;
    private $courses;
    private $doctorals;
    private $examcourses;
    private $examdays;
    private $examhours;
    private $examperiod;

    private $subject;
    private $doctoral_array;
    private $doctoral_ids_to_send_email_to;


    public function action_send(){

        $this->examperiod_id = ( int ) Input::get('exid');
        $this->doctoral_ids_to_send_email_to = array_flip(explode(',', Input::get('doctorals')));

        if(is_null(Model_Examperiod::find($this->examperiod_id))){
            echo "No exam period with id:". $this->examperiod_id ." <br>";
            exit;
        }

        try{
            $this->fetch_data();
        }catch(\Exception $e){
            \Fuel\Core\Log::error($e->getMessage()."-- Assignments Emails");
        }
        try{
            $this->populate_doctoral_array();
        }catch(\Exception $e){
            \Fuel\Core\Log::error($e->getMessage()."-- Assignments Emails");
        }
        try{
            \Fuel\Core\Log::error("Sending emails! -- Assignments Emails");
            echo "<br><br>Sending emails! -- Assignments Emails<br>";
            $this->send_all();
            \Fuel\Core\Log::error("Emails sent! -- Assignments Emails");
            echo "Emails sent! -- Assignments Emails<br>";
        }catch(\Exception $e){
            \Fuel\Core\Log::error($e->getMessage()."-- Assignments Emails");
        }

        exit;
    }

    private function populate_doctoral_array(){

        $this->doctoral_array = array();

        foreach($this->examcourses as $ex) {
            $assignments_array = array_filter(explode(',', $ex->assignments));
            foreach ($assignments_array as $d_id) {

                // Fetch doctoral
                $doctoral = Model_Doctoral::find($d_id);
                if(is_null($doctoral)){
                    \Fuel\Core\Log::error("Doctoral with id: ".$d_id." does not have DB record. -- Assignments Emails");
                    continue;
                }

                // Check if the doctoral is selected
                if(!array_key_exists($d_id, $this->doctoral_ids_to_send_email_to)){                    
                    continue;
                }

                //First time
                if(!array_key_exists($d_id,$this->doctoral_array)){

                    $this->doctoral_array[$d_id] = array(
                        'doctoral_object' => $doctoral,
                        'assignments' => array($ex->id)
                    );
                    continue;
                }

                //After the first
                if(array_key_exists('assignments',$this->doctoral_array[$d_id])){
                    array_push($this->doctoral_array[$d_id]['assignments'],$ex->id);
                    continue;
                }

                //If all fail
                \Fuel\Core\Log::error("Doctoral with id: ".$d_id." has no object in doctoral array and no assignments key. -- Assignments Emails");

            }
        }

    }

    private function send_all(){
        Package::load('email');

        $this->subject = 'Αναθέσεις '.$this->examperiod->comment;

        foreach($this->doctoral_array as $doc){

            if(!array_key_exists('assignments',$doc) || !array_key_exists('doctoral_object',$doc)){
                \Fuel\Core\Log::error("A Doctoral with has no object in doctoral array and no assignments key. -- Assignments Emails");
                continue;
            }

            if(!(count($doc['assignments']) > 0)){
                continue;
            }else{

                try{

                    $email_content = $this->create_email_content($doc);
                    $this->send_one_email($doc,$email_content);

                }catch(\Exception $e){
                    \Fuel\Core\Log::error($e->getMessage()."-- Assignments Emails");
                }

            }

        }
    }

    private  function create_email_content($complex_array){

        if(!array_key_exists('doctoral_object',$complex_array)){
            throw new Exception('No doctoral_object!');
        }
        $doctoral = $complex_array['doctoral_object'];
        $fullname = $doctoral->name. ' ' . $doctoral->surname;
        $assignments = $complex_array['assignments'];

        $text = '<h3>'.$fullname.'</h3>';
        $text .= '<h3>Σας έχουν ανατεθεί οι παρακάτω επιτηρήσεις:</h3>';
        foreach($assignments as $ex_id){

            $temp_examcourse = Model_Examcourse::find($ex_id);
            if(is_null($temp_examcourse)){
                continue;
            }else{
                if(array_key_exists($temp_examcourse->course_id,$this->courses)){
                    $text.=
                        '<p>' .
                        $this->courses[$temp_examcourse->course_id]['title'] .
                        '<br>' .
                        $this->examdays[$temp_examcourse->examday_id] .
                        '&nbsp;&nbsp;&nbsp;&nbsp; ' .
                        $this->examhours[$temp_examcourse->examhour_id] .
                        '</p>';
                }
            }
        }
        $text.= '<hr>';
        $text.= self::stat_text();
        return $text;

    }


    private function send_one_email($complex_array, $email_content){

        $doctoral = $complex_array['doctoral_object'];
        //create the mail
        $email_to_send = Email::forge();

        //add all the doctoral's emails in an array and add his full name as value.
        $email_addresses_array = explode(",",$doctoral->email);
        foreach($email_addresses_array as $key){
            $email_addresses_array[$key] = $doctoral->name . " " . $doctoral->surname;
        }

        $email_to_send->to($email_addresses_array);

        $bcc_email = \Config::get('email.admin_bcc');
            if (!empty($bcc_email)) {
                $email_to_send->bcc($bcc_email);
            }

        $email_to_send->subject($this->subject);
        $email_to_send->html_body($email_content,true);
        $email_to_send->priority(Email::P_HIGHEST);

        //now try to send it!
        try {
            $email_to_send->send();
            try{
                //Save the email in DB
                $assignment_email = new Model_Assignments_Email();
                $assignment_email->examperiod_id = $this->examperiod->id;
                $assignment_email->doctoral_id = $doctoral->id;
                $assignment_email->content = $email_content;
                $assignment_email->title = $this->subject;
                $assignment_email->save();

            }catch (\Database_Exception $e){
                \Fuel\Core\Log::error("Could not save assignment email for doctoral with id: ".$doctoral->id. " but saved it non the less. -- Assignments Emails");
            }

            \Fuel\Core\Log::error("Sent email to " . $doctoral->name . " " . $doctoral->surname." at email(s) ". $doctoral->email ." -- Assignments Emails");
            echo "Sent email to " . $doctoral->name . " " . $doctoral->surname." at email(s) ". $doctoral->email ." -- Assignments Emails <br>";

        } catch (\EmailValidationFailedException $e) {
            \Fuel\Core\Log::error("The validation failed for email to doctoral with id: ".$doctoral->id." -- Assignments Emails");
        } catch (\EmailSendingFailedException $e) {
            \Fuel\Core\Log::error("The driver could not send the email to doctoral with id: ".$doctoral->id." -- Assignments Emails");
        } catch (\SmtpConnectionException $e) {
            \Fuel\Core\Log::error("The driver could not send the email to doctoral with id: ".$doctoral->id." -- Assignments Emails");
        }
    }


    private function fetch_data(){

        $coursesDB = Model_Course::find('all');
        $this->courses = array();
        foreach($coursesDB as $c){
            $tmp = array(
                'code' => $c->code,
                'title' => $c->title,
                'number_of_supervisors_winter' => $c->number_of_supervisors_winter,
                'number_of_supervisors_summer' => $c->number_of_supervisors_summer,
                'number_of_supervisors_september' => $c->number_of_supervisors_september,
            );
            $this->courses[$c->id] = $tmp;
        }

        $this->examcourses = Model_Examcourse::query()
            ->select('id','examday_id', 'examhour_id', 'course_id', 'assignments')
            ->where('examperiod_id', '=', $this->examperiod_id)
            ->order_by('examday_id','asc')
            ->order_by('examhour_id','asc')
            ->get();

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


        $this->examperiod = Model_Examperiod::find($this->examperiod_id);
    }

    private static function stat_text(){
        $stat_text = <<<LABEL

<hr>
<p>Οποιαδήποτε διευκρίνιση θα γίνει μέσω επικοινωνίας στο προσωπικό μου email: mailto:paskalis@di.uoa.gr και όχι με reply στο παρόν.</p>

<p>Ευχαριστώ για τη συνεργασία,</p>

<p>Σαράντης Πασκαλής</p>

LABEL;

        return $stat_text;
    }


}
